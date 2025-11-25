<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse as response;
use App\Mail\NotificationEmail;
use App\Mail\SendCodeEmail;
use App\Models\Code;
use App\Models\Utilisateur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class APIAuthController extends Controller
{
    public function login(Request $request)
    {
        $email = $request['email'];
        $password = $request['password'];

        $client = Utilisateur::where('email', $email)->first();

        if ($client != null) {
            if (Hash::check($password, $client->password)) {
                if ($client->status) {
                    $data = [
                        'utilisateur' => $client->only(['id', 'nom', 'prenom', 'phone', 'email', 'dob', 'sexe'])
                    ];
                    return response::success($data);
                }
            }
        }

        $message = "Les informations sont incorrectes";
        return response::error($message, \Illuminate\Http\Response::HTTP_BAD_REQUEST);
    }

    public function register(Request $request)
    {
        // Déterminer le type d'inscription
        $inscriptionType = $request->input('type', 'phone'); // phone, email, social

        try {
            if ($inscriptionType === 'phone') {
                return $this->registerByPhone($request);
            } elseif ($inscriptionType === 'email') {
                return $this->registerByEmail($request);
            } elseif ($inscriptionType === 'social') {
                return $this->registerBySocial($request);
            }

            return response::error('Type d\'inscription invalide', \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            \Log::error('Erreur inscription: ' . $e->getMessage());
            return response::error('Une erreur est survenue lors de l\'inscription', \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function registerByPhone(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|unique:utilisateurs,phone|regex:/^\+224[0-9]{9}$/',
            'sexe' => 'required|in:M,F,Autre',
            'age' => 'required|integer|min:13|max:100',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
            'nom' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
        ], [
            'phone.required' => 'Le numéro de téléphone est obligatoire',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé',
            'phone.regex' => 'Le format du numéro doit être +224XXXXXXXXX',
            'sexe.required' => 'Le sexe est obligatoire',
            'sexe.in' => 'Le sexe doit être M, F ou Autre',
            'age.required' => 'L\'âge est obligatoire',
            'age.min' => 'Vous devez avoir au moins 13 ans',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
            'password.confirmed' => 'Les mots de passe ne concordent pas',
        ]);

        \DB::beginTransaction();

        try {
            // Calculer date de naissance approximative
            $dob = now()->subYears($validated['age'])->format('Y-m-d');

            $utilisateur = new Utilisateur();
            $utilisateur->nom = $validated['nom'] ?? '';
            $utilisateur->prenom = $validated['prenom'] ?? '';
            $utilisateur->phone = $validated['phone'];
            $utilisateur->sexe = $validated['sexe'];
            $utilisateur->dob = $dob;
            $utilisateur->password = bcrypt($validated['password']);
            $utilisateur->status = false;
            $utilisateur->save();

            // Générer code SMS à 4 chiffres
            $code = new Code();
            $code->code = rand(1000, 9999);
            $code->utilisateur_id = $utilisateur->id;
            $code->phone = $utilisateur->phone;
            $code->created_at = now();
            $code->save();

            // TODO: Envoyer SMS via API (à configurer avec le prestataire SMS)
            // $this->sendSMS($utilisateur->phone, "Votre code GquiOse : {$code->code}. Valide 10 minutes.");

            \DB::commit();

            $data = [
                'utilisateur' => $utilisateur->only(['id', 'nom', 'prenom', 'phone', 'sexe', 'dob']),
                'message' => 'Un code de vérification a été envoyé à votre numéro'
            ];

            return response::success($data);
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    private function registerByEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:utilisateurs,email|max:255',
            'sexe' => 'required|in:M,F,Autre',
            'age' => 'required|integer|min:13|max:100',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
            'nom' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
        ]);

        \DB::beginTransaction();

        try {
            $dob = now()->subYears($validated['age'])->format('Y-m-d');

            $utilisateur = new Utilisateur();
            $utilisateur->nom = $validated['nom'] ?? '';
            $utilisateur->prenom = $validated['prenom'] ?? '';
            $utilisateur->email = $validated['email'];
            $utilisateur->sexe = $validated['sexe'];
            $utilisateur->dob = $dob;
            $utilisateur->password = bcrypt($validated['password']);
            $utilisateur->status = false;
            $utilisateur->save();

            // Générer code email à 5 chiffres
            $code = new Code();
            $code->code = rand(10000, 99999);
            $code->utilisateur_id = $utilisateur->id;
            $code->email = $utilisateur->email;
            $code->created_at = now();
            $code->save();

            $fullname = trim($utilisateur->prenom . ' ' . $utilisateur->nom) ?: 'Utilisateur';
            $objet = "Activation de compte GquiOse";
            $greeting = "Bonjour " . $fullname;
            $content = "Votre code de confirmation est: " . $code->code . ". Il est valide pendant 10 minutes.";

            Mail::to($utilisateur->email)->send(new NotificationEmail($greeting, $objet, $content));

            \DB::commit();

            $data = [
                'utilisateur' => $utilisateur->only(['id', 'nom', 'prenom', 'email', 'sexe', 'dob']),
                'message' => 'Un code de vérification a été envoyé à votre email'
            ];

            return response::success($data);
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    private function registerBySocial(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|in:google,facebook',
            'provider_id' => 'required|string',
            'email' => 'required|email',
            'nom' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'photo' => 'nullable|url',
            'sexe' => 'required|in:M,F,Autre',
            'age' => 'required|integer|min:13|max:100',
        ]);

        \DB::beginTransaction();

        try {
            // Vérifier si l'utilisateur existe déjà
            $utilisateur = Utilisateur::where('email', $validated['email'])->first();

            if ($utilisateur) {
                \DB::commit();
                return response::error('Un compte existe déjà avec cet email', \Illuminate\Http\Response::HTTP_CONFLICT);
            }

            $dob = now()->subYears($validated['age'])->format('Y-m-d');

            $utilisateur = new Utilisateur();
            $utilisateur->nom = $validated['nom'] ?? '';
            $utilisateur->prenom = $validated['prenom'] ?? '';
            $utilisateur->email = $validated['email'];
            $utilisateur->sexe = $validated['sexe'];
            $utilisateur->dob = $dob;
            $utilisateur->provider = $validated['provider'];
            $utilisateur->provider_id = $validated['provider_id'];
            $utilisateur->photo = $validated['photo'] ?? null;
            $utilisateur->status = true; // Compte social activé directement
            $utilisateur->save();

            \DB::commit();

            $data = [
                'utilisateur' => $utilisateur->only(['id', 'nom', 'prenom', 'email', 'sexe', 'dob', 'photo']),
                'message' => 'Compte créé avec succès'
            ];

            return response::success($data);
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function codeConfirmation(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string', // email ou phone
            'code' => 'required|string|size:4',
        ], [
            'identifier.required' => 'L\'identifiant est obligatoire',
            'code.required' => 'Le code est obligatoire',
            'code.size' => 'Le code doit contenir 4 chiffres',
        ]);

        // Détecter si c'est un email ou un phone
        $isEmail = filter_var($validated['identifier'], FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'phone';

        // Vérifier les tentatives (max 3 par jour)
        $cacheKey = 'code_attempts_' . $validated['identifier'];
        $attempts = \Cache::get($cacheKey, 0);

        if ($attempts >= 3) {
            return response::error('Trop de tentatives. Veuillez réessayer dans 24 heures.', \Illuminate\Http\Response::HTTP_TOO_MANY_REQUESTS);
        }

        $codeConfirmation = Code::where($field, $validated['identifier'])
            ->where('code', $validated['code'])
            ->where('created_at', '>=', now()->subMinutes(10)) // Code valide 10 minutes
            ->orderByDesc('id')
            ->first();

        if (!$codeConfirmation) {
            // Incrémenter les tentatives
            \Cache::put($cacheKey, $attempts + 1, now()->addDay());
            
            $remainingAttempts = 3 - ($attempts + 1);
            $message = $remainingAttempts > 0 
                ? "Code incorrect. Il vous reste {$remainingAttempts} tentative(s)."
                : "Code incorrect. Trop de tentatives. Veuillez réessayer dans 24 heures.";

            return response::error($message, \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }

        // Code valide - activer le compte
        \DB::beginTransaction();
        try {
            $utilisateur = $codeConfirmation->utilisateur;
            $utilisateur->status = true;
            $utilisateur->email_verified_at = now();
            $utilisateur->save();

            // Supprimer le code utilisé
            $codeConfirmation->delete();

            // Réinitialiser les tentatives
            \Cache::forget($cacheKey);

            \DB::commit();

            $data = [
                'utilisateur' => $utilisateur->only(['id', 'nom', 'prenom', 'phone', 'email', 'dob', 'sexe']),
                'message' => 'Compte activé avec succès'
            ];

            return response::success($data);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Erreur activation compte: ' . $e->getMessage());
            return response::error('Une erreur est survenue', \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
