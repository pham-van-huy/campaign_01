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
            'user_id' => $data['campaignGoal']['user_id'],
            'name' => Activity::CREATE,
        ]);

        return true;
    }

    public function getGoal($goal)
    {
        $inforGoals = $goal->withTrashed()
            ->with([
                'user',
                'goals' => function ($query) {
                    $query->withTrashed()->with(['donationType.quality', 'donations']);
                },
            ])
            ->getLikes()
            ->orderBy('created_at', 'desc')
            ->paginate(config('settings.paginate_goal'));

        $goals = $inforGoals->each(function ($item) {
            $item->load(['comments' => function ($query) {
                $query->withTrashed()
                    ->getLikes()
                    ->where('parent_id', config('settings.comment_parent'))
                    ->orderBy('created_at', 'desc')
                    ->paginate(config('settings.paginate_comment'), ['*'], 1);
            }]);
        });

        return [
            'inforPage' => $inforGoals,
            'data' => $goals,
        ];
    }
}
