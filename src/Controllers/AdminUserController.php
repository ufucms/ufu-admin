<?php

namespace Slowlyo\OwlAdmin\Controllers;

use Slowlyo\OwlAdmin\Renderers\Page;
use Slowlyo\OwlAdmin\Renderers\Form;
use Slowlyo\OwlAdmin\Services\AdminUserService;
use Slowlyo\OwlAdmin\Services\AdminRoleService;

/**
 * @property AdminUserService $service
 */
class AdminUserController extends AdminController
{
    protected string $serviceName = AdminUserService::class;

    public function list(): Page
    {
        $model = $this->serviceName::make()->getModel();
	    $columns = [
            amis()->TableColumn('id', 'ID')->sortable(),
            amis()->TableColumn('avatar', admin_trans('admin.admin_user.avatar'))->type('avatar')->src('${avatar}'),
            amis()->TableColumn('username', admin_trans('admin.username')),
            amis()->TableColumn('mobile', admin_trans('admin.admin_user.mobile'))->copyable(),
            amis()->TableColumn('email', admin_trans('admin.admin_user.email'))->copyable(),
            amis()->TableColumn('name', admin_trans('admin.admin_user.name')),
            amis()->TableColumn('gender', admin_trans('admin.admin_user.gender'))->type('status')->source($model::toSource('genderOpt')),
            amis()->TableColumn('birthday', admin_trans('admin.admin_user.birthday'))->type('date'),
            amis()->TableColumn('roles', admin_trans('admin.admin_user.roles'))->type('each')->items(
                amis()->Tag()->label('${name}')->className('my-1')
            ),
            amis()->TableColumn('enabled', admin_trans('admin.extensions.card.status'))->quickEdit(
                amis()->SwitchControl()->mode('inline')->disabledOn('${administrator}')->saveImmediately(true)
            ),
	        amis()->TableColumn('reason', admin_trans('admin.admin_user.reason')),
            amis()->TableColumn('memo', admin_trans('admin.admin_user.memo')),
            amis()->TableColumn('created_at', admin_trans('admin.created_at'))->type('datetime')->sortable(true),
	        amis()->TableColumn('updated_at', admin_trans('admin.updated_at'))->type('datetime'),
            $this->rowActions([
                $this->rowEditButton(true),
                $this->rowDeleteButton()->hiddenOn('${administrator}'),
            ]),
        ];
        $crud = $this->baseCRUD()
            ->filterTogglable(true)
            ->filter($this->baseFilter()->body(
                amis()->TextControl('keyword', admin_trans('admin.keyword'))
                    ->size('md')
                    ->placeholder(admin_trans('admin.admin_user.search_username'))
            ))
            ->itemCheckableOn('${!administrator}')
            ->columns($columns);

        return $this->baseList($crud);
    }

    public function form(): Form
    {
        $model = $this->serviceName::make()->getModel();
        $form = [
            amis()->ImageControl('avatar', admin_trans('admin.admin_user.avatar'))->receiver($this->uploadImagePath()),
            amis()->TextControl('username', admin_trans('admin.username'))->required(),
            amis()->TextControl('name', admin_trans('admin.admin_user.name'))->required(),
            amis()->TextControl('password', admin_trans('admin.password'))->type('input-password'),
            amis()->TextControl('confirm_password', admin_trans('admin.confirm_password'))->type('input-password'),
            amis()->SelectControl('roles', admin_trans('admin.admin_user.roles'))
                ->searchable()
                ->multiple()
                ->labelField('name')
                ->valueField('id')
                ->joinValues(false)
                ->extractValue()
                ->options(AdminRoleService::make()->query()->get(['id', 'name'])),
            amis()->TextControl('mobile', admin_trans('admin.admin_user.mobile'))->type('input-number')->validations('isPhoneNumber'),
            amis()->TextControl('email', admin_trans('admin.admin_user.email'))->type('input-email')->validations('isEmail'),
            amis()->DateControl('birthday', admin_trans('admin.admin_user.birthday'))->format("YYYY-MM-DD"),
            amis()->RadiosControl('gender', admin_trans('admin.admin_user.gender'))->options($model::$genderOpt)->inline(true)->value($model::$genderDef),
            amis()->SwitchControl('enabled', admin_trans('admin.extensions.card.status'))
                ->onText(admin_trans('admin.extensions.enable'))
                ->offText(admin_trans('admin.extensions.disable'))
                ->disabledOn('${id == 1}')
                ->value(1),
            amis()->TextControl('reason', admin_trans('admin.admin_user.reason'))->visibleOn('this.enabled != 1')->required(),
            amis()->TextControl('memo', admin_trans('admin.admin_user.memo')),
        ];

        return $this->baseForm()->body($form);
    }

    public function detail(): Form
    {
        return $this->baseDetail()->body([]);
    }
}
