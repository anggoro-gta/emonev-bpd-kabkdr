<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PermissionController extends Controller
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
         $this->authorize('setting.permission.read');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'permissions' => Permission::filtered()->paginate()
        ];
        return view('setting::permission.index', compact('data'));
    }


    // public function datatable(Request $request)
    // {
    //     $data = User::all();
    //     return DataTables::of($data)->addColumn('action', function ($data) {
    //             $editButton = '';
    //             $deleteButton = '';
    //             if (request()->user()->can('layanan.update'))
    //                 $editButton = '<a href="' . route('setting.permission.edit', $data->Id) . '" class="mb-2 mr-2 btn btn-warning btn-sm" title="Ubah" title-pos="up"><i class="icon-feather-edit-2"></i></a>';
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
         $this->authorize('setting.permission.create');

        $roles = Role::orderBy('name')->get();
        $groupedRoles = $roles->split(ceil($roles->count() / 4));
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'action' => route('setting.permission.store'),
            'method' => 'POST',
            'roles' => $roles,
            'groupedRoles' => $groupedRoles
        ];
        return view('setting::permission.form', compact('data'));
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
            'name' => 'required|max:50',
        ]);

        $name = $request['name'];
        $permission = new Permission();
        $permission->name = $name;

        $roles = $request['roles'];

        $permission->save();

        if (!empty($request['roles'])) {
            foreach ($roles as $role) {
                $r = Role::where('id', '=', $role)->firstOrFail(); //Match input role to db record

                $permission = Permission::where('name', '=', $name)->first();
                $r->givePermissionTo($permission);
            }
        }

        return redirect()->route('setting.permission.index')
            ->with('flash_message', 'Permission' . $permission->name . ' added!')
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
         $this->authorize('setting.permission.update');
        $roles = Role::orderBy('name')->get();
        $groupedRoles = $roles->split(ceil($roles->count() / 4));
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'permission' => Permission::find($id),
            'action' => route('setting.permission.update',$id),
            'method' => 'PUT',
            'roles' => $roles,
            'groupedRoles' => $groupedRoles
        ];
        return view('setting::permission.form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $this->authorize('setting.permission.update');
        $permission = Permission::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|max:50',
        ]);

        $input = $request->only('name');
        $permission->fill($input)->save();
        DB::beginTransaction();
        try {
            $permission->syncRoles([]);
            $roles = $request['roles'];
            if (!empty($request['roles'])) {
                foreach ($roles as $role) {
                    $r = Role::where('id', '=', $role)->firstOrFail(); //Match input role to db record
                    $permission = Permission::where('name', '=', $input)->first();
                    $r->givePermissionTo($permission);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
        return redirect()->route('setting.permission.index')
            ->with('flash_message', 'Permission ' . $permission->name . ' telah diperbarui!')
            ->with('flash_type', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorize('setting.permission.destroy');
        $permission = Permission::findOrFail($id);
        $nama = $permission->name;

        if (substr($permission->name, 0, 5) == "user.") {
            return redirect()->route('setting.permission.index')
                ->with('flash_message', 'Cannot delete this Permission!');
        }

        $permission->delete();

        return redirect()->route('setting.permission.index')
            ->with('flash_message', "Permission {$nama} telah dihapus!")
            ->with('flash_type', 'warning');
    }
}
