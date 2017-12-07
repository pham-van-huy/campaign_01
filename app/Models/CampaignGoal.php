<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Eloquent\CommentLike;

class CampaignGoal extends BaseModel
{
    use SoftDeletes, CommentLike;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    protected $fillable = [
        'user_id',
        'campaign_id',
        'title',
        'description',
        'number_of_comments',
        'number_of_likes',
    ];

    protected $dates = ['deleted_at'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'activitiable');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
