<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests\CampaignGoalRequest;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CampaignInterface;
use App\Repositories\Contracts\CampaignGoalInterface;
use App\Repositories\Contracts\QualityInterface;

class CampaignGoalController extends ApiController
{
    protected $campaignRepository;
    protected $campaignGoalRepository;
    protected $qualityRepository;

    public function __construct(
        CampaignInterface $campaignRepository,
        CampaignGoalInterface $campaignGoalRepository,
        QualityInterface $qualityRepository
    ) {
        parent::__construct();
        $this->campaignRepository = $campaignRepository;
        $this->campaignGoalRepository = $campaignGoalRepository;
        $this->qualityRepository = $qualityRepository;
    }

    public function store(CampaignGoalRequest $request)
    {
        $data['campaignGoal'] = $request->intersect('campaign_id', 'title', 'description');
        $data['user'] = $this->user;
        $campaign = $this->campaignRepository->findOrFail($data['campaignGoal']['campaign_id']);
        $data['goals'] = $this->qualityRepository->getOrCreate($request->get('goals'));

        if ($this->user->cant('manage', $campaign)) {
            throw new UnknowException('You do not have authorize to create this goal', UNAUTHORIZED);
        }

        return $this->doAction(function () use ($data) {
            $this->campaignGoalRepository->create($data);
            $this->compacts['result'] = 'Create success';
        });
    }
}
