<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

abstract class RestfullController extends Controller
{
    use ModelForm;

    const RESOURCE_NAME = 'Admin';

    /**
     * Index interface.
     *
     * @return Content
     */

    abstract protected function form($id = null);

    abstract protected function grid();

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header(self::RESOURCE_NAME);
            $content->description('list');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header(self::RESOURCE_NAME);
            $content->description('edit');

            $content->body($this->form($id)->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header(self::RESOURCE_NAME);
            $content->description('create');

            $content->body($this->form());
        });
    }
}