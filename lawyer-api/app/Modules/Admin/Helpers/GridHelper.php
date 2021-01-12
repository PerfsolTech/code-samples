<?php

namespace App\Modules\Admin\Helpers;

trait GridHelper
{

    protected function displayStatus()
    {
        return function ($status) {
            $statuses = [
                'ENABLED' => 'success',
                'DISABLED' => 'danger',
            ];
            $decorated = strtolower($status);
            $label = $statuses[$status];
            return "<span class='label label-{$label}'>{$decorated}</span>";

        };
    }


    protected function changeStatusLink($route, $status)
    {
        $text = $status == 'ENABLED' ? 'Disable' : 'Enable';
        return "<a href='{$route}' class='btn btn-default btn-xs'>{$text}</a>";
    }
}