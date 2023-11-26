<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\Media\Facades\RvMedia;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyPeriodEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\QueryBuilders\PropertyBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @method static \Botble\RealEstate\QueryBuilders\PropertyBuilder<static> query()
 */
class Order extends BaseModel
{
    protected $table = 're_orders';

    protected $fillable = [
        'phone',
        'type',
        'status',
        'city_id',
        'state_id',
        'country_id',
        'author_id',
        'author_type',
        'unique_id',
        'note'
    ];

    protected $casts = [
        'phone' => SafeContent::class,
        'status' => PropertyStatusEnum::class,
        'moderation_status' => ModerationStatusEnum::class,
        'type' => PropertyTypeEnum::class,
        'note' => SafeContent::class,
    ];

    protected static function booted(): void
    {
        static::deleting(function (Order $order) {
            $order->categories()->detach();
            $order->metadata()->delete();
        });
    }

    protected function category(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->categories->first() ?: new Category();
            },
        );
    }

    public function author(): MorphTo
    {
        return $this->morphTo()->withDefault();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 're_order_categories');
    }

    protected function cityName(): Attribute
    {
        return Attribute::make(
            get: function () {
                return ($this->city->name ? $this->city->name . ', ' : null) . $this->state->name;
            },
        );
    }

    protected function typeHtml(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->type->label();
            },
        );
    }

    protected function statusHtml(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->status->toHtml();
            },
        );
    }

    protected function categoryName(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->category->name;
            },
        );
    }
}
