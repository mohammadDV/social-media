<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\TableRequest;
use App\Http\Requests\ChatMessageRequest;
use App\Http\Requests\ChatRequest;
use App\Http\Requests\ChatStatusRequest;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IChatRepository.
 */
interface IChatRepository  {

    /**
     * Get the tikets pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator;

    /**
     * Store the chat.
     * @param ChatRequest $request
     * @param User $user
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(ChatRequest $request, User $user) :JsonResponse;

    /**
     * Change status of the chat
     * @param ChatStatusRequest $request
     * @param Chat $chat
     * @return JsonResponse
     */
    public function deleteMessages(Chat $chat) :JsonResponse;

    /**
    * Get the chat.
    * @param Chat $chat
    * @return Chat
    */
   public function chatInfo(Chat $chat) :Chat;

    /**
     * Get the messages of the chat.
     * @param TableRequest $request
     * @param Chat $chat
     * @return LengthAwarePaginator
     */
    public function show(TableRequest $request, Chat $chat) :LengthAwarePaginator;

}
