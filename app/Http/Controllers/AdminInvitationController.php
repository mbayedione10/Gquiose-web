<?php

namespace App\Http\Controllers;

use App\Models\AdminInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminInvitationController extends Controller
{
    public function showAcceptForm(string $token)
    {
        $invitation = AdminInvitation::where('token', $token)->first();

        if (! $invitation) {
            return view('admin.invitation.invalid', [
                'message' => 'Cette invitation n\'existe pas ou a été supprimée.',
            ]);
        }

        if ($invitation->isAccepted()) {
            return view('admin.invitation.invalid', [
                'message' => 'Cette invitation a déjà été utilisée. Vous pouvez vous connecter avec votre compte.',
            ]);
        }

        if ($invitation->isExpired()) {
            return view('admin.invitation.invalid', [
                'message' => 'Cette invitation a expiré. Veuillez contacter un administrateur pour en obtenir une nouvelle.',
            ]);
        }

        return view('admin.invitation.accept', [
            'invitation' => $invitation,
        ]);
    }

    public function accept(Request $request, string $token)
    {
        $invitation = AdminInvitation::where('token', $token)->first();

        if (! $invitation || $invitation->isAccepted() || $invitation->isExpired()) {
            return redirect()->route('admin.invitation.accept', $token)
                ->with('error', 'Cette invitation n\'est plus valide.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => ['required', 'string', 'max:255', 'unique:users,phone'],
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
        ]);

        $user = User::create([
            'name' => $invitation->name,
            'email' => $invitation->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $invitation->role_id,
            'email_verified_at' => now(),
        ]);

        $invitation->update([
            'accepted_at' => now(),
        ]);

        Auth::login($user);

        return redirect('/admin')->with('success', 'Votre compte a été activé avec succès. Bienvenue !');
    }
}
