<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportJournalExport implements FromView, WithEvents
{

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('reports.journal.export.index', [
            'data' => $this->data
        ]);
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setAutoFilter('A2:'.$event->sheet->getDelegate()->getHighestColumn().'2');
                $stringCount = 1;

                $event->sheet->getDelegate()->getStyle('A' . $stringCount . ':'.$event->sheet->getDelegate()->getHighestColumn(). $stringCount)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                if (isset($this->data['medics'])) {
                    $stringCount += count($this->data['medics']) * 2 + 5;
                    $event->sheet->getDelegate()->getStyle('A' . $stringCount . ':'.$event->sheet->getDelegate()->getHighestColumn(). $stringCount)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                if (isset($this->data['techs'])) {
                    $stringCount += count($this->data['techs']) * 2 + 5;
                    $event->sheet->getDelegate()->getStyle('A' . $stringCount . ':'.$event->sheet->getDelegate()->getHighestColumn(). $stringCount)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                if (isset($this->data['medics_other'])) {
                    foreach ($this->data['medics_other'] as $medic) {
                        $stringCount += count($medic['reports']) * 2 + 4;
                        $event->sheet->getDelegate()->getStyle('A' . $stringCount . ':'.$event->sheet->getDelegate()->getHighestColumn(). $stringCount)
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }

                    // Start table techs other period
                    $stringCount++;
                    $event->sheet->getDelegate()->getStyle('A' . $stringCount . ':'.$event->sheet->getDelegate()->getHighestColumn(). $stringCount)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                if (isset($this->data['techs_other'])) {
                    foreach ($this->data['techs_other'] as $tech) {
                        $stringCount += count($tech['reports']) * 2 + 4;
                        $event->sheet->getDelegate()->getStyle('A' . $stringCount . ':'.$event->sheet->getDelegate()->getHighestColumn(). $stringCount)
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }

                    // Start next table
                    $stringCount++;
                    $event->sheet->getDelegate()->getStyle('A' . $stringCount . ':'.$event->sheet->getDelegate()->getHighestColumn(). $stringCount)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $event->sheet->getDelegate()->getStyle('A1:'.$event->sheet->getDelegate()->getHighestColumn(). $event->sheet->getDelegate()->getHighestRow())
                    ->getAlignment()->setWrapText(true);
            }
        ];
    }
}
