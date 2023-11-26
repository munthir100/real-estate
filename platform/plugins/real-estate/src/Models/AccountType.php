<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Facades\Html;
use Botble\Base\Models\BaseModel;

class AccountType extends BaseModel
{
    protected $table = 're_account_types';

    protected $fillable = ['name'];

    const BROKER = 1;
    const SEEKER = 2;
    const DEVELOPER = 3;
}
