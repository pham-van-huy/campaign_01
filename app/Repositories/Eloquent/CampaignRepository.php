<?php
namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\CampaignRepositoryInterface;
use App\Models\Campaign;

class CampaignRepository extends BaseRepository implements CampaignRepositoryInterface
{
    public function model()
    {
        return Campaign::class;
    }
}
