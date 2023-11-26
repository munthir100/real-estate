<?php

namespace Botble\RealEstate\Tables;

use Illuminate\Validation\Rule;
use Botble\Table\Columns\Column;
use Illuminate\Http\JsonResponse;
use Botble\Table\Columns\IdColumn;
use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Models\Order;
use Botble\Table\Actions\EditAction;
use Botble\Table\Columns\EnumColumn;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Columns\CreatedAtColumn;
use Illuminate\Database\Eloquent\Builder;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;

class OrderTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Order::class)
            ->addActions([
                EditAction::make()->route('order.edit'),
                DeleteAction::make()->route('order.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('views', function (Order $item) {
                return number_format($item->views);
            })
            ->editColumn('unique_id', function (Order $item) {
                return BaseHelper::clean($item->unique_id ?: '&mdash;');
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->select([
                'id',
                'phone',
                'views',
                'status',
                'moderation_status',
                'created_at',
                'unique_id',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            Column::make('phone')
                ->title(trans('plugins/real-estate::account.phone'))
                ->alignLeft(),
            Column::make('views')
                ->title(trans('plugins/real-estate::order.views')),
            Column::make('unique_id')
                ->title(trans('plugins/real-estate::order.unique_id')),
            CreatedAtColumn::make(),
            StatusColumn::make(),
            EnumColumn::make('moderation_status')
                ->title(trans('plugins/real-estate::order.moderation_status'))
                ->width(150),
        ];
    }

    public function buttons(): array
    {
        $buttons = $this->addCreateButton(route('order.create'), 'order.create');

        return $buttons;
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('order.destroy'),
        ];
    }

    public function getBulkChanges(): array
    {
        return [
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => PropertyStatusEnum::labels(),
                'validate' => 'required|' . Rule::in(PropertyStatusEnum::values()),
            ],
            'moderation_status' => [
                'title' => trans('plugins/real-estate::order.moderation_status'),
                'type' => 'select',
                'choices' => ModerationStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', ModerationStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }

    public function applyFilterCondition(EloquentBuilder|QueryBuilder|EloquentRelation $query, string $key, string $operator, ?string $value): EloquentRelation|EloquentBuilder|QueryBuilder
    {
        if ($key == 'status') {
            switch ($value) {
                case 'expired':
                    // @phpstan-ignore-next-line
                    return $query->expired();
                case 'active':
                    // @phpstan-ignore-next-line
                    return $query->active();
            }
        }

        return parent::applyFilterCondition($query, $key, $operator, $value);
    }
}
