<?php

namespace HMS\Governance;

class VotingPreference
{
    /**
     * Member has not yet stated a voting preference.
     */
    public const AUTOMATIC = 'AUTOMATIC';

    /**
     * Member has stated a preference as Voting.
     */
    public const VOTING = 'VOTING';

    /**
     * Member has stated a preference as NonVoting.
     */
    public const NONVOTING = 'NONVOTING';

    /**
     * String representation of states for display.
     */
    public const STATE_STRINGS = [
        self::AUTOMATIC => 'Automatic',
        self::VOTING => 'Voting',
        self::NONVOTING => 'Non-voting',
    ];
}
