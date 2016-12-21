<?php

namespace HMS\User;

use HMS\Entities\User;
use HMS\Entities\Profile;
use Doctrine\ORM\EntityManagerInterface;

class ProfileManager {
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * ProfileManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Bulk populate a user profile, used on registration.
     * @param User $user
     * @param string $address1
     * @param string $address2
     * @param string $address3
     * @param string $addressCity
     * @param string $addressCounty
     * @param string $addressPostcode
     * @param string $contactNumber
     * @return User
     */
    public function create(User $user, string $address1, string $address2, string $address3, string $addressCity, string $addressCounty, string $addressPostcode, string $contactNumber)
    {

        $profile = new Profile($user);

        // $profile = $user->getProfile();
        $profile->setAddress1($address1);

        if (!empty($address2))
            $profile->setAddress2($address2);

        if (!empty($address3))
            $profile->setAddress3($address3);

        $profile->setAddressCity($addressCity);
        $profile->setAddressCounty($addressCounty);
        $profile->setAddressPostcode($addressPostcode);
        $profile->setContactNumber($contactNumber);

        // TODO: get this from meta at some point
        $profile->setCreditLimit(2000);

        $profile->setUnlockText('Welcome ' . $user->getFirstName());

        $this->em->persist($profile);
        $this->em->flush();

        $user->setProfile($profile);
        $this->em->persist($user);
        return $user;
    }

}
