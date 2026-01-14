<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeleteAccountController extends Controller
{
    public function form()
    {
        $title = 'Suppression du compte';

        return view('frontend.remove', compact('title'));
    }

    public function remove(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        $user = Utilisateur::whereEmail($request['email'])->first();

        $error = true;

        if ($user == null) {
            $message = "Ce courriel n'existe pas dans notre base de données";
        } else {
            $message = 'Votre compte a bien été supprimé';
            $error = false;

            // Supprimer les données liées
            $user->responses()->delete();
            $user->alertes()->delete();
            $user->notificationPreferences()->delete();
            $user->tokens()->delete();
            $user->delete();

            Log::info('Account deleted via web form', ['email' => $request['email']]);
        }

        return redirect(route('remove.form'))->with([
            'error' => $error,
            'message' => $message,
        ]);
    }

    /**
     * Facebook Data Deletion Callback
     * Called by Facebook when a user requests deletion of their data
     */
    public function facebookDataDeletion(Request $request)
    {
        // Parse the signed request from Facebook
        $signedRequest = $request->input('signed_request');

        if (! $signedRequest) {
            return response()->json(['error' => 'Missing signed_request'], 400);
        }

        $data = $this->parseSignedRequest($signedRequest);

        if (! $data) {
            return response()->json(['error' => 'Invalid signed_request'], 400);
        }

        $facebookUserId = $data['user_id'] ?? null;

        if (! $facebookUserId) {
            return response()->json(['error' => 'Missing user_id'], 400);
        }

        // Find and delete the user
        $user = Utilisateur::where('provider', 'facebook')
            ->where('provider_id', $facebookUserId)
            ->first();

        if ($user) {
            // Supprimer les données liées
            $user->responses()->delete();
            $user->alertes()->delete();
            $user->notificationPreferences()->delete();
            $user->tokens()->delete();
            $user->delete();

            Log::info('Facebook data deletion callback - user deleted', [
                'facebook_user_id' => $facebookUserId,
            ]);
        }

        // Generate a confirmation code
        $confirmationCode = 'del_'.bin2hex(random_bytes(10));

        // Return the response Facebook expects
        return response()->json([
            'url' => route('remove.form'),
            'confirmation_code' => $confirmationCode,
        ]);
    }

    /**
     * Parse Facebook signed request
     */
    private function parseSignedRequest($signedRequest)
    {
        [$encodedSig, $payload] = explode('.', $signedRequest, 2);

        $secret = config('services.facebook.app_secret');

        // Decode the data
        $sig = $this->base64UrlDecode($encodedSig);
        $data = json_decode($this->base64UrlDecode($payload), true);

        // Verify the signature
        $expectedSig = hash_hmac('sha256', $payload, $secret, true);

        if ($sig !== $expectedSig) {
            Log::warning('Facebook signed request signature mismatch');

            return null;
        }

        return $data;
    }

    private function base64UrlDecode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
}
