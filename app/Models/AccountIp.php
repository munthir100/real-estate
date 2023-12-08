<?php

namespace App\Models;

use Botble\RealEstate\Models\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountIp extends Model
{
    use HasFactory;
    protected $fillable = ['account_id', 'ip'];

    function account()
    {
        return $this->belongsTo(Account::class);
    }
}
