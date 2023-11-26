<?php

namespace Botble\RealEstate\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broker extends Model
{
    use HasFactory;
    protected $table = 're_brokers';

    protected $fillable = [
        'is_developer',
        'val_license_number',
        'commercial_registration',
        'license_number',
        'account_id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    function agents()
    {
        return $this->hasMany(Agent::class);
    }
}
