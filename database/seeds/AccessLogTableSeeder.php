<?php

use HMS\Entities\Gatekeeper\AccessLog;
use HMS\Repositories\Gatekeeper\AccessLogRepository;
use HMS\Repositories\Gatekeeper\DoorRepository;
use HMS\Repositories\Gatekeeper\RfidTagRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Database\Seeder;

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
     * Create a new TableSeeder instance.
     *
     * @param RoleRepository      $roleRepository
     * @param RfidTagRepository   $rfidTagRepository
     * @param AccessLogRepository $accessLogRepository
     * @param DoorRepository      $doorRepository
     */
    public function __construct(RoleRepository $roleRepository,
        RfidTagRepository $rfidTagRepository,
        AccessLogRepository $accessLogRepository,
        DoorRepository $doorRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->rfidTagRepository = $rfidTagRepository;
        $this->accessLogRepository = $accessLogRepository;
        $this->doorRepository = $doorRepository;
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
        foreach ($tags as $tag) {
            $a = entity(AccessLog::class)->make(['tag' => $tag, 'door' => $door]);

            $this->accessLogRepository->save($a);
        }
    }
}
