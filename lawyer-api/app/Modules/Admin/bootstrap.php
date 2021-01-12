<?php

use App\Modules\Admin\Extensions\Column\ExpandRow;
use Encore\Admin\Form;

\Encore\Admin\Grid\Column::extend('expand', ExpandRow::class);

Form::forget(['map', 'editor']);
Form::extend('language', \App\Modules\Admin\Form\Field\Language::class);
Form::extend('city', \App\Modules\Admin\Form\Field\City::class);
Form::extend('competency', \App\Modules\Admin\Form\Field\Competency::class);
