<?php

if (! function_exists('realisasiProgram')) {
    function realisasiProgram($r_program, $programId, $triwulan)
    {

        if ($r_program->where('fk_program_id', $programId)->where('triwulan', $triwulan)->first() != null) {
            $rp = $r_program->where('fk_program_id', $programId)->where('triwulan', $triwulan)->first();
        }
        return [
            'k' => $rp->k ?? null,
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
            'k' => $rk->k ?? null,
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
