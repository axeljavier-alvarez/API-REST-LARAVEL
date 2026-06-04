<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\PermissionResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller implements HasMiddleware
{

    public static function middleware(): array
        {
            return [
                new Middleware('auth:api'),
            ];
        }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
                Gate::authorize('permissions.index', 'api');


        $permissions = Permission::all();
        return PermissionResource::collection($permissions);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        Gate::authorize('permissions.store', 'api');


        $data = $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        $data['guard_name'] = 'api';

         $permission = Permission::create($data); 
        // return new PermissionResource($permission);
        return PermissionResource::make($permission);

    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
                Gate::authorize('permissions.show', 'api');

        return PermissionResource::make($permission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        Gate::authorize('permissions.update', 'api');


        $data = $request->validate([
           'name' => 'required|unique:permissions,name,'.$permission->id,

        ]);

        $permission->update($data);
        return PermissionResource::make($permission);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {

        Gate::authorize('permissions.destroy', 'api');


        $permission->delete();

        return response()->json([
            'message' => 'Permission eliminada correctamente'
        ]);

    }
}
