<?php

namespace App\Modules\Admin\Form\Field;

use Encore\Admin\Form\Field\Select;

class City extends Select
{
    public function __construct($column = 'city_id', $arguments = [])
    {
        parent::__construct($column, $arguments ? $arguments : ['City']);

        $this->options = function ($id) {
            $city = \App\Models\City::find($id);

            if ($city) {
                return [$city->id => $city->name];
            }
        };
        $this->ajax(route('cities.ajax-select-list'));
        return $this;
    }

    public function getView()
    {
        return 'admin::form.select';
    }
}