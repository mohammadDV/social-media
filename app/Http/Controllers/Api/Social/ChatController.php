<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChatRequest;
use App\Http\Requests\TableRequest;
use App\Models\Chat;
use App\Models\User;
use App\Repositories\Contracts\IChatRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ChatController extends Controller
{
    /**
     * Constructor of ChatController.
     */
    public function __construct(protected  IChatRepository $repository)
    {
        //
    }

    /**
     * Get all of chats with pagination
     * @param TableRequest $request
     * @return JsonResponse
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get the chat.
     * @param Chat $chat
     * @return JsonResponse
     */
    public function chatInfo(TableRequest $request, Chat $chat) :JsonResponse
    {
        return response()->json($this->repository->chatInfo($chat), Response::HTTP_OK);
    }

    /**
     * Get the message of the chat.
     * @param TableRequest $request
     * @param Chat $chat
     * @return JsonResponse
     */
    public function show(TableRequest $request, Chat $chat) :JsonResponse
    {
        return response()->json($this->repository->show($request, $chat), Response::HTTP_OK);
    }

    /**
     * Delete messages
     * @param Chat $chat
     * @return JsonResponse
     */
    public function deleteMessages(Chat $chat) :JsonResponse
    {
        return $this->repository->deleteMessages($chat);
    }

    /**
     * Store the chat.
     * @param ChatRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function store(ChatRequest $request, User $user) :JsonResponse
    {
        return $this->repository->store($request, $user);
    }

    /**
     * Delete the chat.
     * @param Chat $chat
     * @return JsonResponse
     */
    public function destroy(Chat $chat)
    {
        // return $this->repository->destroy($chat);
    }
}
