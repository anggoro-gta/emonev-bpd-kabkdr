<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Master\Models\MsSKPDUnit;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $unit = MsSKPDUnit::all();
        foreach ($unit as $key => $value) {
            $user  = User::create([
                'fk_skpd_unit_id' => $value->id,
                'name' => $value->nama_unit,
                'email' => $value->username.'@sgmail.com',
                'username' => $value->username,
                'password' => bcrypt('password'),
            ]);
            $user->assignRole(2);
        }
        return view('pages.dashboard-general-dashboard', ['type_menu' => 'dashboard']);
    }
}
