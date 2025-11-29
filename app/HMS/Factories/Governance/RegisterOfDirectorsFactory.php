<?php

namespace HMS\Factories\Governance;

use Carbon\Carbon;
use HMS\Entities\Governance\RegisterOfDirectors;
use HMS\Entities\User;
use HMS\Repositories\Governance\RegisterOfDirectorsRepository;

class RegisterOfDirectorsFactory
{
    /**
     * @var RegisterOfDirectorsRepository
     */
    protected $registerOfDirectorsRepository;

    /**
     * @param RegisterOfDirectorsRepository $registerOfDirectorsRepository
     */
    public function __construct(RegisterOfDirectorsRepository $registerOfDirectorsRepository)
    {
        $this->registerOfDirectorsRepository = $registerOfDirectorsRepository;
    }

    /**
     * Function to instantiate a new RegisterOfDirectors from given params.
     *
     * @param User $uswer
     * @param string $firstname
     * @param string $lastname
     * @param string $address1
     * @param null|string $address2
     * @param null|string $address3
     * @param string $addressCity
     * @param string $addressCounty
     * @param string $addressPostcode
     * @param Carbon $startedAt
     * @param null|Carbon $endedAt
     *
     * @return RegisterOfDirectors
     */
    public function create(
        User $user,
        string $firstname,
        string $lastname,
        ?string $address1,
        ?string $address2,
        ?string $address3,
        ?string $addressCity,
        ?string $addressCounty,
        ?string $addressPostcode,
        Carbon $startedAt,
        ?Carbon $endedAt = null
    ) {
        $_registerOfDirectors = new RegisterOfDirectors();

        $_registerOfDirectors->setUser($user);
        $_registerOfDirectors->setFirstname($firstname);
        $_registerOfDirectors->setLastname($lastname);

        if (! empty($address1)) {
            $_registerOfDirectors->setAddress1($address1);
        }
        if (! empty($address2)) {
            $_registerOfDirectors->setAddress2($address2);
        }
        if (! empty($address3)) {
            $_registerOfDirectors->setAddress3($address3);
        }
        if (! empty($addressCity)) {
            $_registerOfDirectors->setAddressCity($addressCity);
        }
        if (! empty($addressCounty)) {
            $_registerOfDirectors->setAddressCounty($addressCounty);
        }
        if (! empty($addressPostcode)) {
            $_registerOfDirectors->setAddressPostcode($addressPostcode);
        }

        $_registerOfDirectors->setStartedAt($startedAt);

        if ($endedAt) {
            $_registerOfDirectors->setEndedAt($endedAt);
        }

        return $_registerOfDirectors;
    }
}
