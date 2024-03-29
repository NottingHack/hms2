<?php

namespace Database\Seeders;

use HMS\Entities\Gatekeeper\RfidTag;
use HMS\Entities\Gatekeeper\RfidTagState;
use HMS\Entities\Role;
use HMS\Repositories\Gatekeeper\PinRepository;
use HMS\Repositories\Gatekeeper\RfidTagRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Database\Seeder;

class RfidTagTableSeeder extends Seeder
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var PinRepository
     */
    protected $pinRepository;

    /**
     * @var RfidTagRepository
     */
    protected $rfidTagRepository;

    /**
     * Create a new TableSeeder instance.
     *
     * @param RoleRepository    $roleRepository
     * @param PinRepository     $pinRepository
     * @param RfidTagRepository $rfidTagRepository
     */
    public function __construct(RoleRepository $roleRepository, PinRepository $pinRepository, RfidTagRepository $rfidTagRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->pinRepository = $pinRepository;
        $this->rfidTagRepository = $rfidTagRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [Role::MEMBER_CURRENT, Role::MEMBER_YOUNG, Role::MEMBER_EX];
        foreach ($roles as $role) {
            $role = $this->roleRepository->findOneByName($role);
            foreach ($role->getUsers() as $user) {
                // TODO: seed some rfid card, not for all user, cancle pins, add some lost and expired cards add more than one card in some cases

                if (random_int(1, 20) == 1) {
                    // skip on a 1 in 20 chance
                    continue;
                }

                // generate HEX serial
                [$rfidSerial, $rfidSerialLegacy] = $this->generateRfid();

                // rand generate legacy version
                if (random_int(1, 10) > 4) {
                    $rfidSerialLegacy = null; // throw away the legacy most fo the time
                } else {
                    // keep the legacy and sometimes dont have a hex version
                    if (random_int(1, 10) == 1) {
                        $rfidSerial = null;
                    }
                }

                $rfidTag = new RfidTag($rfidSerial, $rfidSerialLegacy);
                $rfidTag->setState(random_int(1, 3) * 10);
                $rfidTag->setUser($user);
                $this->rfidTagRepository->save($rfidTag);
                if ($rfidTag->getState() != RfidTagState::ACTIVE and random_int(1, 3) >= 2) {
                    [$rfidSerial, $rfidSerialLegacy] = $this->generateRfid();
                    $rfidTag = new RfidTag($rfidSerial);
                    $rfidTag->setState(RfidTagState::ACTIVE);
                    $rfidTag->setUser($user);
                    $this->rfidTagRepository->save($rfidTag);
                }

                // cancle this users pin
                $pins = $this->pinRepository->findbyUser($user);
                foreach ($pins as $pin) {
                    $pin->setStateCancelled();
                    $this->pinRepository->save($pin);
                }
            }
        }
    }

    private function generateRfid()
    {
        $lengthOptions = [4, 7, 10];
        $rfidSerialBytes = random_bytes($lengthOptions[random_int(0, 2)]);
        $rfidSerial = bin2hex($rfidSerialBytes);
        $rfidSerialLegacy = hexdec(substr($rfidSerial, -8));

        return [$rfidSerial, $rfidSerialLegacy];
    }
}
