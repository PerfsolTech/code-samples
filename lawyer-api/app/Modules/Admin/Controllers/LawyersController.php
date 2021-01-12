<?php

namespace App\Modules\Admin\Controllers;

use App\Models\CaseModel;
use App\Models\LawyerCase;
use App\Models\User;
use App\Modules\Admin\Helpers\UserHelper;
use App\Repositories\CompetencyRepository;
use App\Repositories\LanguageRepository;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class LawyersController extends RestfullController
{
    use UserHelper;

    const RESOURCE_NAME = 'lawyers';

    protected function grid()
    {
        $controller = $this;
        return Admin::grid(User::class, function (Grid $grid) use ($controller) {
            $grid->model()->lawyerProfile();

            $controller->userGrid($grid);
            $grid->lawyer()->cases();
            $grid->lawyer()->rating()->display(function ($rating) {
                $width = $rating / 0.05;
                return "<div class=\"progress\">
  <div class=\"progress-bar\" role=\"progressbar\" aria-valuenow=\"60\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: {$width}%;\">
    {$rating}
  </div>
</div>";
            });
            $grid->actions(function ($actions) use ($controller) {
                $changeStatusLink = route('admin.users.change-status', ['user_id' => $actions->getkey()]);
                $changeStatusText = in_array($this->row->status, [User::STATUS_BANNED, User::STATUS_REGISTRED]) ? 'Activate' : 'Ban';
                $actions->append("<a class='btn btn-default btn-xs' href='{$changeStatusLink}'>{$changeStatusText}</a>");
            });


            $grid->filter(function ($filter) {
                $filter->like('first_name');
                $filter->like('last_name');
                $filter->like('email');

                $filter->is('status', 'Status')->select(User::HUMAN_STATUSES);
                $filter->is('type', 'Type')->select([
                    User::TYPE_LAWYER => 'Lawyer',
                    User::TYPE_CLIENT => 'Client',
                ]);
                $filter->is('language_id', 'Language')->select(app(LanguageRepository::class)->listFromAdminSelect());
            });


            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableBatchDeletion();
        });
    }


    protected function form($id = null)
    {
        $controller = $this;
        $user = User::find($id);
        return Admin::form(User::class, function (Form $form) use ($controller, $user) {
            $form->disableReset();
            $form->tab('Basic', function (Form $form) use ($controller) {
                $controller->basicForm($form);
            });
            $controller->locationForm($form);
            $controller->passwordFrom($form);


            $form->tab('Competency', function (Form $form) {
                $form->hasMany('competencies', function (Form\NestedForm $form) {
                    $form->select('competency_id', 'Competency')->options((new CompetencyRepository())->listFromAdminSelect());
                });
            });

            $form->tab('Languages', function (Form $form) {
                $form->hasMany('languages', function (Form\NestedForm $form) {
                    $form->language('language_id');
                });
            });

            $form->tab('Cases', function (Form $form) use ($user) {
                $form->display('lawyerCases', 'Cases list')->with(function ($cases) use ($user) {
                    $rows = $user->lawyerCases->map(function ($lawyerCase) {
                        $case = CaseModel::find($lawyerCase['case_id']);
                        $label = LawyerCase::STATUS_LABEL[$lawyerCase['status']];
                        $route = route('cases.index', ['id' => $case->id]);
                        return "<p><a href='{$route}'>{$case->title} - <span class='label label-{$label}'>{$lawyerCase['status']}</span></a></p>";
                    });

                    return implode('', $rows->toArray());
                });
            });


        });
    }

}