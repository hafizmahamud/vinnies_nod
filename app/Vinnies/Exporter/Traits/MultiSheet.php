<?php
namespace App\Vinnies\Exporter\Traits;

trait MultiSheet
{
    protected $rowNum;

    protected function resetRowNum()
    {
        $this->rowNum = 0;
    }

    protected function setCurrentSheet($sheet)
    {
        $this->sheet = $sheet;
    }

    private function insertBlankRow($row = 1)
    {
        foreach (range(1, $row) as $count) {
            $this->insert([null]);
        }
    }

    private function insert($data)
    {
        $this->rowNum = $this->rowNum + 1;

        $this->sheet->row($this->rowNum, $data);
    }
}
