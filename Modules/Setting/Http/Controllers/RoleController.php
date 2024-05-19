<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{

    private $type_menu;
    public function __construct()
    {
        $this->type_menu = 'setting';
    }
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
         $this->authorize('setting.role.read');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'roles' => Role::whereNull('deleted_at')
            ->filtered()
            ->with('permissions')
            ->paginate()
        ];
        return view('setting::role.index', compact('data'));
    }


    // public function datatable(Request $request)
    // {
    //     $data = User::all();
    //     return DataTables::of($data)->addColumn('action', function ($data) {
    //             $editButton = '';
    //             $deleteButton = '';
    //             if (request()->user()->can('layanan.update'))
    //                 $editButton = '<a href="' . route('setting.role.edit', $data->Id) . '" class="mb-2 mr-2 btn btn-warning btn-sm" title="Ubah" title-pos="up"><i class="icon-feather-edit-2"></i></a>';
    //             if (request()->user()->can('layanan.delete'))
    //                 $deleteButton = '<a data-id="' . $data->Id . ' " data-url="/layanan/' . $data->Id . ' " class="mb-2 mr-2 btn btn-danger btn-sm deleteData" data-title ="Data" title="Hapus" title-pos="up"><i class="icon-feather-trash-2"></i></a>';
    //             return '<span class="btn-group" role="group">' . $editButton . $deleteButton . '</span>';
    //         })->rawColumns(['action', 'NoTicket', 'StatusLayanan', 'PermintaanLayanan', 'TglLayanan', 'PermintaanLayanan2'])->make(true);
    // }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $this->authorize('setting.role.create');

        $permissions = Permission::all();
        $groupedPermissions = $permissions->groupBy(function ($item, $key) {
            $dots = explode('.', $item->name);
            return $dots[0];
        });
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'action' => route('setting.role.store'),
            'method' => 'POST',
            'permissions' => $permissions,
            'groupedPermissions' => $groupedPermissions
        ];
        return view('setting::role.form', compact('data'));
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function createInput(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles',
            // 'permissions' => 'required',
        ]);

        $name = $request['name'];
        $role = new Role();
        $role->name = $name;

        $permissions = $request['permissions'];

        $role->save();

        foreach ($permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail();
            $role = Role::where('name', '=', $name)->first();
            $role->givePermissionTo($p);
        }

        return redirect()->route('setting.role.index')
            ->with('flash_message', 'Role ' . $role->name . ' added!')
            ->with('flash_type', 'success');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('setting::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
         $this->authorize('setting.role.update');
        $permissions = Permission::all();
        $groupedPermissions = $permissions->groupBy(function ($item, $key) {
            $dots = explode('.', $item->name);
            return $dots[0];
        });
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'role' => Role::find($id),
            'action' => route('setting.role.update',$id),
            'method' => 'PUT',
            'permissions' => $permissions,
            'groupedPermissions' => $groupedPermissions
        ];
        return view('setting::role.form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  Role $role)
    {
        $this->authorize('setting.role.update');
        $this->validate($request, [
            'name' => 'required|unique:roles,name,' . $role->id,
            // 'permissions' => 'required',
        ]);

        $input = $request->except(['permissions']);
        $permissions = $request['permissions'];
        $role->fill($input)->save();
        $p_all = Permission::all();
        foreach ($p_all as $p) {
            $role->revokePermissionTo($p);
        }
        if ($permissions) {
            foreach ($permissions as $permission) {
                $p = Permission::where('id', '=', $permission)->firstOrFail(); //Get corresponding form permission in db
                $role->givePermissionTo($p);
            }
        }

        return redirect()->route('setting.role.index')
            ->with('flash_message', 'Role ' . $role->name . ' telah diperbarui')
            ->with('flash_type', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {

        $this->authorize('setting.role.destroy');
        $nama = $role->name;
        $role->deleted_at = now();
        $role->save();

        return redirect()->route('setting.role.index')
            ->with('flash_message', "Role {$nama} telah dihapus!")
            ->with('flash_type', 'warning');
    }
}
