<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\ChatRequest;
use App\Http\Requests\ChatStatusRequest;
use App\Http\Requests\TableRequest;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface IChatRepository.
 */
interface IChatRepository
{
    /**
     * Get the tikets pagination.
     */
    public function indexPaginate(TableRequest $request): LengthAwarePaginator;

    /**
     * Store the chat.
     *
     * @throws \Exception
     */
    public function store(ChatRequest $request, User $user): JsonResponse;

    /**
     * Change status of the chat
     *
     * @param  ChatStatusRequest  $request
     */
    public function deleteMessages(Chat $chat): JsonResponse;

    /**
     * Get the chat.
     */
    public function chatInfo(Chat $chat): Chat;

    /**
     * Get the messages of the chat.
     */
    public function show(TableRequest $request, Chat $chat): LengthAwarePaginator;
}
