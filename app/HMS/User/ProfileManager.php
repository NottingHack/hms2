<?php

namespace HMS\User;

use App\Events\Users\DiscordUsernameUpdated;
use Carbon\Carbon;
use Exception;
use HMS\Entities\Profile;
use HMS\Entities\User;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\ProfileRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

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
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * ProfileManager constructor.
     *
     * @param ProfileRepository $profileRepository
     * @param UserRepository $userRepository
     * @param MetaRepository $metaRepository
     */
    public function __construct(
        ProfileRepository $profileRepository,
        UserRepository $userRepository,
        MetaRepository $metaRepository
    ) {
        $this->profileRepository = $profileRepository;
        $this->userRepository = $userRepository;
        $this->metaRepository = $metaRepository;
    }

    /**
     * Bulk populate a user profile, used on registration.
     *
     * @param User $user
     * @param string $address1
     * @param null|string $address2
     * @param null|string $address3
     * @param string $addressCity
     * @param string $addressCounty
     * @param string $addressPostcode
     * @param string $contactNumber
     * @param null|string $dateOfBirth
     * @param null|string $discordUsername
     *
     * @return User
     */
    public function create(
        User $user,
        string $address1,
        ?string $address2,
        ?string $address3,
        string $addressCity,
        string $addressCounty,
        string $addressPostcode,
        string $contactNumber,
        ?string $dateOfBirth,
        ?string $discordUsername = null
    ): User {
        $profile = new Profile($user);

        $profile->setAddress1($address1);

        if (! empty($address2)) {
            $profile->setAddress2($address2);
        }

        if (! empty($address3)) {
            $profile->setAddress3($address3);
        }

        $profile->setAddressCity($addressCity);
        $profile->setAddressCounty($addressCounty);
        $profile->setAddressPostcode($addressPostcode);
        $profile->setContactNumber($contactNumber);

        if (! empty($dateOfBirth)) {
            $profile->setDateOfBirth(new Carbon($dateOfBirth));
        }

        if (! empty($discordUsername)) {
            $profile->setDiscordUsername($discordUsername);
            event(new DiscordUsernameUpdated($user, $profile, null));
        }

        $profile->setCreditLimit($this->metaRepository->get('member_credit_limit'));

        $profile->setUnlockText('Welcome ' . $user->getFirstName());

        $this->profileRepository->save($profile);

        $user->setProfile($profile);
        $this->userRepository->save($user);

        return $user;
    }

    /**
     * Update the user form a form request.
     *
     * @param User $user User to update
     * @param array $request
     *
     * @return User
     */
    public function updateUserProfileFromRequest(User $user, array $request)
    {
        $profile = $user->getProfile();

        if (isset($request['address1'])) {
            $profile->setAddress1($request['address1']);
        }

        // Nullable field
        if (array_key_exists('address2', $request)) {
            $profile->setAddress2($request['address2']);
        }

        // Nullable field
        if (array_key_exists('address3', $request)) {
            $profile->setAddress3($request['address3']);
        }

        if (isset($request['addressCity'])) {
            $profile->setAddressCity($request['addressCity']);
        }

        if (isset($request['addressCounty'])) {
            $profile->setAddressCounty($request['addressCounty']);
        }

        if (isset($request['addressPostcode'])) {
            $profile->setAddressPostcode($request['addressPostcode']);
        }

        if (isset($request['contactNumber'])) {
            $profile->setContactNumber($request['contactNumber']);
        }

        // Nullable field
        if (array_key_exists('dateOfBirth', $request)) {
            if (is_null($request['dateOfBirth'])) {
                $profile->setDateOfBirth(null);
            } else {
                $profile->setDateOfBirth(new Carbon($request['dateOfBirth']));
            }
        }

        // Nullable field
        if (array_key_exists('discordUsername', $request)) {
            $oldDiscordUsername = $profile->getDiscordUsername();

            if (is_null($request['discordUsername'])) {
                $profile->setDiscordUsername(null);
            } else {
                $profile->setDiscordUsername($request['discordUsername']);
            }

            // When a user sets their discord user ID, we want to
            // fire an event to push all roles.
            if ($oldDiscordUsername != $profile->getDiscordUsername()) {
                event(new DiscordUsernameUpdated($user, $profile, $oldDiscordUsername));
            }
        }

        if (isset($request['creditLimit'])) {
            $profile->setCreditLimit($request['creditLimit']);
        }

        if (isset($request['unlockText'])) {
            $profile->setUnlockText($request['unlockText']);
        }

        $this->userRepository->save($user);

        return $user;
    }
}
