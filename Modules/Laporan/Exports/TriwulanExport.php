<?php
namespace Modules\Laporan\Exports;

use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class TriwulanExport implements FromView
{
    protected $request;
    protected $data;
    use Exportable;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function view(): View
    {
        $data = $this->data;
        return view('laporan::triwulan.cetak2', compact('data'));
    }
}