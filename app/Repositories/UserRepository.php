<?php

namespace App\Repositories;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Club;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\IUserRepository;
use App\Repositories\traits\GlobalFunc;
use App\Services\File\FileService;
use App\Services\Image\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * Get the users
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(Request $request) :LengthAwarePaginator
    {
        $this->checkLevelAccess();

        $search = $request->get('query');
        return User::query()
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('nickname', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->when(Auth::user()->role_id != 1, function ($query) {
                $query->where('role_id', '!=', 1);
            })
            ->orderBy($request->get('sortBy', 'id'), $request->get('sortType', 'desc'))
            ->paginate($request->get('rowsPerPage', 25));

    }

    /**
     * Get the user.
     * @param ?User $user
     * @return UserResource
     */
    public function show(?User $user) :UserResource
    {
        return new UserResource(User::query()
            ->where('id', !empty($user->id) ? $user->id : Auth::user()->id)
            ->with('clubs')
            ->first());
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

        $role_id = $request->input('role_id', 4);

        if ($role_id == 1 && Auth::user()->role_id != 1) {
            throw New \Exception('Unauthorized', 403);
        }

        $user = User::create([
            'first_name'            => $request->input('first_name'),
            'last_name'             => $request->input('last_name'),
            'nickname'              => $request->input('nickname'),
            'role_id'               => $role_id,
            'level'                 => in_array($role_id, [1,3]) ? 3 : 1,
            'email'                 => $request->input('email'),
            'password'              => Hash::make($request->input('password')),
            'status'                => $request->input('status'),
            'mobile'                => $request->input('mobile'),
            'is_private'            => $request->input('is_private', false),
            'national_code'         => $request->input('national_code'),
            'biography'             => $request->input('biography'),
            'profile_photo_path'    => $request->input('profile_photo_path'),
            'bg_photo_path'         => $request->input('bg_photo_path'),
        ]);

        if ($user) {

            $role = Role::findOrFail($role_id);
            $user->syncRoles([$role->name]);

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

        $role_id = $request->input('role_id', 4);

        if ($role_id == 1 && Auth::user()->role_id != 1) {
            throw New \Exception('Unauthorized', 403);
        }

        if (!$this->checkNickname($request->input('nickname'), $user->id)) {
            return response()->json([
                'status' => 0,
                'message' => __('site.The Nickname is invalid')
            ], Response::HTTP_BAD_REQUEST);
        }

        $update = $user->update([
            'first_name'            => $request->input('first_name'),
            'last_name'             => $request->input('last_name'),
            'nickname'              => $request->input('nickname'),
            'role_id'               => $role_id,
            'level'                 => in_array($role_id, [1,3]) ? 3 : 1,
            'status'                => $request->input('status'),
            'is_private'            => $request->input('is_private', false),
            'mobile'                => $request->input('mobile'),
            'national_code'         => $request->input('national_code'),
            'biography'             => $request->input('biography'),
            'profile_photo_path'    => $request->input('profile_photo_path', config('image.default-profile-image')),
            'bg_photo_path'         => $request->input('bg_photo_path', config('image.default-background-image')),
        ]);

        if ($update) {

            $role = Role::findOrFail($role_id);
            $user->syncRoles([$role->name]);

            return response()->json([
                'status' => 1,
                'message' => __('site.The data has been updated')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
    }

    /**
    * Search users.
    * @param SearchRequest $request
    * @return LengthAwarePaginator|array
    */
   public function search(SearchRequest $request) :LengthAwarePaginator|array
   {
        $search = $request->get('search');

        if (empty($search)) {
            return [];
        }

        $data['users'] = User::query()
            ->where('level', '!=', 3)
            ->where('status', 1)
            ->where('is_report', 0)
            ->whereDoesntHave('blocked', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })
            ->where(function ($query) use ($search) {
                return $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('nickname', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        $data['clubs'] = Club::query()
            ->where('status', 1)
            ->where(function ($query) use ($search) {
                return $query->where('title', 'like', '%' . $search . '%');
            })
            ->with('sport')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return $data;
   }

    /**
    * Report the user.
    * @param User $user
    * @return JsonResponse
    */
    public function destroy(User $user) :JsonResponse
    {
        $this->checkLevelAccess();

        $user->update([
            'is_report' => 1
        ]);

        if ($user) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
    }
}
