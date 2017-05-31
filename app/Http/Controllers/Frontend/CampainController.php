<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Repositories\Contracts\CampaignRepositoryInterface;

class CampainController extends FrontendController
{
    public function __construct(CampaignRepositoryInterface $campaign)
    {
        parent::__construct($campaign);

    }

    public function listMember($campaignId)
    {
        $collections = $this->repository->find($campaignId)->users()->paginate(2);
        $this->compacts = $collections->toArray();
        $this->compacts['paginate'] = $collections;
        $this->view = 'campaign';
        return $this->viewRender();
    }

    public function show($campaignId)
    {
        $campaign = $this->repository->with('users')->find($campaignId)->toJson();
        return $campaign;
    }
}
