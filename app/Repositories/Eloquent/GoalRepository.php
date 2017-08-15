<?php

namespace App\Repositories\Eloquent;

use Exception;
use App\Models\Goal;
use App\Repositories\Contracts\GoalInterface;

class GoalRepository extends BaseRepository implements GoalInterface
{
    public function model()
    {
        return Goal::class;
    }

    public function updateManyRow($data)
    {
        if (!is_array($data)) {
            return false;
        }

        foreach ($data as $value) {
            $this->update($value['id'], ['goal' => $value['goal']]);
        }
    }

    public function listGoal($event)
    {
        // $result = $event
        //     ->goals()
        //     ->with([
        //         'donations',
        //         'donationType',
        //         'donationType.quality',
        //         'expenses.products',
        //     ])
        //     ->get();
        // dd($result);

        $goals = Goal::where('event_id', 61)->get()->sortBy('goal');

        dd($event->goals, Goal::where('event_id', 61)->get());

        // $data = $event
        //     ->goals;
            // ->with([
            //     'donations',
            //     'donationType',
            //     'donationType.quality',
            //     'expenses.products',
            // ])->get();

        // $goals->sortByDesc(function($goals) {
        //     return (int) ($goals->id);
        // });

        return ($goals->values()->all()) ;
    }
}
