<?php

declare(strict_types=1);

namespace Npds\Support;

use Closure;

use Npds\Database\Query\Builder as QueryBuilder;
use Npds\Support\Facades\Input;
use Npds\Support\Facades\Response;


class DataTable
{
    /**
     * Server Side Processor for DataTables.
     *
     * @param Npds\Database\Query\Builder $query
     * @param array $options
     * @param array $input
     *
     * @return array
     */
    public static function handle(QueryBuilder $query, array $options, array $input = array())
    {
        if (empty($input)) {
            $input = Input::only('draw', 'columns', 'start', 'length', 'search', 'order');
        }

        $columns = Arr::array_get($input, 'columns', array());

        // Compute the total count.
        $recordsTotal = $query->count();

        // Compute the draw.
        $draw = intval(Arr::array_get($input, 'draw', 0));

        // Handle the global searching.
        $search = trim(Arr::array_get($input, 'search.value'));

        if (! empty($search)) {
            $query->whereNested(function($query) use($columns, $options, $search)
            {
                foreach($columns as $column) {
                    $data = $column['data'];

                    $option = Arr::array_first($options, function ($key, $value) use ($data)
                    {
                        return ($value['data'] == $data);
                    });

                    if ($column['searchable'] == 'true') {
                        $query->orWhere($option['field'], 'LIKE', '%' .$search .'%');
                    }
                }
            });
        }

        // Handle the column searching.
        foreach($columns as $column) {
            $data = $column['data'];

            $option = Arr::array_first($options, function ($key, $value) use ($data)
            {
                return ($value['data'] == $data);
            });

            $search = trim(Arr::array_get($column, 'search.value'));

            if (($column['searchable'] == 'true') && (strlen($search) > 0)) {
                $query->where($option['field'], 'LIKE', '%' .$search .'%');
            }
        }

        // Compute the filtered count.
        $recordsFiltered = $query->count();

        // Handle the column ordering.
        $orders = Arr::array_get($input, 'order', array());

        foreach ($orders as $order) {
            $index = intval($order['column']);

            $column = Arr::array_get($input, 'columns.' .$index, array());

            //
            $data = $column['data'];

            $option = Arr::array_first($options, function ($key, $value) use ($data)
            {
                return ($value['data'] == $data);
            });

            if ($column['orderable'] == 'true') {
                $dir = ($order['dir'] === 'asc') ? 'ASC' : 'DESC';

                $field = $option['field'];

                if (is_null($model = $query->getModel())) {
                    $field = $model->getTable() .'.' .$field;
                }

                $query->orderBy($field, $dir);
            }
        }

        // Handle the pagination.
        $start  = Arr::array_get($input, 'start',  0);
        $length = Arr::array_get($input, 'length', 25);

        $query->skip($start)->take($length);

        // Calculate the columns.
        $columns = array();

        foreach ($options as $option) {
            $key = $option['data'];

            //
            $field = Arr::array_get($option, 'field');

            $columns[$key] = Arr::array_get($option, 'uses', $field);
        }

        // Retrieve the data from database and it on respect of DataTables specs.
        $results = $query->get();

        $data = array();

        foreach ($results as $result) {
            $record = array();

            foreach ($columns as $key => $field) {
                if (is_null($field)) {
                    continue;
                }

                // Process for dynamic columns.
                else if ($field instanceof Closure) {
                    $value = call_user_func($field, $result, $key);
                }

                // Process for standard columns.
                else {
                    $value = $result->{$field};
                }

                $record[$key] = $value;
            }

            $data[] = $record;
        }

        return Response::json(array(
            "draw"            => $draw,
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data"            => $data
        ));
    }
}
