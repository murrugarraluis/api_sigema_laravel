<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserNotificationResource;
use App\Http\Resources\UserResource;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $users = User::withTrashed()->get()->sortByDesc('created_at');
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     * @return JsonResponse|object
     */
    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $employee = Employee::find($request->employee["id"]);
            $user = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            $role_ids = [];
            array_map(function ($role) use (&$role_ids) {
                $permission_id = $role['id'];
                $role_ids[] = $permission_id;
            }, $request->roles);
            $roles = Role::whereIn('id', $role_ids)->get();
            $user->assignRole($roles);
            $employee->user()->associate($user)->save();
            DB::commit();
            return (new UserResource($user))
                ->additional(['message' => 'User created.'])
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            DB::rollback();
            throw new BadRequestException($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);

    }
    public function show_notifications(User $user): AnonymousResourceCollection
    {
        return UserNotificationResource::collection($user->notifications);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $request
     * @param User $user
     * @return UserResource
     */
    public function update(UserUpdateRequest $request, User $user): UserResource
    {
        DB::beginTransaction();
        try {
            $role_ids = [];
            array_map(function ($role) use (&$role_ids) {
                $permission_id = $role['id'];
                $role_ids[] = $permission_id;
            }, $request->roles);
            $roles = Role::whereIn('id', $role_ids)->get();
            $user->syncRoles($roles);
            DB::commit();
            return (new UserResource($user))
                ->additional(['message' => 'User updated.']);
        } catch (\Exception $e) {
            DB::rollback();
            throw new BadRequestException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy($user): JsonResponse
    {
        $user = User::withTrashed()->find($user);
        if ($user->deleted_at){
            $user->restore();
            return response()->json(['message' => 'User Unlocked.']);
        }
        $user->delete();
        return response()->json(['message' => 'User Locked.']);
    }
}
