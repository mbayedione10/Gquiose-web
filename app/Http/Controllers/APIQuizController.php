<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Response;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

class APIQuizController extends Controller
{
    public function sync(Request $request)
    {
        $user = Utilisateur::findOrFail($request['user_id']);

        $data = json_decode($request['responses'], true);

        for ($i = 0; $i < count($data); $i++)
        {
            $question = Question::whereId($data[$i]['questionId'])->first();


            if ($question != null)
            {
                $reponse = new Response();
                $reponse->question_id = $question->id;
                $reponse->utilisateur_id = $request['user_id'];
                $reponse->reponse = $data[$i]['reponse'];
                $reponse->isValid = $data[$i]['valid'];
                $reponse->save();
            }
        }

        return \response()->json("SUCCESS");
    }
}
