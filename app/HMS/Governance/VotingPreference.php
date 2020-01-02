<?php

namespace HMS\Governance;

class VotingPreference
{
    /**
     * Member has not yet stated a voting preference.
     */
    const AUTOMATIC = 'AUTOMATIC';

    /**
     * Member has stated a preference as Voting.
     */
    const VOTING = 'VOTING';

    /**
     * Member has stated a preference as NonVoting.
     */
    const NONVOTING = 'NONVOTING';

    /**
     * String representation of states for display.
     */
    const STATE_STRINGS =
    [
        self::AUTOMATIC => 'Automatic',
        self::VOTING => 'Voting',
        self::NONVOTING => 'Non-voting',
    ];
}
