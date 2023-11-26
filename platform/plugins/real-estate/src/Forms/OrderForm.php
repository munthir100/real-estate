<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Models\Order;
use Botble\RealEstate\Enums\PropertyTypeEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Http\Requests\OrderRequest;
use Botble\RealEstate\Forms\Fields\CategoryMultiField;

class OrderForm extends FormAbstract
{
    public function buildForm(): void
    {
        Assets::addStyles(['datetimepicker'])
            ->addScripts(['input-mask'])
            ->addScriptsDirectly([
                'vendor/core/plugins/real-estate/js/real-estate.js',
                'vendor/core/plugins/real-estate/js/components.js',
            ])
            ->addStylesDirectly('vendor/core/plugins/real-estate/css/real-estate.css');

        Assets::usingVueJS();


        $selectedCategories = [];
        if ($this->getModel()) {
            $selectedCategories = $this->getModel()->categories()->pluck('category_id')->all();
        }

        if (!$this->formHelper->hasCustomField('categoryMulti')) {
            $this->formHelper->addCustomField('categoryMulti', CategoryMultiField::class);
        }

        $selectedCategories = [];
        if ($this->getModel()) {
            $selectedCategories = $this->getModel()->categories()->pluck('category_id')->all();
        }


        $this
            ->setupModel(new Order())
            ->setValidatorClass(OrderRequest::class)
            ->withCustomFields()
            ->add('phone', 'text', [
                'label' => trans('plugins/real-estate::order.form.phone'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('+966xxxxxxxx'),
                    'data-counter' => 120,
                ],
            ])
            ->add('type', 'customSelect', [
                'label' => trans('plugins/real-estate::property.form.type'),
                'required' => true,
                'choices' => PropertyTypeEnum::labels(),
            ])->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'required' => true,
                'attr' => [
                    'class' => 'form-control select-search-full',
                ],
                'choices' => PropertyStatusEnum::labels(),
                'selected' => (string)$this->model->status ?: PropertyStatusEnum::SELLING,
            ])
            ->add('moderation_status', 'customSelect', [
                'label' => trans('plugins/real-estate::property.moderation_status'),
                'required' => true,
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => ModerationStatusEnum::labels(),
                'selected' => (string)$this->model->moderation_status ?: ModerationStatusEnum::APPROVED,
            ])
            ->add('note', 'textarea', [
                'label' => trans('plugins/real-estate::order.form.note'),
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('plugins/real-estate::order.form.note'),
                    'data-counter' => 100,
                ],
            ])
            ->setBreakFieldPoint('author_id')
            ->add('author_id', 'autocomplete', [
                'label' => trans('plugins/real-estate::property.account'),
                'label_attr' => [
                    'class' => 'control-label',
                ],
                'attr' => [
                    'id' => 'author_id',
                    'data-url' => route('account.list'),
                ],
                'choices' => $this->getModel()->author_id ?
                    [
                        $this->model->author->id => $this->model->author->name,
                    ]
                    :
                    ['' => trans('plugins/real-estate::property.select_account')],
            ])
            ->add('unique_id', 'text', [
                'label' => trans('plugins/real-estate::property.unique_id'),
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::property.unique_id'),
                    'class' => 'form-control',
                ],
            ])
            ->add('categories[]', 'categoryMulti', [
                'label' => trans('plugins/real-estate::property.form.categories'),
                'choices' => get_property_categories_with_children(),
                'value' => old('categories', $selectedCategories),
            ])


            ->setActionButtons(view('plugins/real-estate::forms.form-order-actions', ['order' => $this->model])->render());
    }
}
