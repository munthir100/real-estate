<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Facades\Assets;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\Fields\CustomEditorField;
use Botble\RealEstate\Forms\Fields\MultipleUploadField;
use Botble\RealEstate\Http\Requests\AccountPropertyRequest;
use Botble\RealEstate\Models\Property;

class AccountPropertyForm extends PropertyForm
{
    public function buildForm(): void
    {
        parent::buildForm();

        Assets::addScriptsDirectly('vendor/core/core/base/libraries/tinymce/tinymce.min.js');

        if (!$this->formHelper->hasCustomField('customEditor')) {
            $this->formHelper->addCustomField('customEditor', CustomEditorField::class);
        }

        if (!$this->formHelper->hasCustomField('multipleUpload')) {
            $this->formHelper->addCustomField('multipleUpload', MultipleUploadField::class);
        }

        $this
            ->setupModel(new Property())
            ->setFormOption('template', 'plugins/real-estate::account.forms.base')
            ->setFormOption('enctype', 'multipart/form-data')
            ->setValidatorClass(AccountPropertyRequest::class)
            ->setActionButtons(view('plugins/real-estate::account.forms.actions')->render())
            ->remove('is_featured')
            ->remove('moderation_status')
            ->remove('content')
            ->remove('images[]')
            ->remove('never_expired')
            ->remove('author_id')
            ->addAfter('description', 'content', 'customEditor', [
                'label' => trans('core/base::forms.content'),
                'required' => true,
                'attr' => [
                    'rows' => 4,
                ],
            ])
            ->addAfter('content', 'images', 'multipleUpload', [
                'label' => trans('plugins/real-estate::account-property.images', ['max' => RealEstateHelper::maxPropertyImagesUploadByAgent()]),
            ]);
    }
}
