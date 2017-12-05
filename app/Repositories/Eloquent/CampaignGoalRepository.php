<?php

namespace App\Repositories\Eloquent;

use Exception;
use App\Models\CampaignGoal;
use App\Models\Activity;
use App\Repositories\Contracts\CampaignGoalInterface;
use App\Exceptions\Api\NotFoundException;

class CampaignGoalRepository extends BaseRepository implements CampaignGoalInterface
{

    public function model()
    {
        return CampaignGoal::class;
    }

    public function create($data)
    {
        $campaignGoal = parent::create($data['campaignGoal']);

        if (!$campaignGoal) {
            throw new NotFoundException('Error create campaign goal');
        }

        $goals = $campaignGoal->goals()->createMany($data['goals']);

        if (!$goals) {
            throw new NotFoundException('Error create goal');
        }

        $campaignGoal->activities()->create([
            'user_id' => $data['user']->id,
            'name' => Activity::CREATE,
        ]);

        return true;
    }
}
