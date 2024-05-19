<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
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
        $this->authorize('setting.user.read');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'users' => User::filter()->paginate(10)
        ];
        return view('setting::user.index', compact('data'));
    }


    // public function datatable(Request $request)
    // {
    //     $data = User::all();
    //     return DataTables::of($data)->addColumn('action', function ($data) {
    //             $editButton = '';
    //             $deleteButton = '';
    //             if (request()->user()->can('layanan.update'))
    //                 $editButton = '<a href="' . route('setting.user.edit', $data->Id) . '" class="mb-2 mr-2 btn btn-warning btn-sm" title="Ubah" title-pos="up"><i class="icon-feather-edit-2"></i></a>';
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
        $this->authorize('setting.user.create');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'action' => route('setting.user.store'),
            'method' => 'POST'
        ];
        return view('setting::user.form', compact('data'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
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
        $this->validator($request->all())->validate();
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return redirect(route('setting.user.index'))
            ->with('flash_message', "Data berhasil disimpan")
            ->with('flash_type', 'primary');
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
         $this->authorize('setting.user.update');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'user' => User::find($id),
            'action' => route('setting.user.update',$id),
            'method' => 'PUT'
        ];
        return view('setting::user.form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = $request->password;
        }
        $user->save();
        return redirect(route('setting.user.index'))
            ->with('flash_message', "Data berhasil disimpan")
            ->with('flash_type', 'primary');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorize('setting.user.destroy');
        User::find($id)->delete();
        return redirect(route('setting.user.index'))
            ->with('flash_message', "Data berhasil dihapus")
            ->with('flash_type', 'primary');
    }
    function assignRole($id) {

        $role = Role::get();
        $groupedRoles = $role->split(ceil($role->count() / 4));
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'user' => User::find($id),
            'action' => route('setting.user.assign-role',$id),
            'method' => 'PUT',
            'roles' => $role,
            'groupedRoles'=> $groupedRoles,
        ];
        return view('setting::user.assign-role', compact('data'));
    }
    function updateRole(Request $request,$id) {
        $user = User::find($id);
        if ($user->roles->isEmpty() == false) {
            foreach ($user->roles as $role) {
                $user->removeRole($role);
            }
        }
        $user->assignRole($request->roles);
        return redirect(route('setting.user.index'))
            ->with('flash_message', 'User successfully edited.')
            ->with('flash_type', 'success');
    }

}
