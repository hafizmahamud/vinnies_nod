<?php

namespace App\Vinnies\Exporter;

use Illuminate\Support\Collection;
use App\Vinnies\Exporter\Traits\NAValue;

abstract class BaseExporter
{
    use NAValue;

    protected $filename;
    protected $excel;
    protected $data;

    protected $markedCells = [];
    protected $markedRows  = [];

    abstract public function generate();

    protected function insert($sheet, $rowNum, $data)
    {
        $sheet->row($rowNum, $data);
        $this->markCell($sheet, $rowNum);
        $this->markRow($sheet, $rowNum);
    }

    protected function markCell($sheet, $rowNum)
    {
        if (empty($this->markedCells)) {
            return;
        }

        foreach ($this->markedCells as $column => $value) {
            if ($sheet->getCell($column . $rowNum)->getValue() == $value[0]) {
                $sheet->cell($column . $rowNum, function ($cell) use ($value) {
                    $cell->setFontColor($value[1]);
                });
            }
        }
    }

    protected function markRow($sheet, $rowNum)
    {
        if (empty($this->markedRows)) {
            return;
        }

        foreach ($this->markedRows as $column => $bgColor) {
            $sheet->cell($column . $rowNum, function ($cell) use ($bgColor) {
                $cell->setBackground($bgColor);
            });
        }
    }
}
