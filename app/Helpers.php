<?php

if (! function_exists('realisasiProgram')) {
    function realisasiProgram($r_program, $programId, $triwulan)
    {

        if ($r_program->where('fk_program_id', $programId)->where('triwulan', $triwulan)->first() != null) {
            $rp = $r_program->where('fk_program_id', $programId)->where('triwulan', $triwulan)->first();
        }
        return [
            'volume_realisasi' => $rp->volume_realisasi ?? 0,
            'satuan_prog' => $rp->satuan_prog ?? null
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
    function realisasiKegiatan($r_kegiatan, $kegiatanId, $triwulan)
    {

        if ($r_kegiatan->where('fk_kegiatan_id', $kegiatanId)->where('triwulan', $triwulan)->first() != null) {
            $rk = $r_kegiatan->where('fk_kegiatan_id', $kegiatanId)->where('triwulan', $triwulan)->first();
        };
        return [
            'volume_realisasi' => $rk->volume_realisasi ?? 0,
            'satuan_kegiatan' => $rk->satuan_kegiatan ?? null
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