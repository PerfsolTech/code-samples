<?php

namespace App\Modules\Admin\Form\Field;

use App\Repositories\CompetencyRepository;
use Encore\Admin\Form\Field\Select;

class Competency extends Select
{
    public function __construct($column = 'competency_id', $arguments = [])
    {
        parent::__construct($column, $arguments ? $arguments : ['Competency']);
        $this->options = app(CompetencyRepository::class)->listFromAdminSelect()->toArray();
    }

    public function getElementClassSelector()
    {
        return 'competency_id';
    }

    public function getView()
    {
        return 'admin::form.select';
    }
}