<?php

namespace Botble\RealEstate\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Order;
use Botble\RealEstate\Models\Property;
use Botble\Table\Actions\Action;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EnumColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;

class AccountOrderTable extends OrderTable
{
    public function setup(): void
    {
        $this
            ->model(Order::class)
            ->addActions([
                EditAction::make()->route('public.account.orders.edit'),
                DeleteAction::make()->route('public.account.orders.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
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
            ])
            ->where([
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ]);

        return $this->applyScopes($query);
    }

    public function bulkActions(): array
    {
        return [];
    }

    public function buttons(): array
    {
        $buttons = [];
            $buttons = $this->addCreateButton(route('public.account.orders.create'));

        return $buttons;
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

    public function getDefaultButtons(): array
    {
        return ['reload'];
    }
}
