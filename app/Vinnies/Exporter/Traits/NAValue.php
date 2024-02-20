<?php
namespace App\Vinnies\Exporter\Traits;

trait NAValue
{
    protected function getValue($model, $relationship, $column, $subcolumn = false)
    {
        if (empty($model->{$relationship})) {
            return 'N/A';
        }

        if (empty($model->{$relationship}->{$column})) {
            return 'N/A';
        }

        if ($subcolumn) {
            if (empty($model->{$relationship}->{$column})) {
                return 'N/A';
            }

            return $model->{$relationship}->{$column}->{$subcolumn};
        }

        return $model->{$relationship}->{$column};
    }
}
