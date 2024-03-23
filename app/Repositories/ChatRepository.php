<?php

namespace App\Repositories;

use App\Http\Requests\TableRequest;
use App\Http\Requests\ChatRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Block;
use App\Models\Sport;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\IChatRepository;
use App\Repositories\traits\GlobalFunc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ChatRepository implements IChatRepository {

    use GlobalFunc;

    /**
     * Get the sports pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator
    {
        $search = $request->get('query');
        return Chat::query()
            ->with('user', 'target')
            ->withCount(['messages' => function ($query) {
                $query->where(function($query) {
                    $query->where('status', ChatMessage::STATUS_PENDING)
                        ->where('user_id', '<>', Auth::user()->id);
                })
                ->Where(function($query) {
                    $query->where('remover_id', '<>', Auth::user()->id)
                        ->orWhereNull('remover_id');
                });
            }])
            ->where(function ($query) {
                return $query->where('user_id', Auth::user()->id)
                    ->orWhere('target_id', Auth::user()->id);
            })
            ->when(!empty($search), function ($query) use ($search) {
                $query->whereHas('user', function($query) use ($search) {
                    $query->where([
                        ['first_name', 'like', '%' . $search . '%'],
                        ['first_name', '<>', Auth::user()->first_name],
                    ])
                        ->orWhere(
                            [
                                ['last_name', 'like', '%' . $search . '%'],
                                ['last_name', '<>', Auth::user()->last_name],
                            ]
                        )
                        ->orWhere(
                            [
                                ['nickname', 'like', '%' . $search . '%'],
                                ['nickname', '<>', Auth::user()->nickname],
                            ]
                        )
                        ->orWhere([
                            ['email', 'like', '%' . $search . '%'],
                            ['email', '<>', Auth::user()->email],
                        ]);
                })
                ->orWhereHas('target', function ($query) use ($search) {
                    $query->where([
                        ['first_name', 'like', '%' . $search . '%'],
                        ['first_name', '<>', Auth::user()->first_name],
                    ])
                        ->orWhere(
                            [
                                ['last_name', 'like', '%' . $search . '%'],
                                ['last_name', '<>', Auth::user()->last_name],
                            ]
                        )
                        ->orWhere(
                            [
                                ['nickname', 'like', '%' . $search . '%'],
                                ['nickname', '<>', Auth::user()->nickname],
                            ]
                        )
                        ->orWhere([
                            ['email', 'like', '%' . $search . '%'],
                            ['email', '<>', Auth::user()->email],
                        ]);
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($request->get('rowsPerPage', 50));
    }

    /**
     * Get the chat.
     * @param Chat $chat
     * @return Chat
     */
    public function chatInfo(Chat $chat) :Chat
    {
        $this->checkLevelAccess(Auth::user()->id == $chat->user_id || Auth::user()->id == $chat->target_id);

        $chat = Chat::query()
            ->with('user', 'target')
            ->where('id', $chat->id)
            ->first();

        $user = Auth::user()->id == $chat->user_id ? $chat->target : $chat->user;
        $chat->block = Block::query()
            ->where('user_id', $user->id)
            ->where('blocker_id', Auth::user()->id)
            ->count() == 1;

        $chat->banned = $this->areBothBlocked($user);

        return $chat;
    }

    /**
     * Get the messages of the chat.
     * @param TableRequest $request
     * @param Chat $chat
     * @return LengthAwarePaginator
     */
    public function show(TableRequest $request, Chat $chat) :LengthAwarePaginator
    {

        $this->checkLevelAccess(Auth::user()->id == $chat->user_id || Auth::user()->id == $chat->target_id);

        ChatMessage::query()
            ->where('chat_id', $chat->id)
            ->where('status', ChatMessage::STATUS_PENDING)
            ->where('user_id', '<>', Auth::user()->id)
            ->update([
                'status' => ChatMessage::STATUS_READ
            ]);

        return ChatMessage::query()
                // ->with('chat.user', 'chat.target')
                ->where('chat_id', $chat->id)
                ->where(function ($query) {
                    $query->whereNull('remover_id')
                        ->orWhere('remover_id', '<>', Auth::user()->id);
                })
                ->orderBy('id', 'desc')
                ->paginate(15);
    }

    /**
     * Store the chat.
     * @param ChatRequest $request
     * @param User $user
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(ChatRequest $request, User $user) :JsonResponse
    {

        if ($this->areBothBlocked($user)) {
            return response()->json([
                'status' => 0,
                'message' => ''
            ]);
        }

        $chat = Chat::query()
            ->where([
                ['user_id', Auth::user()->id],
                ['target_id', $user->id],
            ])
            ->orWhere([
                ['target_id', Auth::user()->id],
                ['user_id', $user->id],
            ])
            ->orderBy('id', 'desc')
            ->first();

        if (!$chat) {
            $chat = Chat::create([
                'user_id'       => Auth::user()->id,
                'target_id'       => $user->id,
            ]);
        }

        $message = ChatMessage::create([
            'chat_id'    => $chat->id,
            'file'       => $request->input('file', null),
            'message'    => $request->input('message'),
            'user_id'    => Auth::user()->id,
        ]);

        $chat->touch();

        if ($message) {

            cache()->remember(
                'notification.chat.user' . Auth::user()->id . '.' . $user->id,
                now()->addMinutes(1),
                function () use($user, $chat) {
                    // Add notification
                    return Notification::create([
                        'message' => __('site.Someone sent a private message to you.', ['someone' => Auth::user()->nickname]),
                        'link' => '/profile/chats/' . $chat->id,
                        'user_id' => $user->id,
                        'model_id' => Auth::user()->id,
                        'model_type' => User::class,
                        'has_email' => 1,
                    ]);
                });


            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();
    }

    /**
     * Change status of the chat
     * @param Chat $chat
     * @return JsonResponse
     */
    public function deleteMessages(Chat $chat) :JsonResponse
    {
        $this->checkLevelAccess(Auth::user()->id == $chat->user_id || Auth::user()->id == $chat->target_id);

        $removerId = $chat->target_id;

        if (Auth::user()->id == $removerId) {
            $removerId = $chat->user_id;
        }

        ChatMessage::query()
            ->where('chat_id', $chat->id)
            ->where('remover_id', $removerId)
            ->delete();

        ChatMessage::query()
            ->where('chat_id', $chat->id)
            ->update([
                'remover_id' => Auth::user()->id
            ]);


        return response()->json([
            'status' => 1,
            'message' => __('site.The operation has been successfully')
        ], Response::HTTP_OK);

    }

    /**
    * Delete the sport.
    * @param UpdatePasswordRequest $request
    * @param Sport $sport
    * @return JsonResponse
    */
   public function destroy(Sport $sport) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $sport->user_id);

        $sport->delete();

        if ($sport) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }
}
