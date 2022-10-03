<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class UsersExport implements FromCollection
{
    public function collection()
    {
        return GroupModel::all();
    }
}

class LaporanViewExport implements FromView,ShouldAutoSize
{
	private $data;

	public function __construct($data){
        $this->data = $data;
    }

    public function view(): View{
        return view('admin.laporan.excel_laporan', [
            'data' => $this->data
        ]);
    }

}

class LaporanPenjualanExcelExport implements FromView,ShouldAutoSize, WithColumnFormatting
{
    private $data;

    public function __construct($data){
        $this->data = $data;
    }

    public function view(): View{
        return view('admin.laporan.excel_laporan_penjualan_detail', [
            'data' => $this->data
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
        ];
    }

}

class LaporanPembelianViewExport implements FromView,ShouldAutoSize,WithEvents
{
    private $data;

    public function __construct($data){
        $this->data = $data;
    }

    public function view(): View{
        return view('admin.laporan.excel_laporan_pembelian', [
            'data' => $this->data
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers                
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle($cellRange,[
                        'borders' => [
                            'outline' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                'color' => ['argb' => 'FFFF0000'],
                            ],
                        ]
                    ]);
            },
        ];
    }

}
