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


class TriwulanExport implements FromView, WithEvents
{
    use Exportable;

    protected $data;

    // sesuaikan kalau header Anda beda
    protected int $headerEndRow = 8;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $data = $this->data;
        return view('laporan::triwulan.cetak2', compact('data'));
    }

    private function argbFromHex(?string $hex): ?string
    {
        if (!$hex) return null;

        // bersihkan "#", spasi, titik koma
        $hex = trim($hex);
        $hex = trim($hex, " ;");
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = "{$hex[0]}{$hex[0]}{$hex[1]}{$hex[1]}{$hex[2]}{$hex[2]}";
        }

        if (strlen($hex) !== 6) return null;

        return 'FF' . strtoupper($hex); // FF + RRGGBB
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $highestRow = $sheet->getHighestRow();
                $highestCol = $sheet->getHighestColumn();
                $highestColIndex = Coordinate::columnIndexFromString($highestCol);

                // baris data pertama (tepat setelah header)
                $firstDataRow = $this->headerEndRow + 1;

                // Ambil realisasi array (sesuaikan dengan struktur data Anda)
                $realisasiRows = $this->data->realisasi ?? ($this->data['realisasi'] ?? []);

                // Mapping warna default per type (sesuaikan seperti PDF Anda)
                $typeColor = [
                    'program'      => '#87d1eb', // biru
                    'kegiatan'     => '#e7b763', // oranye
                    'sub_kegiatan' => null,      // putih / no fill
                ];

                // ===== Apply warna per row sesuai urutan realisasi =====
                $i = 0;
                foreach ($realisasiRows as $item) {
                    $rowNumber = $firstDataRow + $i;

                    // jika rowNumber melewati sheet (jaga-jaga)
                    if ($rowNumber > $highestRow) break;

                    $type = $item['type'] ?? null;

                    // prioritas: warna dari data, kalau kosong pakai mapping
                    $hex = $item['background-color'] ?? ($typeColor[$type] ?? null);

                    $argb = $this->argbFromHex($hex);

                    if ($argb) {
                        $rowRange = 'A'.$rowNumber.':'.$highestCol.$rowNumber;

                        $sheet->getStyle($rowRange)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => $argb],
                            ],
                        ]);

                        // opsional: program/kegiatan dibuat bold seperti PDF
                        if (in_array($type, ['program', 'kegiatan'], true)) {
                            $sheet->getStyle($rowRange)->getFont()->setBold(true);
                        }
                    }

                    $i++;
                }

                // ===== (opsional) Freeze pane: header tidak geser =====
                $sheet->freezePane('A'.$firstDataRow);

                // ===== (opsional) wrap & vertical top =====
                $fullRange = 'A1:'.$highestCol.$highestRow;
                $sheet->getStyle($fullRange)->getAlignment()->setWrapText(true);
                $sheet->getStyle($fullRange)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

                // ===== (opsional) border seluruh area =====
                $sheet->getStyle($fullRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                $noBorderRange = 'A1:'.$highestCol.'4';

                $sheet->getStyle($noBorderRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_NONE,
                        ],
                    ],
                ]);
            }
        ];
    }
}
