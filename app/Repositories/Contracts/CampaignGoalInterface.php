<?php
namespace App\Repositories\Contracts;

interface CampaignGoalInterface extends RepositoryInterface
{
    public function getGoal($goal);
}
