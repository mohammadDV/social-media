<?php

namespace App\Repositories;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\Contracts\IUserRepository;
use App\Repositories\traits\GlobalFunc;
use App\Services\File\FileService;
use App\Services\Image\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository implements IUserRepository {

    use GlobalFunc;

    /**
     * @param ImageService $imageService
     * @param FileService $fileService
     */
    public function __construct(protected ImageService $imageService, protected FileService $fileService)
    {

    }

    /**
     * Get the live.
     * @return LengthAwarePaginator
     */
    public function indexPaginate() :LengthAwarePaginator
    {
        return User::query()
            ->orderBy('id', 'DESC')
            ->paginate(10);
    }

    /**
     * Get the user.
     * @return User
     */
    public function show() :User
    {
        return User::query()
            ->where('id', Auth::user()->id)
            ->first();
    }

    /**
     * Store the user.
     * @param UserRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(UserRequest $request) :JsonResponse
    {
        $this->checkLevelAccess();

        $existUser = User::query()
                        ->where('email', $request->input('email'))
                        ->first();
        if ($existUser) {
            return response()->json([
                'status' => 0,
                'message' => __('site.Duplicate email error')
            ], Response::HTTP_BAD_REQUEST);
        }

        $prImageResult = "";
        $bgImageResult = "";
        if ($request->hasFile('profile_photo_path')){
            $this->imageService->setExclusiveDirectory('uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'users');
            $prImageResult = $this->imageService->save($request->file('profile_photo_path'));
        }
        if ($request->hasFile('bg_photo_path')){
            $this->imageService->setExclusiveDirectory('uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'users');
            $bgImageResult = $this->imageService->save($request->file('bg_photo_path'));
        }

        $user = User::create([
            'first_name'            => $request->input('first_name'),
            'last_name'             => $request->input('last_name'),
            'nickname'              => $request->input('nickname'),
            'role_id'               => 1,
            'email'                 => $request->input('email'),
            'password'              => Hash::make($request->input('password')),
            'status'                => $request->input('status'),
            'mobile'                => $request->input('mobile'),
            'national_code'         => $request->input('national_code'),
            'biography'             => $request->input('biography'),
            'profile_photo_path'    => $prImageResult,
            'bg_photo_path'         => $bgImageResult,
        ]);

        if ($user) {
            return response()->json([
                'status' => 1,
                'message' => __('site.New user has been stored')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();
    }

    /**
     * Update the password of user.
     * @param UpdatePasswordRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request, User $user) :JsonResponse
    {

        if (empty($request->input('current_password')) || ! Hash::check($request->input('current_password'), auth()->user()->password)) {

            return response()->json([
                'status' => 0,
                'message' => __('The provided password does not match your current password.')
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = User::query()
            ->where('id' , Auth::user()->id)
            ->update([
                'password' => Hash::make($request->input('password')),
            ]);


        if ($user) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The password of user has been changed')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();

    }

    /**
     * Update the user.
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user) :JsonResponse
    {

        $this->checkLevelAccess($user->id == Auth::user()->id);

        $prImageResult = $user->profile_photo_path;

        $url = 'uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'users';
        if ($request->hasFile('profile_photo_path')){
            $prImageResult = $this->uploadImage($this->imageService, $request->file('profile_photo_path'), $url, $user->profile_photo_path);
        }

        $bgImageResult = $user->bg_photo_path;

        if ($request->hasFile('bg_photo_path')){
            $bgImageResult = $this->uploadImage($this->imageService, $request->file('bg_photo_path'), $url, $user->bg_photo_path);
        }

        $update = $user->update([
            'first_name'            => $request->input('first_name'),
            'last_name'             => $request->input('last_name'),
            'nickname'              => $request->input('nickname'),
            'status'                => $request->input('status'),
            'mobile'                => $request->input('mobile'),
            'national_code'         => $request->input('national_code'),
            'biography'             => $request->input('biography'),
            'profile_photo_path'    => $prImageResult,
            'bg_photo_path'         => $bgImageResult,
        ]);

        if ($update) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The data has been updated')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
    }
}
