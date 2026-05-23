<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TriageController;
use App\Http\Controllers\PassController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\EmergencyController;

// --- 1. Authentification ---
Route::post('/auth/pro/login', [AuthController::class, 'proLogin']);
Route::post('/auth/patient/register', [AuthController::class, 'patientRegister']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', function (Request $request) {
        return $request->user();
    });
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // --- 2. Triage IA & Chatbot ---
    Route::post('/triage/chat', [TriageController::class, 'chat']);
    Route::get('/triage/history', [TriageController::class, 'history']);
    Route::post('/triage/kill-switch', [TriageController::class, 'killSwitch']);

    // --- Patient Secure Data ---
    Route::put('/patient/secure-data', [AuthController::class, 'updateSecureData']);

    // --- 3. Pass Sutura (QR Code) ---
    // Patients
    Route::post('/pass/generate', [PassController::class, 'generate']);
    Route::get('/pass/my-passes', [PassController::class, 'myPasses']);
    // Pros (Pharmaciens)
    Route::post('/pass/verify', [PassController::class, 'verify']);
    Route::post('/pass/consume', [PassController::class, 'consume']);

    // --- 4. Consultations & Escalade ---
    // Patients
    Route::post('/consultation/request', [ConsultationController::class, 'requestConsultation']);
    Route::get('/consultation/{id}/messages', [ConsultationController::class, 'getMessages']);
    Route::post('/consultation/{id}/reply', [ConsultationController::class, 'patientReply']);
    // Pros (Médecins)
    Route::get('/pro/consultations/pending', [ConsultationController::class, 'pendingConsultations']);
    Route::post('/pro/consultation/{id}/reply', [ConsultationController::class, 'reply']);
    Route::post('/pro/consultation/{id}/issue-code', [ConsultationController::class, 'issueCode']);

    // --- 5. Secours et Déchiffrement ---
    Route::post('/admin/emergency/request-access', [EmergencyController::class, 'requestAccess']);
    Route::post('/admin/emergency/break-seal', [EmergencyController::class, 'breakSeal']);
});
