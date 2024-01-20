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


        $client = Utilisateur::where('email', $email)
            ->first();



        if ($client != null) {
            if (Hash::check($password, $client->password)) {

                if ($client->status)
                {
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


        $nom = $request['nom'];
        $prenom = $request['prenom'];
        $sexe = $request['sexe'];
        $phone = $request['phone'];
        $email = $request['email'];
        $dob = $request['dob'];
        $password = $request['password'];
        $passwordConfirmed = $request['password_confirmed'];


        $status = false;
        $client = null;

        $data = array();

        if (empty($nom) || empty($prenom) || empty($sexe) || empty($phone) || empty($dob) || empty($email) || empty($password) || empty($passwordConfirmed)) {
            $message = 'Les champs sont obligatoires';
        } else {
            if ($password != $passwordConfirmed)
                $message = 'Les mots de passe ne concordent pas';
            else {
                $client = Utilisateur::where('email', $email)->first();

                if ($client != null)
                    $message = "Cette adresse email est deja utilisé";
                else {
                    $fullname = $prenom . ' ' . $nom;

                    $client = new Utilisateur();
                    $client->nom = $nom;
                    $client->prenom = $prenom;
                    $client->email = $email;
                    $client->phone = $phone;
                    $client->sexe = $phone;
                    $client->dob = $dob;
                    $client->password = bcrypt($password);
                    $client->status = false;
                    $client->save();

                    $code = new Code();
                    $code->code = rand(10000, 99999);
                    $code->utilisateur_id = $client->id;
                    $code->email = $client->email;
                    $code->save();

                    $objet = "Activation de compte";
                    $greeting = "Bonjour " . $fullname;
                    $content = "Votre code de confirmation est: " . $code->code;

                    Mail::to($email)
                        ->send(new NotificationEmail($greeting, $objet, $content));

                    $data = [
                        'utilisateur' => $client->only(['id', 'nom', 'prenom', 'phone', 'email', 'dob', 'sexe'])
                    ];

                    return response::success($data);
                }
            }

            return response::error($message, \Illuminate\Http\Response::HTTP_BAD_REQUEST);

        }

        return response::error($message, \Illuminate\Http\Response::HTTP_BAD_REQUEST);

    }

    public function codeConfirmation(Request $request)
    {
        $email = $request['email'];
        $code_confirmation = $request['code_confirmation'];


        $codeConfirmation = Code::where('email', $email)->where('code', $code_confirmation)
            ->orderByDesc('id')
            ->first();

        if ($codeConfirmation != null) {
            $utilisateur = $codeConfirmation->utilisateur;
            $utilisateur->status = true;
            $utilisateur->save();

            $data = [
                'utilisateur' => $utilisateur->only(['id', 'nom', 'prenom', 'phone', 'email', 'dob', 'sexe'])
            ];

            return response::success($data);
        }

        return response::error("Ce code de confirmation n'est pas correct", \Illuminate\Http\Response::HTTP_BAD_REQUEST);


    }

    public function codePasswordUpdate(Request $request)
    {

        $response = "Failed";
        if (isset($request['email'])) {

            $client = Utilisateur::where('email', $request['email'])->first();

            if ($client != null) {
                $title = "Mise à jour du mot de passe";
                $content = "Vous trouverez ci-dessous votre code de confirmation pour mettre à jour votre mot de passe, sachez qu'il expirera dans les 15 prochaines minutes.";

                $this->sendEmailCodeConfirmation($request['email'], $title, $content);

                $response = "Success";
            }
        }

        return response()->json($response);

    }

    public function updatePassword(Request $request)
    {
        $response = "Failed";
        if (isset($request['email']) && isset($request['password'])) {
            $email = $request['email'];
            $password = $request['password'];


            $client = Utilisateur::where('email', $email)->first();

            if ($client != null) {
                $client->password = bcrypt($password);
                $client->save();

                $response = "Success";
            }
        }

        return response()->json($response);

    }

    public function sendEmailCodeConfirmation($email, $title, $content)
    {
        $code = rand(10000, 99999);

        $codeSms = new Code();
        $codeSms->code = $code;
        $codeSms->email = $email;
        $codeSms->date = Carbon::now();

        $codeSms->save();

        Mail::to($email)
            ->send(new SendCodeEmail($title, $content, $code));
    }


}
