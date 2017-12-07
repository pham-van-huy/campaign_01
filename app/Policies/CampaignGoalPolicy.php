<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CampaignGoal;
use App\Models\Role;

class CampaignGoalPolicy extends BasePolicy
{
    public function view(User $user, CampaignGoal $campaignGoal)
    {
        return $user->can('view', $campaignGoal->campaign);
    }

    public function manage(User $user, CampaignGoal $campaignGoal)
    {
        return $user->id === $campaignGoal->user_id
            || $campaignGoal->campaign->getUserByRole([Role::ROLE_OWNER, Role::ROLE_MODERATOR])->get()->contains($user);
    }

    public function comment(User $user, CampaignGoal $campaignGoal)
    {
        return $user->can('comment', $campaignGoal->campaign);
    }

    public function like(User $user, CampaignGoal $campaignGoal)
    {
        return $user->can('like', $campaignGoal->campaign);
    }

    public function member(User $user, CampaignGoal $campaignGoal)
    {
        return $user->can('member', $campaignGoal->campaign);
    }
}
