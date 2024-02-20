<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $exclude_orders = [];

    protected function sortModelFromRequest($model, $request, $table = '')
    {
        if (!empty($request->get('order'))) {
            foreach ($request->get('order') as $order) {
                if (in_array($request->get('columns')[$order['column']]['name'], $this->exclude_orders)) {
                    continue;
                }

                $model->orderBy(($table ? $table . '.' : '') . $request->get('columns')[$order['column']]['name'], $order['dir']);
            }
        }

        return $model;
    }

    protected function getDatatableBaseData($model, $request)
    {
        return [
            'draw'            => (int) $request->get('draw'),
            'recordsTotal'    => $model->total(),
            'recordsFiltered' => $model->total(),
            'pagination'      => [
                'first'   => 1,
                'last'    => $model->lastPage(),
                'current' => $model->currentPage(),
                'total'   => $model->total(),
                'url'     => [
                    'first' => $model->url(1),
                    'prev'  => $model->previousPageUrl(),
                    'next'  => $model->nextPageUrl(),
                    'last'  => $model->url($model->lastPage()),
                ],
            ],
            'data' => [],
        ];
    }

    public function getLastUpdatedData($model)
    {
        return [
            'updated_date' => $model->updated_at->format('d/m/Y'),
            'updated_time' => $model->updated_at->format('H:i'),
            'updated_name' => $model->updated_by->getFullName(),
        ];
    }
}
