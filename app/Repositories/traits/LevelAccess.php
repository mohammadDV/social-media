<?php

namespace App\Repositories\traits;

use Illuminate\Support\Facades\Auth;

trait LevelAccess
{
    /**
     * Check the level access
     * @param bool $conditions
     * @return void
     */
    public function checkLevelAccess(bool $condition = false) {

        if (!$condition && Auth::user()->level != 3) {
            throw New \Exception('Unauthorized', 401);
        }
    }
}
;
