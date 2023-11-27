<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Facades\Assets;
use Botble\RealEstate\Forms\OrderForm;
use Botble\RealEstate\Http\Requests\AccountOrderRequest;
use Botble\RealEstate\Models\Order;

class AccountOrderForm extends OrderForm
{
    public function buildForm(): void
    {
        parent::buildForm();

        Assets::addScriptsDirectly('vendor/core/core/base/libraries/tinymce/tinymce.min.js');

        $this
            ->setupModel(new Order())
            ->setFormOption('template', 'plugins/real-estate::account.forms.base')
            ->setValidatorClass(AccountOrderRequest::class)
            ->setActionButtons(view('plugins/real-estate::account.forms.actions')->render())
            ->remove('moderation_status')
            ->setBreakFieldPoint('unique_id')

            ->remove('author_id');
    }
}
