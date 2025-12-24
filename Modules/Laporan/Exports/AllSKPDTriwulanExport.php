<?php

namespace Modules\Laporan\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class AllSKPDTriwulanExport implements FromView, WithEvents
{
    use Exportable;

    protected $data;

    // sesuaikan sesuai file Anda
    protected int $headerStartRow = 4;
    protected int $headerEndRow   = 7; // data mulai row 8

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $data = $this->data;
        return view('laporan::triwulan.cetakAll', compact('data'));
    }

    private function argbFromHex(?string $hex): ?string
    {
        if (!$hex) return null;
        $hex = trim($hex);
        $hex = trim($hex, " ;");
        if ($hex === 'none') return null;

        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = "{$hex[0]}{$hex[0]}{$hex[1]}{$hex[1]}{$hex[2]}{$hex[2]}";
        }
        if (strlen($hex) !== 6) return null;

        return 'FF' . strtoupper($hex);
    }

    private function fillRow($sheet, string $highestCol, int $row, string $argb, bool $bold = false): void
    {
        $range = "A{$row}:{$highestCol}{$row}";
        $sheet->getStyle($range)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => $argb],
            ]
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $highestRow = $sheet->getHighestRow();
                $highestCol = $sheet->getHighestColumn();

                // ===== Freeze header (header tidak ikut geser) =====
                $firstDataRow = $this->headerEndRow + 1;
                $sheet->freezePane('A' . $firstDataRow);

                // ===== Border hanya untuk tabel, bukan title (row 1-3) =====
                $borderRange = 'A' . $this->headerStartRow . ':' . $highestCol . $highestRow;
                $sheet->getStyle($borderRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // ===== Wrap + vertical top untuk area tabel =====
                $sheet->getStyle($borderRange)->getAlignment()->setWrapText(true);
                $sheet->getStyle($borderRange)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

                // ===== Style header tabel =====
                $headerRange = 'A'.$this->headerStartRow.':'.$highestCol.$this->headerEndRow;
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // ====== KUSTOM WARNA PER TYPE + SKPD HEADER (1 sheet multi SKPD) ======

                // Ambil data sama seperti di blade
                $skpd            = $this->data->skpd ?? collect();
                $realisasiByUnit = $this->data->realisasiByUnit ?? collect();

                // default color kalau item tidak punya background-color
                $defaultTypeHex = [
                    'program'      => '#87d1eb',
                    'kegiatan'     => '#e7b763',
                    'sub_kegiatan' => null,
                ];

                $row = $firstDataRow;

                foreach ($skpd as $skpd_item) {

                    // 1) Baris judul unit SKPD (abu-abu)
                    $this->fillRow($sheet, $highestCol, $row, 'FFE1E1E1', true);
                    $row++;

                    $rowsUnit = $realisasiByUnit->get($skpd_item->kode_unit, collect());

                    if ($rowsUnit->isNotEmpty()) {

                        // 2) Baris data program/kegiatan/sub_kegiatan
                        foreach ($rowsUnit as $item) {
                            $type = $item['type'] ?? null;

                            $hex = $item['background-color'] ?? ($defaultTypeHex[$type] ?? null);
                            $argb = $this->argbFromHex($hex);

                            if ($argb) {
                                $bold = in_array($type, ['program','kegiatan'], true);
                                $this->fillRow($sheet, $highestCol, $row, $argb, $bold);
                            }

                            $row++;
                        }

                        // 3) Summary + faktor + spacer:
                        // sesuai blade Anda: rata-rata, predikat, 4 faktor, 1 spacer kosong => total 7 baris
                        $row += 7;

                    } else {
                        // kalau "Belum ada data" (1 baris)
                        $row += 1;
                    }

                    // stop aman kalau melewati highestRow
                    if ($row > $highestRow) break;
                }

                // Opsional: alignment right untuk kolom angka (sesuaikan)
                // $sheet->getStyle('J'.$firstDataRow.':'.$highestCol.$highestRow)
                //       ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        ];
    }
}