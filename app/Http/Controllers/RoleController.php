<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleTypeRequest;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\ArticleTypeResource;
use App\Http\Resources\RoleDetailsResource;
use App\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $roles = Role::all()->sortByDesc('created_at');
        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RoleRequest $request
     * @return JsonResponse|object
     */
    public function store(RoleRequest $request)
    {
        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
            $permission_ids = [];
            array_map(function ($permission) use (&$permission_ids) {
                $permission_id = $permission['id'];
                $permission_ids[] = $permission_id;
            }, $request->permissions);
            $permissions = Permission::whereIn('id', $permission_ids)->get();
            $role->syncPermissions($permissions);
            DB::commit();
            return (new RoleDetailsResource($role))
                ->additional(['message' => 'Role created.'])
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
     * @param Role $role
     * @return RoleDetailsResource
     */
    public function show(Role $role): RoleDetailsResource
    {
        return new RoleDetailsResource($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ArticleTypeRequest $request
     * @param Role $role
     * @return RoleDetailsResource
     */
    public function update(ArticleTypeRequest $request, Role $role): RoleDetailsResource
    {
        DB::beginTransaction();
        try {
            $role->update($request->all());
            $permission_ids = [];
            array_map(function ($permission) use (&$permission_ids) {
                $permission_id = $permission['id'];
                $permission_ids[] = $permission_id;
            }, $request->permissions);
            $permissions = Permission::whereIn('id', $permission_ids)->get();
            $role->syncPermissions($permissions);
            DB::commit();
            return (new RoleDetailsResource($role))
                ->additional(['message' => 'Role updated.']);
        } catch (\Exception $e) {
            DB::rollback();
            throw new BadRequestException($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Role $role
     * @return JsonResponse
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();
        return response()->json(['message' => 'Role removed.'], 200);
    }
}
