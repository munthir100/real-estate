<?php


namespace Botble\RealEstate\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;
    protected $table = 're_agents';

    protected $fillable = [
        'account_id',
        'broker_id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }
    public function properties()
    {
        return $this->hasMany(AgentProperty::class);
    }
}