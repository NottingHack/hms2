<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use HMS\Repositories\RoleRepository;
use HMS\Entities\GateKeeper\AccessLog;
use HMS\Factories\GateKeeper\AccessLogFactory;
use HMS\Repositories\GateKeeper\DoorRepository;
use HMS\Repositories\GateKeeper\RfidTagRepository;
use HMS\Repositories\GateKeeper\AccessLogRepository;

class AccessLogTableSeeder extends Seeder
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var RfidTagRepository
     */
    protected $rfidTagRepository;

    /**
     * @var AccessLogRepository
     */
    protected $accessLogRepository;

    /**
     * @var DoorRepository
     */
    protected $doorRepository;

    /**
     * @var AccessLogFactory
     */
    protected $accessLogFactory;

    /**
     * Create a new TableSeeder instance.
     *
     * @param RoleRepository      $roleRepository
     * @param RfidTagRepository   $rfidTagRepository
     * @param AccessLogRepository $accessLogRepository
     * @param DoorRepository      $doorRepository
     * @param AccessLogFactroy    $accessLogFactory
     */
    public function __construct(RoleRepository $roleRepository, RfidTagRepository $rfidTagRepository, AccessLogRepository $accessLogRepository, DoorRepository $doorRepository, AccessLogFactory $accessLogFactory)
    {
        $this->roleRepository = $roleRepository;
        $this->rfidTagRepository = $rfidTagRepository;
        $this->accessLogRepository = $accessLogRepository;
        $this->doorRepository = $doorRepository;
        $this->accessLogFactory = $accessLogFactory;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = $this->rfidTagRepository->findAll();
        $door = $this->doorRepository->findOnebyShortName('UP-INNER');
        $now = Carbon::now();
        foreach ($tags as $tag) {
            $a = $this->accessLogFactory->create();
            if ($tag->getRfidSerial()) {
                $a->setRfidSerial($tag->getRfidSerial());
            } else {
                $a->setRfidSerial($tag->getRfidSerialLegacy());
            }
            $a->setUser($tag->getUser());
            $a->setAccessResult(AccessLog::ACCESS_GRANTED);
            $a->setDoor($door);
            $a->setAccessTime($now);

            $this->accessLogRepository->save($a);
        }
    }
}
