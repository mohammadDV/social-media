<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

 /**
 * Interface IMemberRepository.
 */
interface IMemberRepository  {

    /**
     * Get the new members
     * @return AnonymousResourceCollection
     */
    public function getNewMembers() :AnonymousResourceCollection;

    /**
     * Get the congenial members
     * @return AnonymousResourceCollection
     */
    public function getCongenialMembers() :AnonymousResourceCollection;

    /**
     * Get the member info
     * @param User $user
     * @return array
     */
    public function getMemberInfo(User $user) :array;

}
