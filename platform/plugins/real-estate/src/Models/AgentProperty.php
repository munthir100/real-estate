<?php


namespace Botble\RealEstate\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentProperty extends Model
{
    use HasFactory;
    protected $table = 're_agent_properties';

    protected $fillable = [
        'agent_id',
        'property_id',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
