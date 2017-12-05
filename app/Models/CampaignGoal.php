<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignGoal extends BaseModel
{
    use SoftDeletes;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    protected $fillable = [
        'campaign_id',
        'title',
        'description',
    ];

    protected $dates = ['deleted_at'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'activitiable');
    }
}
