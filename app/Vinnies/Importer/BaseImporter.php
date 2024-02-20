<?php

namespace App\Vinnies\Importer;

use PDOException;
use Carbon\Carbon;
use Monolog\Logger;
use League\Csv\Reader;
use League\Csv\Exception;
use Monolog\Handler\StreamHandler;
use Illuminate\Database\QueryException;

abstract class BaseImporter
{
    protected $csv;
    protected $headers;
    protected $data;
    protected $log;
    protected $result;

    public function __construct($path)
    {
        $this->csv     = Reader::createFromPath($path);
        $this->csv->setHeaderOffset(0);

        $this->result = [
            'success' => 0,
            'failed'  => 0,
        ];
    }

    public function isInvalid()
    {
        try {
            $data = $this->getData();
        } catch (Exception $e) {
            return true;
        }

        return false;
    }

    public function setLogger($name)
    {
        $this->log = new Logger($name);

        $this->log->pushHandler(new StreamHandler(storage_path() . '/logs/' . $name . '-' . time() . '.log'), Logger::INFO);

        return $this;
    }

    public function getHeaders()
    {
        return $this->csv->getHeader();
    }

    public function getData()
    {
        return collect($this->csv->getRecords());
    }

    public function updateSuccessCount()
    {
        $this->result['success'] = $this->result['success'] + 1;

        return $this;
    }

    public function updateFailedCount()
    {
        $this->result['failed'] = $this->result['failed'] + 1;

        return $this;
    }

    protected function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        if ($date == '?') { // what is this, I don't even
            return null;
        }

        $date = Carbon::createFromFormat('d/m/Y', $date);

        return $date;
    }

    protected function save($model, $row = [])
    {
        try {
            $model->save();
            $this->updateSuccessCount();
            $this->log->info('Successfully imported ID: ' . $model->id);
        } catch (QueryException $e) {
            $this->log->error('Fail to insert data to database', [
                'row' => $row,
                'msg' => $e->getMessage(),
            ]);

            $this->updateFailedCount();
        } catch (PDOException $e) {
            $this->log->error('Fail to insert data to database', [
                'row' => $row,
                'msg' => $e->getMessage(),
            ]);

            $this->updateFailedCount();
        }
    }

    protected function parseQuarter($value)
    {
        $value   = explode(':', $value);
        $value   = array_map('trim', $value);
        $quarter = (int) str_replace(['Q', 'q'], '', $value[0]);
        $year    = (int) $value[1];

        return compact('quarter', 'year');
    }

    public function index($columnName)
    {
        return array_search($columnName, $this->getHeaders());
    }

    abstract public function import();
}
