<?php

namespace HMS\User;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Entities\Profile;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\UserRepository;
use HMS\Repositories\ProfileRepository;

class ProfileManager
{
    /**
     * @var ProfileRepository
     */
    protected $profileRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var MetsRepository
     */
    protected $metaRepository;

    /**
     * ProfileManager constructor.
     * @param ProfileRepository $profileRepository
     * @param UserRepository    $userRepository
     * @param MetaRepository    $metaRepository
     */
    public function __construct(ProfileRepository $profileRepository, UserRepository $userRepository, MetaRepository $metaRepository)
    {
        $this->profileRepository = $profileRepository;
        $this->userRepository = $userRepository;
        $this->metaRepository = $metaRepository;
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
     * @param string $dateOfBirth
     * @return User
     */
    public function create(User $user, string $address1, string $address2, string $address3, string $addressCity, string $addressCounty, string $addressPostcode, string $contactNumber, string $dateOfBirth)
    {
        $profile = new Profile($user);

        $profile->setAddress1($address1);

        if ( ! empty($address2)) {
            $profile->setAddress2($address2);
        }

        if ( ! empty($address3)) {
            $profile->setAddress3($address3);
        }

        $profile->setAddressCity($addressCity);
        $profile->setAddressCounty($addressCounty);
        $profile->setAddressPostcode($addressPostcode);
        $profile->setContactNumber($contactNumber);

        if ( ! empty($dateOfBirth)) {
            $profile->setDateOfBirth(new Carbon($dateOfBirth));
        }

        $profile->setCreditLimit($this->metaRepository->get('member_credit_limit'));

        $profile->setUnlockText('Welcome ' . $user->getFirstName());

        $this->profileRepository->save($profile);

        $user->setProfile($profile);
        $this->userRepository->save($user);

        return $user;
    }
}
