<?php

namespace App\Http\Controllers;

use App\Events\MessageReplied;
use App\Events\UserMentioned;
use App\Http\Responses\ApiResponse;
use App\Models\Censure;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Theme;
use App\Models\Utilisateur;
use App\Services\MentionDetector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class APIForumController extends Controller
{
    public function syncMessage(Request $request)
    {
        $themeId = $request['theme_id'];
        $utilisateurId = $request['utilisateur_id'];
        $question = $request['question'];

        if (! isset($themeId) || ! isset($utilisateurId) || ! isset($question)) {
            return ApiResponse::error('Les champs theme, utilisateur et questions sont obligatoires');
        }

        $theme = Theme::where('id', $themeId)->first();

        if ($theme == null) {
            return ApiResponse::error('Aucun thème avec cet ID');
        }

        $utilisateur = Utilisateur::where('id', $utilisateurId)->first();

        if ($utilisateur == null) {
            return ApiResponse::error('Aucun utilisteur avec cet ID');
        }

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
        $chat->status = true;
        $chat->anonyme = false;
        $chat->save();

        // Detect and notify mentions in new message
        $mentions = app(MentionDetector::class)->extractMentions($question);
        foreach ($mentions as $mentionedUser) {
            event(new UserMentioned(
                $mentionedUser,
                $utilisateur,
                $question,
                'message',
                $message->id
            ));
        }

        return ApiResponse::success($message);
    }

    public function syncChat(Request $request)
    {
        $utilisateurId = $request['utilisateur_id'];
        $messageId = $request['message_id'];
        $msg = $request['message'];
        $anonyme = $request['anomyme'];

        if (! isset($messageId) || ! isset($utilisateurId) || ! isset($msg) || ! isset($anonyme)) {
            return ApiResponse::error('Les champs messageId, utilisateur et message sont obligatoires');
        }

        $censures = Censure::pluck('name')->toArray();

        if ($this->containsWord($censures, $msg)) {
            return ApiResponse::error('Ton message contient un ou plusieurs mots censuré');
        }

        $message = Message::where('id', $messageId)->first();

        if ($message == null) {
            return ApiResponse::error('Aucun message avec cet ID');
        }

        $utilisateur = Utilisateur::where('id', $utilisateurId)->first();

        if ($utilisateur == null) {
            return ApiResponse::error('Aucun utilisteur avec cet ID');
        }

        $chat = new Chat();
        $chat->message_id = $messageId;
        $chat->utilisateur_id = $utilisateurId;
        $chat->message = $msg;
        $chat->status = true;
        $chat->anonyme = $anonyme;

        $chat->save();

        // Trigger reply notification (only for non-anonymous replies)
        if (! $anonyme) {
            event(new MessageReplied($chat, $message));
        }

        // Detect and notify mentions (only for non-anonymous chats)
        if (! $anonyme) {
            $mentions = app(MentionDetector::class)->extractMentions($msg);
            foreach ($mentions as $mentionedUser) {
                event(new UserMentioned(
                    $mentionedUser,
                    $utilisateur,
                    $msg,
                    'chat',
                    $chat->id
                ));
            }
        }

        return ApiResponse::success($chat);
    }

    public function delete($id)
    {
        $chat = Chat::where('id', $id)->first();

        if ($chat == null) {
            return ApiResponse::error("Ce message n'existe pas");
        }

        $chat->delete();

        return ApiResponse::success($chat);
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
                'chats.anonyme as anomyme',
            )
            ->get();

        $data = [
            'messages' => $messages,
            'chats' => $chats,
        ];

        return ApiResponse::success($data);
    }

    public function containsWord($list, $word)
    {
        $cleanedWord = preg_replace('/[^a-zA-Z0-9]/', '', $word);
        $cleanedWord = strtolower($cleanedWord);

        foreach ($list as $item) {
            $cleanedItem = preg_replace('/[^a-zA-Z0-9]/', '', $item);
            $cleanedItem = strtolower($cleanedItem);
            if ($cleanedItem === $cleanedWord) {
                return true;
            }
        }

        return false;
    }
}
