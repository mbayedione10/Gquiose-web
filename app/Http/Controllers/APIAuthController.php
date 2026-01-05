<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse as response;
use App\Mail\SendCodeEmail;
use App\Models\Code;
use App\Models\Utilisateur;
use App\Services\SMS\SMSService;
use App\Services\SocialAuth\SocialVerifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class APIAuthController extends Controller
{
    /**
     * Connexion avec détection automatique email/phone
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string', // Email ou téléphone
            'password' => 'required|string',
            'fcm_token' => 'nullable|string',
            'platform' => 'required|in:android,ios',
        ]);

        // Détection automatique email ou phone
        $isEmail = filter_var($validated['identifier'], FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'phone';

        $identifier = $validated['identifier'];
        if (!$isEmail) {
            $client = Utilisateur::where('phone', $identifier)->first();
        } else {
            $client = Utilisateur::where('email', $identifier)->first();
        }

        if (!$client || !Hash::check($validated['password'], $client->password)) {
            Log::warning('Login failed - invalid credentials', [
                'identifier_type' => $field,
                'identifier' => $isEmail ? $identifier : substr($identifier, 0, 4) . '****'
            ]);
            return response::error('Les informations sont incorrectes', 400);
        }

        if (!$client->status) {
            Log::warning('Login failed - account not activated', ['user_id' => $client->id]);
            return response::error('Votre compte n\'est pas encore activé', 403);
        }

        // Mise à jour du token FCM et de la plateforme
        if (isset($validated['fcm_token'])) {
            $client->fcm_token = $validated['fcm_token'];
        }
        if (isset($validated['platform'])) {
            $client->platform = $validated['platform'];
        }
        $client->save();

        // Générer un token Sanctum
        $token = $client->createToken('auth_token', ['*'], now()->addDays(30))->plainTextToken;

        Log::info('Login successful', [
            'user_id' => $client->id,
            'platform' => $validated['platform']
        ]);

        return response::success([
            'utilisateur' => $client->only([
                'id', 'nom', 'prenom', 'phone', 'email', 'dob',
                'sexe', 'photo', 'ville_id', 'status', 'provider', 'platform'
            ]),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 30 * 24 * 60 * 60, // 30 jours en secondes
        ]);
    }

    /**
     * Déconnexion (révocation du token)
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            // Révoquer le token actuel
            $request->user()->currentAccessToken()->delete();

            Log::info('Logout successful', ['user_id' => $user->id]);

            return response::success(['message' => 'Déconnexion réussie']);
        }

        return response::error('Non authentifié', 401);
    }

    /**
     * Inscription avec détection automatique du type (phone, email, social)
     */
    public function register(Request $request)
    {
        // Détection automatique du type d'inscription
        $type = $request->input('type');

        if (!$type) {
            // Auto-détection basée sur les champs présents
            if ($request->has('access_token') && $request->has('provider')) {
                $type = 'social';
            } elseif ($request->has('email') && !$request->has('phone')) {
                $type = 'email';
            } elseif ($request->has('phone') && !$request->has('email')) {
                $type = 'phone';
            } elseif ($request->has('email') && $request->has('phone')) {
                // Si les deux sont présents, privilégier email
                $type = 'email';
            } else {
                return response::error('Impossible de déterminer le type d\'inscription. Veuillez fournir soit un email, soit un téléphone, soit un provider social.', 400);
            }
        }

        try {
            return match ($type) {
                'phone'  => $this->registerByPhone($request),
                'email'  => $this->registerByEmail($request),
                'social' => $this->registerBySocial($request),
                default  => response::error('Type d\'inscription invalide', 400),
            };
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response::error($e->errors(), 422);
        } catch (\Exception $e) {
            Log::error('Erreur inscription', [
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $message = 'Une erreur est survenue lors de l\'inscription';
            if (config('app.debug')) {
                $message .= ' : ' . $e->getMessage();
            }
            return response::error($message, 500);
        }
    }

    /**
     * Inscription par téléphone
     */
    private function registerByPhone(Request $request)
    {
        $validated = $request->validate([
            'phone'                => 'required|string|regex:/^\+?[0-9]{8,15}$/',
            'sexe'                 => 'required|in:M,F,Autre',
            'dob'                  => 'required|date|before:today|after:' . now()->subYears(100)->format('Y-m-d'),
            'password'             => 'required|string|min:8|confirmed',
            'password_confirmation'=> 'required',
            'nom'                  => 'nullable|string|max:255',
            'prenom'               => 'nullable|string|max:255',
            'fcm_token'            => 'nullable|string',
            'platform'             => 'required|in:android,ios',
            'ville_id'             => 'nullable|exists:villes,id',
        ], [
            'phone.regex' => 'Le numéro doit contenir entre 8 et 15 chiffres',
            'dob.before' => 'La date de naissance doit être antérieure à aujourd\'hui',
            'dob.after' => 'L\'âge ne peut pas dépasser 100 ans',
        ]);

        // Vérifier que l'utilisateur a au moins 13 ans
        $age = now()->diffInYears($validated['dob']);
        if ($age < 13) {
            return response::error('Vous devez avoir au moins 13 ans pour vous inscrire', 400);
        }

        // Vérifier unicité du phone
        if (Utilisateur::where('phone', $validated['phone'])->exists()) {
            return response::error('Ce numéro est déjà utilisé', 409);
        }

        // Anti-spam : vérifier qu'on n'a pas envoyé de code récemment
        $antiSpamKey = 'sms_sent_' . $validated['phone'];
        if (Cache::has($antiSpamKey)) {
            $remainingTime = Cache::get($antiSpamKey) - time();
            return response::error("Veuillez patienter {$remainingTime} secondes avant de renvoyer un code", 429);
        }

        DB::beginTransaction();
        try {
            $dob = $validated['dob'];

            $utilisateur = Utilisateur::create([
                'nom'        => $validated['nom'] ?? '',
                'prenom'     => $validated['prenom'] ?? '',
                'phone'      => $validated['phone'],
                'sexe'       => $validated['sexe'],
                'dob'        => $dob,
                'password'   => bcrypt($validated['password']),
                'status'     => false,
                'fcm_token'  => $validated['fcm_token'] ?? null,
                'platform'   => $validated['platform'],
                'ville_id'   => $validated['ville_id'] ?? null,
            ]);

            // Générer code 4 chiffres
            $codeValue = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            Code::create([
                'code'           => $codeValue,
                'utilisateur_id' => $utilisateur->id,
                'phone'          => $validated['phone'],
            ]);

            // Envoyer le SMS
            $smsService = new SMSService();
            $smsSent = $smsService->sendVerificationCode($validated['phone'], $codeValue);

            if (!$smsSent) {
                DB::rollBack();
                Log::error('SMS sending failed during registration', [
                    'phone' => substr($validated['phone'], 0, 4) . '****'
                ]);
                return response::error('Impossible d\'envoyer le SMS. Veuillez réessayer.', 500);
            }

            // Marquer l'anti-spam (60 secondes)
            Cache::put($antiSpamKey, time() + 60, 60);

            DB::commit();

            Log::info('Registration by phone successful', [
                'user_id' => $utilisateur->id,
                'phone' => substr($validated['phone'], 0, 4) . '****'
            ]);

            return response::success([
                'utilisateur' => $utilisateur->only(['id', 'nom', 'prenom', 'sexe', 'dob', 'provider', 'platform']),
                'message' => 'Un code de vérification a été envoyé à votre numéro',
                'verification_status' => 'pending_verification',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Inscription par email
     */
    private function registerByEmail(Request $request)
    {
        $validated = $request->validate([
            'email'                => 'required|email|unique:utilisateurs,email',
            'phone'                => 'nullable|string|regex:/^\+?[0-9]{8,15}$/',
            'sexe'                 => 'required|in:M,F,Autre',
            'dob'                  => 'required|date|before:today|after:' . now()->subYears(100)->format('Y-m-d'),
            'password'             => 'required|string|min:8|confirmed',
            'password_confirmation'=> 'required',
            'nom'                  => 'nullable|string|max:255',
            'prenom'               => 'nullable|string|max:255',
            'fcm_token'            => 'nullable|string',
            'platform'             => 'required|in:android,ios',
            'ville_id'             => 'nullable|exists:villes,id',
        ], [
            'dob.before' => 'La date de naissance doit être antérieure à aujourd\'hui',
            'dob.after' => 'L\'âge ne peut pas dépasser 100 ans',
            'phone.regex' => 'Le numéro doit contenir entre 8 et 15 chiffres',
            'platform.required' => 'La plateforme est requise',
            'platform.in' => 'La plateforme doit être android ou ios',
        ]);

        // Vérifier que l'utilisateur a au moins 13 ans
        $age = now()->diffInYears($validated['dob']);
        if ($age < 13) {
            return response::error('Vous devez avoir au moins 13 ans pour vous inscrire', 400);
        }

        // Vérifier unicité du phone si fourni
        if (!empty($validated['phone'])) {
            if (Utilisateur::where('phone', $validated['phone'])->exists()) {
                return response::error('Ce numéro de téléphone est déjà utilisé', 409);
            }
        }

        // Anti-spam : vérifier qu'on n'a pas envoyé de code récemment
        $antiSpamKey = 'email_sent_' . $validated['email'];
        if (Cache::has($antiSpamKey)) {
            $remainingTime = Cache::get($antiSpamKey) - time();
            return response::error("Veuillez patienter {$remainingTime} secondes avant de renvoyer un code", 429);
        }

        DB::beginTransaction();
        try {
            $dob = $validated['dob'];

            $utilisateur = Utilisateur::create([
                'nom'        => $validated['nom'] ?? '',
                'prenom'     => $validated['prenom'] ?? '',
                'email'      => $validated['email'],
                'phone'      => $validated['phone'] ?? null,
                'sexe'       => $validated['sexe'],
                'dob'        => $dob,
                'password'   => bcrypt($validated['password']),
                'status'     => false,
                'fcm_token'  => $validated['fcm_token'] ?? null,
                'platform'   => $validated['platform'],
                'ville_id'   => $validated['ville_id'] ?? null,
            ]);

            // Générer code 4 chiffres (unifié avec phone)
            $codeValue = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            Code::create([
                'code'           => $codeValue,
                'utilisateur_id' => $utilisateur->id,
                'email'          => $utilisateur->email,
            ]);

            $fullname = trim($utilisateur->prenom . ' ' . $utilisateur->nom) ?: 'Utilisateur';
            $title = 'Bienvenue sur G Qui Ose !';
            $content = "Félicitations {$fullname} ! Vous êtes à un pas de rejoindre notre communauté. Utilisez le code ci-dessous pour activer votre compte et commencer votre aventure avec G Qui Ose.";

            Mail::to($utilisateur->email)->send(new SendCodeEmail($title, $content, $codeValue));

            // Marquer l'anti-spam (60 secondes)
            Cache::put($antiSpamKey, time() + 60, 60);

            DB::commit();

            Log::info('Registration by email successful', [
                'user_id' => $utilisateur->id,
                'email' => $utilisateur->email
            ]);

            return response::success([
                'utilisateur' => $utilisateur->only(['id', 'nom', 'prenom', 'email', 'sexe', 'dob', 'ville_id', 'provider', 'platform']),
                'message' => 'Un code de vérification a été envoyé à votre email',
                'verification_status' => 'pending_verification',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Inscription par social login (avec vérification de token)
     */
    private function registerBySocial(Request $request)
    {
        $validated = $request->validate([
            'provider'    => 'required|in:google,facebook,apple',
            'access_token'=> 'required|string', // Token à vérifier
            'fcm_token'   => 'nullable|string',
            'platform'    => 'nullable|in:android,ios',
        ]);

        DB::beginTransaction();
        try {
            // Vérifier le token auprès du fournisseur
            $verifier = new SocialVerifier();
            $socialData = $verifier->verify($validated['provider'], $validated['access_token']);

            if (!$socialData || !isset($socialData['provider_id'])) {
                Log::warning('Social token verification failed', [
                    'provider' => $validated['provider']
                ]);
                return response::error('Token invalide ou expiré', 401);
            }

            // Vérifier si l'utilisateur existe déjà (par email ou provider_id)
            $existingUser = Utilisateur::where('email', $socialData['email'])
                ->orWhere(function ($query) use ($validated, $socialData) {
                    $query->where('provider', $validated['provider'])
                          ->where('provider_id', $socialData['provider_id']);
                })
                ->first();

            if ($existingUser) {
                // Si l'utilisateur existe, on le connecte directement
                // Générer un token Sanctum
                $token = $existingUser->createToken('auth_token', ['*'], now()->addDays(30))->plainTextToken;

                DB::commit();

                Log::info('Social login - existing user', [
                    'user_id' => $existingUser->id,
                    'provider' => $validated['provider']
                ]);

                return response::success([
                    'utilisateur' => $existingUser->only([
                        'id', 'nom', 'prenom', 'email', 'photo', 'provider', 'platform'
                    ]),
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => 30 * 24 * 60 * 60,
                    'message' => 'Connexion réussie'
                ]);
            }

            // Créer un nouveau compte avec les données du provider social
            $nom = $socialData['family_name'] ?? '';
            $prenom = $socialData['given_name'] ?? ($socialData['name'] ?? '');

            $utilisateur = Utilisateur::create([
                'nom'         => $nom,
                'prenom'      => $prenom,
                'email'       => $socialData['email'],
                'phone'       => null,
                'provider'    => $validated['provider'],
                'provider_id' => $socialData['provider_id'],
                'photo'       => $socialData['picture'] ?? null,
                'fcm_token'   => $validated['fcm_token'] ?? null,
                'platform'    => $validated['platform'] ?? null,
                'status'      => true, // Activé directement (email vérifié par le provider)
                'email_verified_at' => $socialData['email_verified'] ?? false ? now() : null,
            ]);

            // Générer un token Sanctum
            $token = $utilisateur->createToken('auth_token', ['*'], now()->addDays(30))->plainTextToken;

            DB::commit();

            Log::info('Social registration successful', [
                'user_id' => $utilisateur->id,
                'provider' => $validated['provider']
            ]);

            return response::success([
                'utilisateur' => $utilisateur->only([
                    'id', 'nom', 'prenom', 'email', 'photo', 'provider', 'platform'
                ]),
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 30 * 24 * 60 * 60,
                'message' => 'Compte créé avec succès'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Confirmation du code (email ou phone)
     */
    public function codeConfirmation(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string', // Email ou téléphone
            'code'       => 'required|string|digits:4', // Unifié à 4 chiffres
        ]);

        $isEmail = filter_var($validated['identifier'], FILTER_VALIDATE_EMAIL);

        $cacheKey = 'code_attempts_' . $validated['identifier'];
        $attempts = Cache::get($cacheKey, 0);

        if ($attempts >= 3) {
            Log::warning('Code confirmation blocked - too many attempts', [
                'identifier' => $isEmail ? $validated['identifier'] : substr($validated['identifier'], 0, 4) . '****'
            ]);
            return response::error('Trop de tentatives. Réessayez dans 24h.', 429);
        }

        // Recherche du code
        if ($isEmail) {
            $codeRecord = Code::where('email', $validated['identifier'])
                ->where('code', $validated['code'])
                ->where('created_at', '>=', now()->subMinutes(10))
                ->latest()
                ->first();
        } else {
            $codeRecord = Code::where('phone', $validated['identifier'])
                ->where('code', $validated['code'])
                ->where('created_at', '>=', now()->subMinutes(10))
                ->latest()
                ->first();
        }

        if (!$codeRecord) {
            Cache::put($cacheKey, $attempts + 1, now()->addDay());
            $remaining = 3 - ($attempts + 1);

            Log::warning('Code confirmation failed - invalid code', [
                'identifier' => $isEmail ? $validated['identifier'] : substr($validated['identifier'], 0, 4) . '****',
                'attempt' => $attempts + 1
            ]);

            $msg = $remaining > 0
                ? "Code incorrect. Il vous reste {$remaining} tentative(s)."
                : "Trop de tentatives. Réessayez dans 24h.";
            return response::error($msg, 400);
        }

        DB::beginTransaction();
        try {
            $utilisateur = $codeRecord->utilisateur;
            $utilisateur->status = true;

            if ($isEmail) {
                $utilisateur->email_verified_at = now();
            } else {
                $utilisateur->phone_verified_at = now();
            }
            $utilisateur->save();

            // Générer un token Sanctum pour connexion automatique
            $token = $utilisateur->createToken('auth_token', ['*'], now()->addDays(30))->plainTextToken;

            $codeRecord->delete();
            Cache::forget($cacheKey);

            DB::commit();

            Log::info('Code confirmation successful - account activated', [
                'user_id' => $utilisateur->id,
                'verification_type' => $isEmail ? 'email' : 'phone'
            ]);

            return response::success([
                'utilisateur' => $utilisateur->only([
                    'id', 'nom', 'prenom', 'phone', 'email', 'dob', 'sexe', 'photo', 'ville_id', 'provider', 'platform'
                ]),
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 30 * 24 * 60 * 60,
                'message' => 'Compte activé avec succès'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur activation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response::error('Une erreur est survenue', 500);
        }
    }

    /**
     * Renvoyer le code de vérification
     */
    public function resendVerificationCode(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string', // Email ou phone
        ]);

        $isEmail = filter_var($validated['identifier'], FILTER_VALIDATE_EMAIL);

        // Anti-spam : vérifier qu'on n'a pas envoyé de code récemment
        $antiSpamKey = 'resend_code_' . $validated['identifier'];
        if (Cache::has($antiSpamKey)) {
            $remainingTime = Cache::get($antiSpamKey) - time();
            return response::error("Veuillez patienter {$remainingTime} secondes avant de renvoyer un code", 429);
        }

        // Rechercher l'utilisateur
        if ($isEmail) {
            $utilisateur = Utilisateur::where('email', $validated['identifier'])->first();
        } else {
            $utilisateur = Utilisateur::where('phone', $validated['identifier'])->first();
        }

        if (!$utilisateur) {
            Log::warning('Resend code requested for non-existent user', [
                'identifier' => $isEmail ? $validated['identifier'] : substr($validated['identifier'], 0, 4) . '****'
            ]);
            return response::error('Utilisateur introuvable', 404);
        }

        // Vérifier que le compte n'est pas déjà activé
        if ($utilisateur->status) {
            return response::error('Votre compte est déjà activé', 400);
        }

        DB::beginTransaction();
        try {
            // Supprimer l'ancien code
            Code::where('utilisateur_id', $utilisateur->id)
                ->where($isEmail ? 'email' : 'phone', '!=', null)
                ->delete();

            // Générer nouveau code 4 chiffres
            $codeValue = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            Code::create([
                'code'           => $codeValue,
                'utilisateur_id' => $utilisateur->id,
                'email'          => $isEmail ? $validated['identifier'] : null,
                'phone'          => !$isEmail ? $validated['identifier'] : null,
            ]);

            // Envoyer par email ou SMS
            if ($isEmail) {
                $fullname = trim($utilisateur->prenom . ' ' . $utilisateur->nom) ?: 'Utilisateur';
                $title = 'Nouveau code de vérification G Qui Ose';
                $content = "{$fullname}, vous avez demandé un nouveau code de vérification. Utilisez le code ci-dessous pour finaliser l'activation de votre compte G Qui Ose.";

                Mail::to($validated['identifier'])->send(new SendCodeEmail($title, $content, $codeValue));
            } else {
                $smsService = new SMSService();
                $smsSent = $smsService->sendVerificationCode($validated['identifier'], $codeValue);

                if (!$smsSent) {
                    DB::rollBack();
                    Log::error('SMS sending failed during code resend', [
                        'phone' => substr($validated['identifier'], 0, 4) . '****'
                    ]);
                    return response::error('Impossible d\'envoyer le SMS. Veuillez réessayer.', 500);
                }
            }

            // Marquer l'anti-spam (60 secondes)
            Cache::put($antiSpamKey, time() + 60, 60);

            DB::commit();

            Log::info('Verification code resent', [
                'user_id' => $utilisateur->id,
                'type' => $isEmail ? 'email' : 'sms'
            ]);

            return response::success([
                'message' => 'Un nouveau code de vérification a été envoyé'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resending verification code', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response::error('Une erreur est survenue', 500);
        }
    }

    /**
     * Envoi du code de réinitialisation de mot de passe
     */
    public function sendPasswordResetCode(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string', // Email ou phone
        ]);

        $isEmail = filter_var($validated['identifier'], FILTER_VALIDATE_EMAIL);

        // Anti-spam
        $antiSpamKey = 'reset_code_sent_' . $validated['identifier'];
        if (Cache::has($antiSpamKey)) {
            $remainingTime = Cache::get($antiSpamKey) - time();
            return response::error("Veuillez patienter {$remainingTime} secondes avant de renvoyer un code", 429);
        }

        // Rechercher l'utilisateur
        if ($isEmail) {
            $utilisateur = Utilisateur::where('email', $validated['identifier'])->first();
        } else {
            $utilisateur = Utilisateur::where('phone', $validated['identifier'])->first();
        }

        if (!$utilisateur) {
            // Pour la sécurité, on retourne le même message même si l'utilisateur n'existe pas
            Log::warning('Password reset requested for non-existent user', [
                'identifier' => $isEmail ? $validated['identifier'] : substr($validated['identifier'], 0, 4) . '****'
            ]);
            return response::success([
                'message' => 'Si cet identifiant existe, un code de réinitialisation a été envoyé'
            ]);
        }

        DB::beginTransaction();
        try {
            // Supprimer les anciens codes de reset
            Code::where('utilisateur_id', $utilisateur->id)
                ->where($isEmail ? 'email' : 'phone', '!=', null)
                ->delete();

            // Générer nouveau code 4 chiffres
            $codeValue = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            Code::create([
                'code'           => $codeValue,
                'utilisateur_id' => $utilisateur->id,
                'email'          => $isEmail ? $validated['identifier'] : null,
                'phone'          => !$isEmail ? $validated['identifier'] : null,
            ]);

            // Envoyer par email ou SMS
            if ($isEmail) {
                $fullname = trim($utilisateur->prenom . ' ' . $utilisateur->nom) ?: 'Utilisateur';
                $title = 'Réinitialisation de votre mot de passe G Qui Ose';
                $content = "{$fullname}, nous avons reçu une demande de réinitialisation de votre mot de passe. Utilisez le code ci-dessous pour créer un nouveau mot de passe sécurisé.";

                Mail::to($validated['identifier'])->send(new SendCodeEmail($title, $content, $codeValue));
            } else {
                $smsService = new SMSService();
                $smsSent = $smsService->sendPasswordResetCode($validated['identifier'], $codeValue);

                if (!$smsSent) {
                    DB::rollBack();
                    Log::error('SMS sending failed during password reset', [
                        'phone' => substr($validated['identifier'], 0, 4) . '****'
                    ]);
                    return response::error('Impossible d\'envoyer le SMS. Veuillez réessayer.', 500);
                }
            }

            // Marquer l'anti-spam (60 secondes)
            Cache::put($antiSpamKey, time() + 60, 60);

            DB::commit();

            Log::info('Password reset code sent', [
                'user_id' => $utilisateur->id,
                'type' => $isEmail ? 'email' : 'sms'
            ]);

            return response::success([
                'message' => 'Un code de réinitialisation a été envoyé'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error sending password reset code', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response::error('Une erreur est survenue', 500);
        }
    }

    /**
     * Réinitialisation du mot de passe avec code
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string',
            'code' => 'required|string|digits:4',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $isEmail = filter_var($validated['identifier'], FILTER_VALIDATE_EMAIL);

        $cacheKey = 'reset_attempts_' . $validated['identifier'];
        $attempts = Cache::get($cacheKey, 0);

        if ($attempts >= 3) {
            return response::error('Trop de tentatives. Réessayez dans 24h.', 429);
        }

        // Recherche du code
        if ($isEmail) {
            $codeRecord = Code::where('email', $validated['identifier'])
                ->where('code', $validated['code'])
                ->where('created_at', '>=', now()->subMinutes(10))
                ->latest()
                ->first();
        } else {
            $codeRecord = Code::where('phone', $validated['identifier'])
                ->where('code', $validated['code'])
                ->where('created_at', '>=', now()->subMinutes(10))
                ->latest()
                ->first();
        }

        if (!$codeRecord) {
            Cache::put($cacheKey, $attempts + 1, now()->addDay());
            $remaining = 3 - ($attempts + 1);

            Log::warning('Password reset failed - invalid code', [
                'identifier' => $isEmail ? $validated['identifier'] : substr($validated['identifier'], 0, 4) . '****',
                'attempt' => $attempts + 1
            ]);

            $msg = $remaining > 0
                ? "Code incorrect. Il vous reste {$remaining} tentative(s)."
                : "Trop de tentatives. Réessayez dans 24h.";
            return response::error($msg, 400);
        }

        DB::beginTransaction();
        try {
            $utilisateur = $codeRecord->utilisateur;
            $utilisateur->password = bcrypt($validated['password']);
            $utilisateur->save();

            $codeRecord->delete();
            Cache::forget($cacheKey);

            DB::commit();

            Log::info('Password reset successful', ['user_id' => $utilisateur->id]);

            return response::success([
                'message' => 'Mot de passe réinitialisé avec succès'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resetting password', [
                'error' => $e->getMessage()
            ]);
            return response::error('Une erreur est survenue', 500);
        }
    }

    /**
     * Mise à jour du profil
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response::error('Non authentifié', 401);
        }

        $validated = $request->validate([
            'nom'      => 'nullable|string|max:255',
            'prenom'   => 'nullable|string|max:255',
            'sexe'     => 'nullable|in:M,F,Autre',
            'dob'      => 'nullable|date',
            'ville_id' => 'nullable|exists:villes,id',
            'photo'    => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $user->fill(collect($validated)->except(['photo'])->toArray());

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos-utilisateurs', 'public');
            $user->photo = $path;
        }

        $user->save();

        Log::info('Profile updated', ['user_id' => $user->id]);

        return response::success([
            'utilisateur' => $user->only(['id', 'nom', 'prenom', 'phone', 'email', 'dob', 'sexe', 'photo', 'ville_id', 'provider', 'platform']),
            'message' => 'Profil mis à jour avec succès'
        ]);
    }

    /**
     * Récupérer le profil de l'utilisateur authentifié
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response::error('Non authentifié', 401);
        }

        return response::success([
            'utilisateur' => $user->only([
                'id', 'nom', 'prenom', 'phone', 'email', 'dob',
                'sexe', 'photo', 'ville_id', 'status', 'provider', 'platform'
            ]),
        ]);
    }

    /**
     * Changement de mot de passe (utilisateur authentifié)
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response::error('Non authentifié', 401);
        }

        $validated = $request->validate([
            'old_password'             => 'required|string',
            'new_password'             => 'required|string|min:8|confirmed',
            'new_password_confirmation'=> 'required',
        ]);

        if (!Hash::check($validated['old_password'], $user->password)) {
            Log::warning('Password change failed - incorrect old password', ['user_id' => $user->id]);
            return response::error('Ancien mot de passe incorrect', 400);
        }

        $user->password = bcrypt($validated['new_password']);
        $user->save();

        Log::info('Password changed', ['user_id' => $user->id]);

        return response::success(['message' => 'Mot de passe modifié']);
    }

    /**
     * Suppression du compte
     */
    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response::error('Non authentifié', 401);
        }

        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($validated['password'], $user->password)) {
            Log::warning('Account deletion failed - incorrect password', ['user_id' => $user->id]);
            return response::error('Mot de passe incorrect', 400);
        }

        $userId = $user->id;

        DB::beginTransaction();
        try {
            $user->responses()->delete();
            $user->alertes()->delete();
            $user->notificationPreferences()->delete();
            $user->tokens()->delete(); // Supprimer tous les tokens Sanctum
            $user->delete();

            DB::commit();

            Log::info('Account deleted', ['user_id' => $userId]);

            return response::success(['message' => 'Compte supprimé avec succès']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting account', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return response::error('Une erreur est survenue', 500);
        }
    }
}
