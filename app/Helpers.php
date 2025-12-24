<?php
use Illuminate\Support\Collection;

if (! function_exists('realisasiProgram')) {
    function realisasiProgram(Collection $r_program, $programId, $triwulan): array
    {
        $filtered = $r_program
            ->where('fk_program_id', $programId)
            ->where('triwulan', $triwulan);

        $rp = $filtered->first(); // bisa null

        return [
            'volume_realisasi' => (float) ($rp->volume_realisasi ?? 0),
            'satuan_prog'      => $rp->satuan_prog ?? null,
            'k'                => $filtered->pluck('keterangan')->filter()->implode(';'),
            'vArray'           => $filtered->pluck('volume_prog')->all(),
            'rArray'           => $filtered->pluck('volume_realisasi')->all(),
        ];
    }
}

if (! function_exists('realisasiAnggaranProgram')) {
    function realisasiAnggaranProgram($r_sub_kegiatan, $programId, $triwulan)
    {
        return $r_sub_kegiatan->where('fk_program_id', $programId)->where('triwulan', $triwulan)->sum('anggaran_realisasi');
    }
}
if (! function_exists('realisasiKegiatan')) {
    function realisasiKegiatan(Collection $r_kegiatan, $kegiatanId, $triwulan): array
    {
        $filtered = $r_kegiatan
            ->where('fk_kegiatan_id', $kegiatanId)
            ->where('triwulan', $triwulan);

        $rk = $filtered->first(); // bisa null

        return [
            'volume_realisasi' => (float) ($rk->volume_realisasi ?? 0),
            'satuan_kegiatan'  => $rk->satuan_kegiatan ?? null,
            'k'                => $filtered->pluck('k')->filter()->implode(';'),
            'vArray'           => $filtered->pluck('k')->all(),
            'rArray'           => $filtered->pluck('volume_realisasi')->all(),
        ];
    }
}
if (! function_exists('realisasiAnggaranKegiatan')) {
    function realisasiAnggaranKegiatan($r_sub_kegiatan, $kegiatanId, $triwulan)
    {
        return $r_sub_kegiatan->where('fk_kegiatan_id', $kegiatanId)->where('triwulan', $triwulan)->sum('anggaran_realisasi');
    }
}

if (! function_exists('getRowData')) {
    function getRowData($data = null)
    {
        if ($data==null) {
            return '';
        }
        if ( $data['type'] == 'persentase') {
           return number_format($data['value'], 2, '.', ''). ' %';
        }
        return $data['type'] == 'int' ? number_format($data['value']) : $data['value'];
    }
}
if (! function_exists('isNumberData')) {
    function isNumberData($data = null)
    {
        if ($data==null) {
            return '';
        }
        return  in_array($data['type'], ['persentase','int'])? true : false;
    }
}

if (! function_exists('getPredikat')) {
    function getPredikat($number)
    {
        if ($number >= 91) {
            return 'ST';
        } elseif ($number >=76) {
            return 'T';
        }elseif ($number >=66) {
            return 'S';
        }elseif ($number >=51) {
            return 'R';
        } else {
            return 'SR';
        }
    }
}
function TglIndo($date, $lc = 'IND')
{
    if ($date != null) {
        $dt = new  \Carbon\Carbon($date);
        setlocale(LC_TIME, $lc);
        return $dt->formatLocalized('%e %B %Y'); // Senin, 3 September 2018
    } else {
        return null;
    }
}