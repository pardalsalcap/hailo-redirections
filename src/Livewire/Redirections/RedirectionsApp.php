<?php

namespace Pardalsalcap\HailoRedirections\Livewire\Redirections;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Pardalsalcap\Hailo\Forms\Traits\HasForms;
use Pardalsalcap\Hailo\Tables\Table;
use Pardalsalcap\Hailo\Tables\Traits\CanDelete;
use Pardalsalcap\Hailo\Tables\Traits\HasActions;
use Pardalsalcap\Hailo\Tables\Traits\HasTables;
use Pardalsalcap\HailoRedirections\Actions\DestroyRedirection;
use Pardalsalcap\HailoRedirections\Actions\UpdateRedirection;
use Pardalsalcap\HailoRedirections\Models\Redirection;
use Pardalsalcap\HailoRedirections\Repositories\RedirectionRepository;
use Throwable;

class RedirectionsApp extends Component
{
    use CanDelete, HasActions, HasForms, HasTables;

    public string $action = 'index';

    public string $q = '';

    public string $redirection_form_title = '';

    protected RedirectionRepository $repository;

    protected $listeners = [
        'searchUpdated' => 'search',
        'destroyRedirection' => 'destroy',
    ];

    protected $queryString = [
        'sort_by' => ['except' => 'id', 'as' => 'sort_by'],
        'sort_direction' => ['except' => ['ASC', 'null'], 'as' => 'sort_direction'],
        'q' => ['except' => ''],
        'filter' => ['except' => 'all'],
        'register_id' => ['except' => ['null', null]],
    ];

    public function mount(RedirectionRepository $repository): void
    {
        $this->repository = $repository;
        $this->setupInitialState();
    }

    public function hydrate(): void
    {
        $this->repository = new RedirectionRepository();
        $this->setupInitialState();
    }

    public function loadForms(): void
    {
        $form = $this->form($this->repository->form($this->loadModel()))
            ->action($this->action == 'edit' ? 'update' : 'store')
            ->title($this->redirection_form_title);
        $this->processFormElements($form, $form->getSchema());
    }

    public function loadModel(): Model
    {
        if ($this->action == 'edit') {
            $redirection = Redirection::find($this->register_id);
            if (! $redirection) {
                $this->cancel();
                $this->dispatch('toast-error', ['title' => __('hailo-redirections::hailo-redirections.not_found')]);
            } else {
                $this->setupFormTitle();

                return $redirection;
            }
        }

        return new Redirection();

    }

    public function destroy($id): void
    {
        try {
            DestroyRedirection::run($id);
            $this->dispatch('deletedRedirection', ['id' => $id]);
            $this->dispatch('toast-success', ['title' => __('hailo-redirections::hailo-redirections.deleted')]);
        } catch (Throwable $e) {
            $this->handleFormException($e, '', __('hailo-redirections::hailo-redirections.not_deleted'));
            $errors = implode('<br />', $this->getValidationErrors()['']);
            $this->dispatch('toast-error', ['title' => $errors]);
        }
    }

    public function update(): void
    {
        try {
            DB::beginTransaction();
            $this->load = false;
            $redirection = $this->loadModel();
            $form = $this->form($this->repository->form($redirection));
            $this->validate($this->validationRules($form));
            UpdateRedirection::run($this->getFormData($form->getName()), $redirection);
            $this->success();
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            $this->handleFormException($e, $form?->getName(), __('hailo-redirections::hailo-redirections.not_saved'));
        }
    }

    public function store(): void
    {
        try {
            DB::beginTransaction();
            $this->load = false;
            $redirection = $this->loadModel();
            $form = $this->form($this->repository->form($redirection));
            $this->validate($this->validationRules($form));
            UpdateRedirection::run($this->getFormData($form->getName()), $redirection);
            $this->success();
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            $this->handleFormException($e, $form?->getName() ?? 'redirections_form', __('hailo-redirections::hailo-redirections.not_saved'));
        }
    }

    public function success(): void
    {
        $this->cancel();
        $this->dispatch('toast-success', ['title' => __('hailo-redirections::hailo-redirections.saved')]);
        $this->load = true;
        $this->loadForms();
    }

    public function cancel(): void
    {
        $this->action = 'index';
        $this->register_id = null;
        $this->setupFormTitle();
        $this->loadForms();
    }

    public function edit($id): void
    {
        $this->register_id = $id;
        $this->action = 'edit';
        $this->setupFormTitle();
        $this->loadForms();
    }

    public function getPaginationAppends(): array
    {
        return [
            'q' => $this->q,
            'action' => 'index',
            'sort_by' => $this->sort_by,
            'sort_direction' => $this->sort_direction,
        ];
    }

    protected function setupTable(): Table
    {
        return $this->table('redirections_table', $this->repository->table(new Redirection()))
            ->sortBy($this->sort_by)
            ->sortDirection($this->sort_direction)
            ->search($this->q)
            ->filterBy($this->filter)
            ->executeQuery()
            ->paginationAppends($this->getPaginationAppends());
    }

    protected function setupFormTitle(): void
    {
        $this->redirection_form_title = __('hailo-redirections::hailo-redirections.redirection_form_title');
        if ($this->action === 'edit') {
            $this->redirection_form_title = __('hailo-redirections::hailo-redirections.redirection_form_title_edit');
        }
    }

    protected function setupInitialState(): void
    {
        $this->deleting_configuration = [
            'title' => __('hailo-redirections::hailo-redirections.confirm_delete_title'),
            'text' => __('hailo-redirections::hailo-redirections.confirm_delete_text'),
            'confirmButtonText' => __('hailo::hailo.confirm_yes'),
            'cancelButtonText' => __('hailo::hailo.confirm_no'),
            'livewireAction' => 'destroyRedirection',
        ];
        $this->nullifyRegisterId();
        $this->setupFormTitle();
        $this->loadForms();
        if ($this->register_id) {
            $this->edit($this->register_id);
        } else {
            $this->action = 'index';
        }
    }

    protected function nullifyRegisterId(): void
    {
        $this->register_id = $this->register_id === 'null' ? null : $this->register_id;
    }

    public function render(): View|Factory
    {
        return view('hailo-redirections::livewire.redirections.app',
            [
                'redirections_table' => $this->setupTable(),
                'redirection_form' => $this->getForm('redirection_form'),
                'validation_errors' => $this->getValidationErrors(),
            ])
            ->layout('hailo::layouts.main')
            ->title(__('hailo-redirections::hailo-redirections.redirections_html_title', ['name' => config('app.name')]));
    }
}
