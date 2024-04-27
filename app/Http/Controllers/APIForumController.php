<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Theme;
use App\Models\Utilisateur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class APIForumController extends Controller
{
    public function syncMessage(Request $request)
    {
        $themeId = $request['theme_id'];
        $utilisateurId = $request['utilisateur_id'];
        $question = $request['question'];

        if (!isset($themeId) || !isset($utilisateurId) || !isset($question))
            return ApiResponse::error("Les champs theme, utilisateur et questions sont obligatoires");

        $theme = Theme::where('id', $themeId)->first();

        if ($theme == null) return  ApiResponse::error("Aucun thème avec cet ID");

        $utilisateur = Utilisateur::where('id', $utilisateurId)->first();

        if ($utilisateur == null) return  ApiResponse::error("Aucun utilisteur avec cet ID");

        $message = new Message();
        $message->utilisateur_id = $utilisateur->id;
        $message->theme_id = $theme->id;
        $message->question = $question;
        $message->status = true;
        $message->save();

        $chat = new Chat();
        $chat->message = $message->question;
        $chat->utilisateur_id = $utilisateur->id;
        $chat->message_id = $message->id;
        $chat->save();

        return  ApiResponse::success($message);
    }


    public function syncChat(Request $request)
    {
        $utilisateurId = $request['utilisateur_id'];
        $messageId = $request['message_id'];
        $msg = $request['message'];

        if (!isset($messageId) || !isset($utilisateurId) || !isset($msg))
            return ApiResponse::error("Les champs messageId, utilisateur et message sont obligatoires");

        $message = Message::where('id', $messageId)->first();

        if ($message == null) return  ApiResponse::error("Aucun message avec cet ID");

        $utilisateur = Utilisateur::where('id', $utilisateurId)->first();

        if ($utilisateur == null) return  ApiResponse::error("Aucun utilisteur avec cet ID");

        $chat = new Chat();
        $chat->message_id = $messageId;
        $chat->utilisateur_id = $utilisateurId;
        $chat->message = $msg;
        $chat->status = true;

        $chat->save();



        return  ApiResponse::success($chat);
    }


    public function forum()
    {
        $messages = DB::table('messages')
            ->join('themes', 'messages.theme_id', 'themes.id')
            ->join('utilisateurs', 'messages.utilisateur_id', 'utilisateurs.id')
            ->select('messages.id as id', 'messages.question', 'utilisateurs.id as utilisateurId',
            'utilisateurs.prenom as utilisateur', 'themes.id as themeId', 'themes.name as theme', 'messages.created_at as date', 'messages.status as status')
            ->get();


        $chats = DB::table('chats')
            ->join('utilisateurs', 'chats.utilisateur_id', 'utilisateurs.id')
            ->join('messages', 'chats.message_id', 'messages.id')
            ->select('chats.id as id', 'chats.message as message',
                'chats.message_id as messageId',
                'chats.utilisateur_id as utilisateurId',
                'utilisateurs.prenom as utilisateurName',
                'chats.created_at as date',
                'chats.status as status',
            )
            ->get();


        $data = [
            'messages' => $messages,
            'chats' => $chats,
        ];

        return ApiResponse::success($data);
    }
}
