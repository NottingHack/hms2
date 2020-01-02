<?php

namespace HMS\Repositories\Governance;

use HMS\Entities\Governance\Meeting;

interface MeetingRepository
{
    /**
     * Find the next meeting.
     *
     * @return Meeting|null
     */
    public function findNext();

    /**
     * Find all future meetings.
     *
     * @return Meeting[]
     */
    public function findFuture();

    /**
     * Find all meetings with a start time in the past size months.
     *
     * @return Meeting[]
     */
    public function findPastSixMonths();

    /**
     * Finds all meetings in the repository.
     *
     * @return Meeting[]
     */
    public function findAll();

    /**
     * Save Meeting to the DB.
     *
     * @param Meeting $meeting
     */
    public function save(Meeting $meeting);

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');
}
