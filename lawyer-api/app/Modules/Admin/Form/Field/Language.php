<?php


namespace App\Modules\Admin\Form\Field;


use App\Repositories\LanguageRepository;
use Encore\Admin\Form\Field\Select;

class Language extends Select
{

    public function __construct($column = 'language_id', $arguments = [])
    {
        parent::__construct($column, $arguments ? $arguments : ['Language']);
        $this->options = app(LanguageRepository::class)->listFromAdminSelect()->toArray();
    }

    public function getElementClassSelector()
    {
        return 'language_id';
    }

    public function getView()
    {
        return 'admin::form.select';
    }
}