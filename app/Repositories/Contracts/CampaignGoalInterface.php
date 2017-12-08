<?php
namespace App\Repositories\Contracts;

interface CampaignGoalInterface extends RepositoryInterface
{
    public function getGoals($goals);

    public function getOneGoal($goal);
}
