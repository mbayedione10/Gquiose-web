<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Alerte;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class APIAlertController extends Controller
{
    public function  sync(Request $request)
    {
        if (!isset($request['user_id']) &&  !isset($request['type']))
        {
            return ApiResponse::error("les champs sont obligatoires", Response::HTTP_BAD_REQUEST);
        }

        $user = Utilisateur::whereId($request['user_id'])->first();

        if ($user == null)
            return ApiResponse::error("Cet utilisateur n'existe pas");


        $types = [ "Mutilation génitale", "Viol", "Mariage précoce"];

        if (!in_array($request['type'], $types))
            return ApiResponse::error("Le type n'est correct", Response::HTTP_BAD_REQUEST);


        $alerte = new Alerte();
        $alerte->ref = uniqid();
        $alerte->utilisateur_id = $user->id;
        $alerte->type = $request['type_id'];
        $alerte->etat = "Non approvée";
        $alerte->save();

        return ApiResponse::success();

    }
}
