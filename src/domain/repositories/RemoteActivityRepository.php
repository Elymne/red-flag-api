<?php

namespace Domain\Repositories;

use Domain\Models\Activity;

interface RemoteActivityRepository
{
    /**
     * Fetch all activities from a remote repository.
     * I need this because I want to list every activities that can be associated to a person while creating one.
     * 
     * @return Activity[] - List of activities.
     */
    function findAll(): array;

    /**
     * Fetch unique activity given his ID.
     * I need this because person data from database only have an activity_id.
     * 
     * @param string $ID - Unique ID.
     * @return Activity|null - The unique activity.
     */
    function findUnique(
        string $ID
    ): Activity|null;
}
