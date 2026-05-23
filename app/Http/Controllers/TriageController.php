<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Patient;

class TriageController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $patientMessage = $request->message;
        $patient = $request->user();

        // 1. Détection des mots-clés d'urgence absolue (Kill Switch)
        $emergencyKeywords = ['suicide', 'mourir', 'tuer', 'saigne', 'sang', 'viol', 'agression', 'overdose'];
        $isEmergency = false;
        foreach ($emergencyKeywords as $keyword) {
            if (stripos($patientMessage, $keyword) !== false) {
                $isEmergency = true;
                break;
            }
        }

        if ($isEmergency) {
            return response()->json([
                'reply' => "⚠️ C'est une urgence. Ne restez pas seul(e). Appelez immédiatement le SAMU au 15 ou le numéro d'écoute au 115. Nous sommes là pour vous aider.",
                'is_emergency' => true,
                'escalation_level' => 4
            ]);
        }

        // 2. Appel à Gemini pour le triage médical (RAG MVP)
        $apiKey = env('GEMINI_API_KEY');
        $geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        // Prompt de base pour forcer le comportement médical progressif
        $systemPrompt = <<<PROMPT
Tu es SUTURA-MED, un assistant santé pour les jeunes sénégalais (15-30 ans).

STYLE DE RÉPONSE :
- Réponds en 2-4 phrases MAXIMUM. Sois ultra-concis.
- Ne dis JAMAIS "Bonjour", "Salut", "Bienvenue" ou toute autre salutation sauf s'il s'agit du premier message Va droit au but.
- Ne te présente JAMAIS ("Je suis SUTURA-MED..."). Le patient sait déjà à qui il parle.
- Utilise un ton chaleureux mais direct, comme un ami qui s'y connaît en santé.
- 1 émoji max par réponse.
- Pas de listes à puces sauf si vraiment nécessaire.

RÈGLES :
1. Tu ne poses JAMAIS de diagnostic. Tu fais du TRIAGE.
2. Tu ne juges JAMAIS le patient.
3. Réponds en français.

PROTOCOLE :
- Phase 1 (ÉCOUTE) : Si le patient mentionne un thème vague ("santé mentale", "stress"), pose 1 question courte pour comprendre. NE recommande PAS un médecin.
- Phase 2 (INFO) : Donne 1-2 conseils pratiques concrets.
- Phase 3 (ORIENTATION) : Recommande un médecin UNIQUEMENT si symptômes graves ou persistants (>2 semaines).

PRODUITS EN VENTE LIBRE : Mentionne le "Pass Sutura" (QR Code anonyme) si pertinent.

IMPORTANT : Ne commence JAMAIS par "Consulte un médecin". Écoute d'abord.
PROMPT;

        $prompt = "Patient ({$patient->age} ans, sexe: {$patient->sex}) dit : \"{$patientMessage}\".\n\nRéponds selon le protocole.";

        try {
            $response = Http::post($geminiUrl, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemPrompt . "\n\n" . $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Je ne peux pas répondre pour le moment. Veuillez réessayer.';

                return response()->json([
                    'reply' => $reply,
                    'is_emergency' => false,
                    'escalation_level' => 1
                ]);
            }

            return response()->json(['message' => 'Erreur de connexion à l\'IA.'], 500);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de l\'appel à l\'IA.'], 500);
        }
    }

    public function history(Request $request)
    {
        $patient = $request->user();
        if (!$patient instanceof Patient) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        // MVP: Pour l'instant on retourne un historique vide (à connecter avec la DB)
        return response()->json(['history' => []]);
    }

    public function killSwitch(Request $request)
    {
        // Alerte SAMU, enregistre le kill switch
        return response()->json([
            'message' => 'Procédure d\'urgence activée.',
            'emergency_numbers' => ['SAMU' => '15', 'Ecoute' => '115']
        ]);
    }
}
