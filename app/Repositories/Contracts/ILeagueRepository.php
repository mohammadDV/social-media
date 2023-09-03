<?php

namespace App\Repositories\Contracts;

use App\Models\League;
use App\Models\Step;

 /**
 * Interface ILeagueRepository.
 */
interface ILeagueRepository  {

    /**
     * Get the leagues.
     * @param array $sports
     * @return array
     */
    public function index(array $sports) :array;

    /**
     * Get the league info.
     * @param League $league
     * @return array
     */
    public function getLeagueInfo(League $league) :array;

    /**
     * Get the step info.
     * @param Step $step
     * @return array
     */
    public function getStepInfo(Step $step) :array;

}
