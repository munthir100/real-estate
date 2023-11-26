<?php


namespace Botble\RealEstate\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seeker extends Model
{
    use HasFactory;
    protected $table = 're_seekers';

    protected $fillable = [
        'account_id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
