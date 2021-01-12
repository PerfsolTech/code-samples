<?php

namespace App\Modules\Admin\Helpers;

use App\Models\User;
use App\Repositories\CurrencyRepository;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Support\Facades\Hash;

trait UserHelper
{
    protected function userGrid(Grid $grid)
    {
        $grid->id('ID')->sortable();
        $grid->column('email')->display(function ($email) {
            return "<a href='mailto:{$email}'>{$email}</a>";
        });
        $grid->column('name')->display(function () {
            return $this->first_name . ' ' . $this->last_name;
        });

        $grid->status()->display(function ($status) {
            $statuses = [
                User::STATUS_REGISTRED => 'warning',
                User::STATUS_ACTIVE => 'success',
                User::STATUS_BANNED => 'danger',
            ];
            $decorated = ucfirst(strtolower($status));
            $label = $statuses[$status];
            return "<span class='label label-{$label}'>{$decorated}</span>";
        });
    }

    public function basicForm(Form $form)
    {
        $form->text('email');

        $form->text('first_name');
        $form->text('last_name');

        $form->select('gender')->options([
            User::GENDER_FEMALE => ucfirst(strtolower(User::GENDER_FEMALE)),
            User::GENDER_MALE => ucfirst(strtolower(User::GENDER_MALE)),
        ]);

        $form->language()->attribute('required', 'required');
        $form->select('currency_id', 'Currency')->options(app(CurrencyRepository::class)->listFromAdminSelect());
        $form->select('status')->options(User::HUMAN_STATUSES);
    }

    public function locationForm(Form $form)
    {

        $form->tab('Location', function (Form $form) {
            $form->text('latitude');
            $form->text('longitude');
        });
    }

    public function passwordFrom(Form $form)
    {

        $form->tab('Password', function (Form $form) {

            $form->password('password')->rules('confirmed');
            $form->password('password_confirmation');

        });

        $form->ignore(['password_confirmation']);

        $form->saving(function(Form $form) {
            if($form->password && $form->model()->password !== $form->password)
            {
                $form->password = Hash::make($form->password);
            }
        });
    }

}