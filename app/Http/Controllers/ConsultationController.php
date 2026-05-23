<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Consultation;
use App\Models\ConsultationMessage;
use Illuminate\Support\Facades\Validator;

class ConsultationController extends Controller
{
    public function requestConsultation(Request $request)
    {
        $patient = $request->user();

        $consultation = Consultation::create([
            'patient_id' => $patient->id,
            'symptoms' => $request->symptoms ?? 'Non spécifié',
            'urgency' => $request->urgency ?? 'Basse',
            'escalation_level' => $request->escalation_level ?? 1,
            'category' => $request->category,
            'ai_summary' => $request->ai_summary,
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Demande de consultation envoyée.', 'consultation' => $consultation]);
    }

    public function getMessages(Request $request, $id)
    {
        $consultation = Consultation::with(['messages', 'doctor'])->findOrFail($id);
        return response()->json([
            'messages' => $consultation->messages,
            'status' => $consultation->status,
            'doctor_name' => $consultation->doctor?->name,
        ]);
    }

    public function pendingConsultations(Request $request)
    {
        $consultations = Consultation::with('patient')->where('status', 'pending')->get()->map(function($c) {
            return [
                'id' => (string) $c->id,
                'pseudo' => $c->patient->pseudo ?? 'Anonyme',
                'avatarStyle' => $c->patient->avatar ?? 'adventurer-neutral',
                'symptoms' => $c->symptoms,
                'urgency' => $c->urgency,
                'escalationLevel' => $c->escalation_level,
                'time' => $c->created_at->diffForHumans(),
                'category' => $c->category,
                'aiSummary' => $c->ai_summary,
            ];
        });

        return response()->json(['consultations' => $consultations]);
    }

    public function reply(Request $request, $id)
    {
        $doctor = $request->user();
        
        $consultation = Consultation::findOrFail($id);
        if ($consultation->status === 'pending') {
            $consultation->update(['status' => 'active', 'doctor_id' => $doctor->id, 'started_at' => now()]);
        }

        $msg = ConsultationMessage::create([
            'consultation_id' => $consultation->id,
            'sender_type' => 'doctor',
            'sender_id' => $doctor->id,
            'content' => $request->message
        ]);

        return response()->json(['message' => 'Message envoyé.', 'data' => $msg]);
    }

    public function patientReply(Request $request, $id)
    {
        $patient = $request->user();
        
        $consultation = Consultation::findOrFail($id);
        if ($consultation->patient_id !== $patient->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $msg = ConsultationMessage::create([
            'consultation_id' => $consultation->id,
            'sender_type' => 'patient',
            'sender_id' => $patient->id,
            'content' => $request->message
        ]);

        return response()->json(['message' => 'Message envoyé.', 'data' => $msg]);
    }

    public function issueCode(Request $request, $id)
    {
        return response()->json(['code' => 'SUT-' . strtoupper(substr(uniqid(), -5))]);
    }
}
