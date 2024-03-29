<?php

namespace Pardalsalcap\HailoRedirections\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Pardalsalcap\Hailo\Forms\Fields\HiddenInput;
use Pardalsalcap\Hailo\Forms\Fields\SelectInput;
use Pardalsalcap\Hailo\Forms\Fields\TextInput;
use Pardalsalcap\Hailo\Forms\Form;
use Pardalsalcap\Hailo\Tables\Columns\TextColumn;
use Pardalsalcap\Hailo\Tables\Table;
use Pardalsalcap\HailoRedirections\Models\Redirection;

class RedirectionRepository
{
    protected ?Redirection $redirection = null;

    public function check($url): ?Redirection
    {
        return Redirection::where('hash', $this->hash($url))
            ->first();
    }

    public function status(): array
    {
        return [
            '301' => __('hailo-redirections::hailo-redirections.http_status_301'),
            '302' => __('hailo-redirections::hailo-redirections.http_status_302'),
            '404' => __('hailo-redirections::hailo-redirections.http_status_404'),
            '500' => __('hailo-redirections::hailo-redirections.http_status_500'),
        ];
    }

    public function hash($url): string
    {
        return md5($url);
    }

    public function logError($url, $http_status = 404, $fix = null): Redirection
    {
        $hash = $this->hash($url);

        Redirection::firstOrCreate(
            ['hash' => $hash],
            ['url' => $url, 'http_status' => $http_status, 'hits' => 0]
        )->increment('hits', 1);

        return Redirection::where('hash', $hash)->first();
    }

    public static function form(Model $redirection)
    {
        return Form::make('redirection_form', $redirection)
            ->livewire(true)
            ->action('save')
            ->name('redirection_form')
            ->title(__('hailo-redirections::hailo-redirections.redirection_form_title'))
            ->button(__('hailo::hailo.save'))
            ->schema([
                TextInput::make('url')
                    ->label(__('hailo-redirections::hailo-redirections.field_label_url'))
                    ->placeholder(__('hailo-redirections::hailo-redirections.field_label_url'))
                    ->required()
                    ->rules(function ($form) {
                        if ($form->getModel()->id) {
                            return [
                                'required',
                                'unique:redirections,url,'.$form->getModel()->id,
                                'url',
                            ];
                        }

                        return [
                            'required',
                            'url',
                            'unique:redirections,url',
                        ];
                    }),
                TextInput::make('fix')
                    ->label(__('hailo-redirections::hailo-redirections.field_label_fix'))
                    ->placeholder(__('hailo-redirections::hailo-redirections.field_label_fix'))
                    ->required()
                    ->rules([
                        'required',
                        'different:formData.url',
                        'url',
                    ]),
                HiddenInput::make('hash')
                    ->label(__('hailo-redirections::hailo-redirections.hash_column'))
                    ->required()
                    ->rules(function ($form) {
                        if ($form->getModel()->id) {
                            return ['unique:redirections,hash,'.$form->getModel()->id];
                        }

                        return ['unique:redirections,hash'];
                    }),
                SelectInput::make('http_status')
                    ->label(__('hailo-redirections::hailo-redirections.http_status_column'))
                    ->placeholder(__('hailo-redirections::hailo-redirections.http_status_column'))
                    ->default('301')
                    ->options((new RedirectionRepository())->status())
                    ->required(),
            ]);
    }

    public static function table(Model $redirection): Table
    {
        return Table::make('redirections', $redirection)
            ->title(__('hailo-redirections::hailo-redirections.redirections_table_title'))
            ->hasEditAction(true)
            ->hasDeleteAction(true)
            ->extraField('fix')
            ->addFilter('is_500', function ($query) {
                return $query->where('http_status', 500);
            }, __('hailo-redirections::hailo-redirections.filter_500'))
            ->addFilter('is_404', function ($query) {
                return $query->where('http_status', 404);
            }, __('hailo-redirections::hailo-redirections.filter_404'))
            ->addFilter('pending', function ($query) {
                return $query->whereNull('fix');
            }, __('hailo-redirections::hailo-redirections.filter_pending'))
            ->schema([
                TextColumn::make('url')
                    ->label(__('hailo-redirections::hailo-redirections.field_label_url'))
                    ->hasUrl(true)
                    ->url(function ($model) {
                        return $model->url;
                    })
                    ->display(function ($model) {
                        return $model?->url.'<br />'.$model?->fix;
                    })
                    ->searchable()
                    ->openInNewTab(true),
                TextColumn::make('http_status')
                    ->label(__('hailo-redirections::hailo-redirections.http_status_column'))
                    ->css('hidden sm:table-cell')
                    ->sortable(),
                TextColumn::make('hits')
                    ->label(__('hailo-redirections::hailo-redirections.hits_column'))
                    ->css('hidden md:table-cell text-right')
                    ->sortable()
                    ->display(function ($model) {
                        return Number::format($model->hits, locale: app()->getLocale());
                    }),

            ]);
    }

    public static function dashBoardWidget(Model $redirection): Table
    {
        return Table::make('redirections', $redirection)
            ->title(__('hailo-redirections::hailo-redirections.redirections_table_title'))
            ->hasEditAction(false)
            ->hasDeleteAction(false)
            ->extraField('fix')
            ->addFilter('pending', function ($query) {
                return $query->whereNull('fix');
            }, __('hailo-redirections::hailo-redirections.filter_pending'))
            ->filterBy('pending')
            ->schema([
                TextColumn::make('url')
                    ->label(__('hailo-redirections::hailo-redirections.field_label_url'))
                    ->hasUrl(true)
                    ->url(function ($model) {
                        return $model->url;
                    })
                    ->display(function ($model) {
                        return $model?->url.'<br />'.$model?->fix;
                    })
                    ->openInNewTab(true),
                TextColumn::make('http_status')
                    ->label(__('hailo-redirections::hailo-redirections.http_status_column'))
                    ->css('hidden sm:table-cell'),
            ]);
    }

    /**
     * @throws Exception
     */
    public function destroy(int $redirection_id): bool
    {
        $redirection = Redirection::find($redirection_id);
        if ($redirection) {
            if (! $redirection->delete()) {
                throw new Exception(__('hailo-redirections::hailo-redirections.not_deleted'));
            }

            return true;
        }
        throw new Exception(__('hailo-redirections::hailo-redirections.not_found'));
    }

    protected function saveRedirectionAttributes(Redirection $redirection, array $values): Redirection
    {
        $redirection->fill([
            'url' => $values['url'],
            'fix' => $values['fix'],
            'http_status' => $values['http_status'],
            'hash' => $this->hash($values['url']),
        ])->save();

        return $redirection;
    }

    public function update($values, Redirection $redirection): Redirection
    {
        return $this->saveRedirectionAttributes($redirection, $values);
    }

    public function store($values): Redirection
    {
        return $this->saveRedirectionAttributes(new Redirection(), $values);
    }
}
