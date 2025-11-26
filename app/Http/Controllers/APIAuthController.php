<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse as response;
use App\Mail\SendCodeEmail;
use App\Models\Code;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class APIAuthController extends Controller
{
    /**
     * Connexion classique email/mot de passe
     */
    public function login(Request $request)
    {
        $email    = $request->input('email');
        $password = $request->input('password');
        $fcmToken = $request->input('fcm_token');
        $platform = $request->input('platform'); // android ou ios

        $client = Utilisateur::where('email', $email)->first();

        if (!$client || !Hash::check($password, $client->password)) {
            return response::error('Les informations sont incorrectes', 400);
        }

        if (!$client->status) {
            return response::error('Votre compte n\'est pas encore activé', 403);
        }

        // Mise à jour du token FCM et de la plateforme
        if ($fcmToken) {
            $client->fcm_token = $fcmToken;
        }
        if ($platform && in_array($platform, ['android', 'ios'])) {
            $client->platform = $platform;
        }
        $client->save();

        $data = [
            'utilisateur' => $client->only([
                'id', 'nom', 'prenom', 'phone', 'email', 'dob',
                'sexe', 'photo', 'ville_id', 'status'
            ])
        ];

        return response::success($data);
    }

    public function register(Request $request)
    {
        $type = $request->input('type', 'phone');

        try {
            return match ($type) {
                'phone'  => $this->registerByPhone($request),
                'email'  => $this->registerByEmail($request),
                'social' => $this->registerBySocial($request),
                default  => response::error('Type d\'inscription invalide', 400),
            };
        } catch (\Exception $e) {
            Log::error('Erreur inscription: ' . $e->getMessage());
            return response::error('Une erreur est survenue lors de l\'inscription', 500);
        }
    }

    // ← IL MANQUAIT CETTE ACCOLADE DANS LA VERSION PRÉCÉDENTE !

    private function registerByPhone(Request $request)
    {
        $validated = $request->validate([
            'phone'                => 'required|string|unique:utilisateurs,phone|regex:/^\+224[0-9]{9}$/',
            'sexe'                 => 'required|in:M,F,Autre',
            'age'                  => 'required|integer|min:13|max:100',
            'password'             => 'required|string|min:8|confirmed',
            'password_confirmation'=> 'required',
            'nom'                  => 'nullable|string|max:255',
            'prenom'               => 'nullable|string|max:255',
            'fcm_token'            => 'nullable|string',
            'platform'             => 'required|in:android,ios',
            'ville_id'             => 'nullable|exists:villes,id',
        ], [
            'phone.regex' => 'Le format du numéro doit être +224XXXXXXXXX',
            'phone.unique' => 'Ce numéro est déjà utilisé',
        ]);

        DB::beginTransaction();
        try {
            $dob = now()->subYears($validated['age'])->format('Y-m-d');

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

            Code::create([
                'code'           => rand(1000, 9999),
                'utilisateur_id' => $utilisateur->id,
                'phone'          => $utilisateur->phone,
            ]);

            DB::commit();

            return response::success([
                'utilisateur' => $utilisateur->only(['id', 'nom', 'prenom', 'phone', 'sexe', 'dob']),
                'message'     => 'Un code de vérification a été envoyé à votre numéro'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function registerByEmail(Request $request)
    {
        $validated = $request->validate([
            'email'                => 'required|email|unique:utilisateurs,email',
            'sexe'                 => 'required|in:M,F,Autre',
            'age'                  => 'required|integer|min:13|max:100',
            'password'             => 'required|string|min:8|confirmed',
            'password_confirmation'=> 'required',
            'nom'                  => 'nullable|string|max:255',
            'prenom'               => 'nullable|string|max:255',
            'fcm_token'            => 'nullable|string',
            'platform'             => 'required|in:android,ios',
            'ville_id'             => 'nullable|exists:villes,id',
        ]);

        DB::beginTransaction();
        try {
            $dob = now()->subYears($validated['age'])->format('Y-m-d');

            $utilisateur = Utilisateur::create([
                'nom'        => $validated['nom'] ?? '',
                'prenom'     => $validated['prenom'] ?? '',
                'email'      => $validated['email'],
                'sexe'       => $validated['sexe'],
                'dob'        => $dob,
                'password'   => bcrypt($validated['password']),
                'status'     => false,
                'fcm_token'  => $validated['fcm_token'] ?? null,
                'platform'   => $validated['platform'],
                'ville_id'   => $validated['ville_id'] ?? null,
            ]);

            $code = Code::create([
                'code'           => rand(10000, 99999),
                'utilisateur_id' => $utilisateur->id,
                'email'          => $utilisateur->email,
            ]);

            $fullname = trim($utilisateur->prenom . ' ' . $utilisateur->nom) ?: 'Utilisateur';

            Mail::to($utilisateur->email)->send(new SendCodeEmail($code->code, $fullname));

            DB::commit();

            return response::success([
                'utilisateur' => $utilisateur->only(['id', 'nom', 'prenom', 'email', 'sexe', 'dob', 'ville_id']),
                'message'     => 'Un code de vérification a été envoyé à votre email'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function registerBySocial(Request $request)
    {
        $validated = $request->validate([
            'provider'    => 'required|in:google,facebook,apple',
            'provider_id' => 'required|string',
            'email'       => 'required|email',
            'nom'         => 'nullable|string|max:255',
            'prenom'      => 'nullable|string|max:255',
            'photo'       => 'nullable|url',
            'sexe'        => 'required|in:M,F,Autre',
            'age'         => 'required|integer|min:13|max:100',
            'fcm_token'   => 'nullable|string',
            'platform'    => 'required|in:android,ios',
            'ville_id'    => 'nullable|exists:villes,id',
        ]);

        DB::beginTransaction();
        try {
            if (Utilisateur::where('email', $validated['email'])->exists()) {
                return response::error('Un compte existe déjà avec cet email', 409);
            }

            $dob = now()->subYears($validated['age'])->format('Y-m-d');

            $utilisateur = Utilisateur::create([
                'nom'         => $validated['nom'] ?? '',
                'prenom'      => $validated['prenom'] ?? '',
                'email'       => $validated['email'],
                'sexe'       => $validated['sexe'],
                'dob'         => $dob,
                'provider'    => $validated['provider'],
                'provider_id' => $validated['provider_id'],
                'photo'       => $validated['photo'] ?? null,
                'fcm_token'   => $validated['fcm_token'] ?? null,
                'platform'    => $validated['platform'],
                'ville_id'    => $validated['ville_id'] ?? null,
                'status'      => true,
            ]);

            DB::commit();

            return response::success([
                'utilisateur' => $utilisateur->only(['id', 'nom', 'prenom', 'email', 'sexe', 'dob', 'photo', 'ville_id']),
                'message'     => 'Compte créé avec succès'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function codeConfirmation(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string',
            'code'       => 'required|string|digits_between:4,5',
        ]);

        $isEmail = filter_var($validated['identifier'], FILTER_VALIDATE_EMAIL);
        $field   = $isEmail ? 'email' : 'phone';

        $cacheKey = 'code_attempts_' . $validated['identifier'];
        $attempts = Cache::get($cacheKey, 0);

        if ($attempts >= 3) {
            return response::error('Trop de tentatives. Réessayez dans 24h.', 429);
        }

        $codeRecord = Code::where($field, $validated['identifier'])
            ->where('code', $validated['code'])
            ->where('created_at', '>=', now()->subMinutes(10))
            ->latest()
            ->first();

        if (!$codeRecord) {
            Cache::put($cacheKey, $attempts + 1, now()->addDay());
            $remaining = 3 - ($attempts + 1);
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

            $codeRecord->delete();
            Cache::forget($cacheKey);

            DB::commit();

            return response::success([
                'utilisateur' => $utilisateur->only(['id', 'nom', 'prenom', 'phone', 'email', 'dob', 'sexe', 'photo', 'ville_id']),
                'message'     => 'Compte activé avec succès'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur activation: ' . $e->getMessage());
            return response::error('Une erreur est survenue', 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'utilisateur_id' => 'required|exists:utilisateurs,id',
            'nom'            => 'nullable|string|max:255',
            'prenom'         => 'nullable|string|max:255',
            'phone'          => 'nullable|string|unique:utilisateurs,phone,' . $request->utilisateur_id,
            'sexe'           => 'nullable|in:M,F,Autre',
            'dob'            => 'nullable|date',
            'ville_id'       => 'nullable|exists:villes,id',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $utilisateur = Utilisateur::findOrFail($validated['utilisateur_id']);

        $utilisateur->fill(collect($validated)->except(['utilisateur_id', 'photo'])->toArray());

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos-utilisateurs', 'public');
            $utilisateur->photo = $path;
        }

        $utilisateur->save();

        return response::success([
            'utilisateur' => $utilisateur->only(['id', 'nom', 'prenom', 'phone', 'email', 'dob', 'sexe', 'photo', 'ville_id']),
            'message'     => 'Profil mis à jour avec succès'
        ]);
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'utilisateur_id'           => 'required|exists:utilisateurs,id',
            'old_password'             => 'required|string',
            'new_password'             => 'required|string|min:8|confirmed',
            'new_password_confirmation'=> 'required',
        ]);

        $utilisateur = Utilisateur::findOrFail($validated['utilisateur_id']);

        if (!Hash::check($validated['old_password'], $utilisateur->password)) {
            return response::error('Ancien mot de passe incorrect', 400);
        }

        $utilisateur->password = bcrypt($validated['new_password']);
        $utilisateur->save();

        return response::success(['message' => 'Mot de passe modifié']);
    }

    public function deleteAccount(Request $request)
    {
        $validated = $request->validate([
            'utilisateur_id' => 'required|exists:utilisateurs,id',
            'password'       => 'required|string',
        ]);

        $utilisateur = Utilisateur::findOrFail($validated['utilisateur_id']);

        if (!Hash::check($validated['password'], $utilisateur->password)) {
            return response::error('Mot de passe incorrect', 400);
        }

        $utilisateur->responses()->delete();
        $utilisateur->alertes()->delete();
        $utilisateur->notificationPreferences()->delete();
        $utilisateur->delete();

        return response::success(['message' => 'Compte supprimé avec succès']);
    }
}