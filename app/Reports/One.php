<?php

namespace App\Reports;

use App\Models\Category;
use App\Models\Task;
use App\Services\ReportService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class One extends DefaultValueBinder implements WithStyles, FromCollection, WithHeadings,WithCustomStartCell,WithEvents
{
    use Exportable;

    private $startDate;
    private $endDate;
    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    private $query;

    /**
     * @param $startDate
     * @param $endDate
     */
    public function __construct($startDate, $endDate)
    {
        $this->query = Category::query()->where('parent_id', null)->select('id','name');
        $this->startDate = Carbon::parse("$startDate-31")->toDateTimeString();
        $this->endDate = Carbon::parse("$endDate-31")->toDateTimeString();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private static function core()
    {
        return Task::query();
    }

    /**
     * @return string
     */
    public function startCell(): string
    {
        return 'A2';
    }

    /**
     * @param Worksheet $sheet
     * @return Worksheet
     */
    public function styles(Worksheet $sheet): Worksheet
    {
        $data = [
            'Открытые ' => 'D1',
            'Открытые Ответ' => 'F1',
            'В исполнении' => 'H1',
            'Закрытые' => 'J1',
            'Не завершено' => 'L1',
            'Отмененные' => 'N1',
            'Всего' => 'P1',
        ];
        foreach($data as $value=>$item){
            $sheet->setCellValue($item, $value);
        }
        $sheet->getStyle('1:2')->getFont()->setBold(true);
        return $sheet;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = $this->query->get();
        for($i = 0;$i<count($query);$i++)
        {
            $query[$i]->parent_id = null;
            $query[$i]->open_count = $this->count($query[$i],$this->startDate,$this->endDate,[Task::STATUS_OPEN]);
            $query[$i]->open_sum = $this->summa($query[$i],$this->startDate,$this->endDate,[Task::STATUS_OPEN]);
            $query[$i]->response_count = $this->count($query[$i],$this->startDate,$this->endDate,[Task::STATUS_RESPONSE]);
            $query[$i]->response_sum = $this->summa($query[$i],$this->startDate,$this->endDate,[Task::STATUS_RESPONSE]);
            $query[$i]->process_count = $this->count($query[$i],$this->startDate,$this->endDate,[Task::STATUS_IN_PROGRESS]);
            $query[$i]->process_sum = $this->summa($query[$i],$this->startDate,$this->endDate,[Task::STATUS_IN_PROGRESS]);
            $query[$i]->finished_count = $this->count($query[$i],$this->startDate,$this->endDate,[Task::STATUS_COMPLETE]);
            $query[$i]->finished_sum = $this->summa($query[$i],$this->startDate,$this->endDate,[Task::STATUS_COMPLETE]);
            $query[$i]->not_complete_count = $this->count($query[$i],$this->startDate,$this->endDate,[Task::STATUS_NOT_COMPLETED]);
            $query[$i]->not_complete_sum = $this->summa($query[$i],$this->startDate,$this->endDate,[Task::STATUS_NOT_COMPLETED]);
            $query[$i]->cancelled_count = $this->count($query[$i],$this->startDate,$this->endDate,[Task::STATUS_CANCELLED]);
            $query[$i]->cancelled_sum = $this->summa($query[$i],$this->startDate,$this->endDate,[Task::STATUS_CANCELLED]);
            $query[$i]->total_count = $this->count($query[$i],$this->startDate,$this->endDate,ReportService::statuses);
            $query[$i]->total_sum = $this->summa($query[$i],$this->startDate,$this->endDate,ReportService::statuses);
        }
        return $query;
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return [
            'ID',
            'Категории',
            'Подкатегории',
            'Кол-во',
            'Сумма',
            'Кол-во',
            'Сумма',
            'Кол-во',
            'Сумма',
            'Кол-во',
            'Сумма',
            'Кол-во',
            'Сумма',
            'Кол-во',
            'Сумма',
            'Кол-во',
            'Сумма',
        ];
    }

    /**
     * @return string
     */
    public static function title() : string
    {
        return 'Отчет';
    }

    /**
     * @param $branch
     * @return string
     */
    private function summa($app,$start_date,$end_date,$status)
    {
        $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
        $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
            ->where('category_id', $cat)->whereIn('status', $status)->pluck('budget')->toArray();
        return array_sum($application);
    }

    /**
     * @param $branch
     * @return string
     */
    private function count($app,$start_date,$end_date,$status)
    {
        $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
        $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
            ->where('category_id', $cat)->whereIn('status', $status)->get();
        return count($application);
    }

    /**
     * @return array
     */
    public static function dtHeaders()
    {
        return [
            [
                __('ID') => [
                    'rowspan' => 2,
                    'colspan' => 0,
                ],
                __('Категории') => [
                    'rowspan' => 2,
                    'colspan' => 0,
                ],
                __('Подкатегории') => [
                    'rowspan' => 2,
                    'colspan' => 0,
                ],
                __('Открытые') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Открытые Ответ') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('В исполнении') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Закрытые') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Не завершено') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Отмененные') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
                __('Всего') => [
                    'rowspan' => 0,
                    'colspan' => 2,
                ],
            ],
            [
                __('Кол-во') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Кол-во ') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма ') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Кол-во  ') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('Сумма  ') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __(' Кол-во') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __(' Сумма') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('  Кол-во') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __('  Сумма') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __(' Кол-во ') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __(' Сумма ') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __(' Кол-во  ') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
                __(' Сумма  ') => [
                    'rowspan' => 0,
                    'colspan' => 0,
                ],
            ]
        ];
    }

    /**
     * @return array
     */
    public static function dtColumns()
    {
        return [
            ['data' => 'id', 'name' => 'id'],
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'parent_id', 'name' => 'parent_id','render' => 'function (data, type, row) {
                            var details = `<a href="/admin/report/child/${row.id}"><i class="fas fa-arrow-right"></i></a>`;
                            // var details = `<i class="fas fa-arrow-right"></i>`;
                            return details;
                        }'],
            ['data' => 'open_count', 'name' => 'open_count'],
            ['data' => 'open_sum', 'name' => 'open_sum'],
            ['data' => 'response_count', 'name' => 'response_count'],
            ['data' => 'response_sum', 'name' => 'response_sum'],
            ['data' => 'process_count', 'name' => 'process_count'],
            ['data' => 'process_sum', 'name' => 'process_sum'],
            ['data' => 'finished_count', 'name' => 'finished_count'],
            ['data' => 'finished_sum', 'name' => 'finished_sum'],
            ['data' => 'not_complete_count', 'name' => 'not_complete_count'],
            ['data' => 'not_complete_sum', 'name' => 'not_complete_sum'],
            ['data' => 'cancelled_count', 'name' => 'cancelled_count'],
            ['data' => 'cancelled_sum', 'name' => 'cancelled_sum'],
            ['data' => 'total_count', 'name' => 'total_count'],
            ['data' => 'total_sum', 'name' => 'total_sum'],
        ];
    }

    /**
     * @return string
     */
    public static function route(): string
    {
        return 'report_export';
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('S')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('U')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('W')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('Y')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('AA')->setWidth(40);

                $event->sheet->mergeCells('D1:E1', Worksheet::MERGE_CELL_CONTENT_MERGE);
                $event->sheet->mergeCells('F1:G1', Worksheet::MERGE_CELL_CONTENT_MERGE);
                $event->sheet->mergeCells('H1:I1', Worksheet::MERGE_CELL_CONTENT_MERGE);
                $event->sheet->mergeCells('J1:K1', Worksheet::MERGE_CELL_CONTENT_MERGE);
                $event->sheet->mergeCells('L1:M1', Worksheet::MERGE_CELL_CONTENT_MERGE);
                $event->sheet->mergeCells('N1:O1', Worksheet::MERGE_CELL_CONTENT_MERGE);
                $event->sheet->mergeCells('P1:Q1', Worksheet::MERGE_CELL_CONTENT_MERGE);
                $event->sheet->mergeCells('R1:S1', Worksheet::MERGE_CELL_CONTENT_MERGE);
                $event->sheet->mergeCells('T1:U1', Worksheet::MERGE_CELL_CONTENT_MERGE);
                $event->sheet->mergeCells('V1:W1', Worksheet::MERGE_CELL_CONTENT_MERGE);
                $event->sheet->mergeCells('X1:Y1', Worksheet::MERGE_CELL_CONTENT_MERGE);
                $event->sheet->mergeCells('Z1:AA1', Worksheet::MERGE_CELL_CONTENT_MERGE);

                $event->sheet->getDelegate()->getStyle('1')
                    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
