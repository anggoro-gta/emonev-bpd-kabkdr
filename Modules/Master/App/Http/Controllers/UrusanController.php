<?php

namespace Modules\Master\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Master\Models\MsSKPDUnit;
use Modules\Master\Models\MsUrusan;

class UrusanController extends Controller
{

    private $type_menu;
    public function __construct()
    {
        $this->type_menu = 'master';
    }
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        // $this->authorize('master.urusan.read');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'urusan' => MsUrusan::filter()->paginate(10)
        ];
        return view('master::urusan.index', compact('data'));
    }


    // public function datatable(Request $request)
    // {
    //     $data = User::all();
    //     return DataTables::of($data)->addColumn('action', function ($data) {
    //             $editButton = '';
    //             $deleteButton = '';
    //             if (request()->user()->can('layanan.update'))
    //                 $editButton = '<a href="' . route('master.urusan.edit', $data->Id) . '" class="mb-2 mr-2 btn btn-warning btn-sm" title="Ubah" title-pos="up"><i class="icon-feather-edit-2"></i></a>';
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
        // $this->authorize('master.urusan.create');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'action' => route('master.urusan.store'),
            'method' => 'POST',
        ];
        return view('master::urusan.form', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $urusan = MsUrusan::create([
            'kode_urusan' => $request->kode_urusan,
            'nama_urusan' => $request->nama_urusan,
        ]);
        return redirect(route('master.urusan.index'))
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
        // $this->authorize('master.urusan.update');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'urusan' => MsUrusan::find($id),
            'action' => route('master.urusan.update', $id),
            'method' => 'PUT',
        ];
        return view('master::urusan.form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $urusan = MsUrusan::find($id);
        $urusan->kode_urusan = $request->kode_urusan;
        $urusan->nama_urusan = $request->nama_urusan;
        $urusan->save();
        return redirect(route('master.urusan.index'))
            ->with('flash_message', "Data berhasil disimpan")
            ->with('flash_type', 'primary');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $this->authorize('master.urusan.destroy');
        MsUrusan::find($id)->delete();
        return redirect(route('master.urusan.index'))
            ->with('flash_message', "Data berhasil dihapus")
            ->with('flash_type', 'primary');
    }
}
