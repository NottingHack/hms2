<?php

use HMS\Tools\ToolManager;
use Illuminate\Database\Seeder;

class ToolTableSeeder extends Seeder
{
    /**
     * @var ToolManager
     */
    protected $toolManager;

    /**
     * Create a new TableSeeder instance.
     *
     * @param ToolManager $toolManager
     */
    public function __construct(ToolManager $toolManager)
    {
        $this->toolManager = $toolManager;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tools = [
            [
                'name' => 'Laser',
                'restricted' => true,
                'pph' => 300,
                'bookingLength' => 30,
                'lengthMax' => 120,
                'bookingsMax' => 1,
            ],
            [
                'name' => 'Ultimaker',
                'restricted' => true,
                'pph' => 0,
                'bookingLength' => 60,
                'lengthMax' => 240,
                'bookingsMax' => 2,
            ],
            [
                'name' => 'Embroidery Machine',
                'restricted' => false,
                'pph' => 0,
                'bookingLength' => 15,
                'lengthMax' => 120,
                'bookingsMax' => 1,
            ],
        ];

        foreach ($tools as $toolSettings) {
            $tool = $this->toolManager->create(
                $toolSettings['name'],
                $toolSettings['restricted'],
                $toolSettings['pph'],
                $toolSettings['bookingLength'],
                $toolSettings['lengthMax'],
                $toolSettings['bookingsMax']);

            $this->toolManager->enableTool($tool);
        }
    }
}
