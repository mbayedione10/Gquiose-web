<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;

class DeleteAccountController extends Controller
{
    public function form()
    {
        $title = "Suppression du compte";

        return view('frontend.remove', compact('title'));
    }


    public function remove(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email'
        ]);

        $user = Utilisateur::whereEmail($request['email'])->first();

        $error = true;

        if ($user == null)
            $message = "Ce courriel n'existe pas dans notre base de données";
        else
        {
            $message = "Votre compte a bien été supprimé";
            $error = false;

            $user->delete();
        }

        return redirect(route('remove.form'))->with([
            'error' => $error,
            'message' => $message
        ]);
    }
}
