<?php

namespace App\Console\Commands\Database;

use Carbon\Carbon;
use HMS\Tools\ToolManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateInstrumentaionCommand extends Command
{
    protected $mappings = [
        'access_log' => [
            'newTableName' => 'access_logs',
            'columns' => [
                'access_id' => 'id',
                'access_time' => 'access_time',
                'rfid_serial' => 'rfid_serial',
                'pin' => 'pin',
                'access_result' => 'access_result',
                'member_id' => 'user_id',
                'door_id' => 'door_id',
                'denied_reason' => 'denied_reason',
            ],
        ],
        'account' => [
            'newTableName' => 'accounts',
            'columns' => [
                'account_id' => 'id',
                'payment_ref' => 'payment_ref',
                'natwest_ref' => 'natwest_ref',
            ],
        ],
        'addresses' => [
            'newTableName' => 'addresses',
            'columns' => [
                'id' => 'id',
                'mac_address' => 'mac_address',
                'last_seen' => 'last_seen',
                'ignore_addr' => 'ignore_addr',
                'comments' => 'comments',
            ],
        ],
        'bank_transactions' => [
            'newTableName' => 'bank_transactions',
            'columns' => [
                'bank_transaction_id' => 'id',
                'transaction_date' => 'transaction_date',
                'description' => 'description',
                'amount' => 'amount',
                'bank_id' => 'bank_id',
                'account_id' => 'account_id',
            ],
        ],
        // 'banks' => [
        //     'newTableName' => 'banks',
        //     'columns' => [
        //         'bank_id' => 'id',
        //         'name' => 'name',
        //         'sort_code' => 'sort_code',
        //         'account_number' => 'account_number',
        //     ],
        // ],
        'bells' => [
            'newTableName' => 'bells',
            'columns' => [
                'bell_id' => 'id',
                'bell_description' => 'description',
                'bell_topic' => 'topic',
                'bell_message' => 'message',
                'bell_enabled' => 'enabled',
            ],
        ],
        'door_bells' => [
            'newTableName' => 'door_bell',
            'columns' => [
                'door_id' => 'door_id',
                'bell_id' => 'bell_id',
            ],
        ],
        'doors' => [
            'newTableName' => 'doors',
            'columns' => [
                'door_id' => 'id',
                'door_description' => 'description',
                'door_short_name' => 'short_name',
                'door_state' => 'state',
                'door_state_change' => 'state_change',
                // 'permission_code' => 'permission_code',
                'side_a_zone_id' => 'side_a_zone_id',
                'side_b_zone_id' => 'side_b_zone_id',
            ],
        ],
        'emails' => [
            'newTableName' => 'snackspace_emails',
            'columns' => [
                'email_id' => 'id',
                'member_id' => 'user_id',
                'email_to' => 'to',
                'email_cc' => 'cc',
                'email_bcc' => 'bcc',
                'email_subj' => 'subject',
                'email_body' => 'body',
                'email_body_alt' => 'body_alt',
                'email_status' => 'status',
                'email_date' => 'date',
                'email_link' => 'link',
            ],
        ],
        'events' => [
            'newTableName' => 'events',
            'columns' => [
                'event_id' => 'id',
                'event_time' => 'time',
                'event_type' => 'type',
                'event_value' => 'value',
            ],
        ],
        'invoices' => [
            'newTableName' => 'invoices',
            'columns' => [
                'invoice_id' => 'id',
                'member_id' => 'user_id',
                'invoice_from' => 'from',
                'invoice_to' => 'to',
                'invoice_generated' => 'generated',
                'invoice_status' => 'status',
                'invoice_amount' => 'amount',
                'email_id' => 'email_id',
            ],
        ],
        // 'label_templates' => [
        //     'newTableName' => 'label_templates',
        //     'columns' => [
        //         'template_name' => 'template_name',
        //         'template' => 'template',
        //     ],
        //     'dateSet' => [
        //         'created_at',
        //         'updated_at',
        //     ],
        // ],
        'light_level' => [
            'newTableName' => 'light_level',
            'columns' => [
                'name' => 'name',
                'sensor' => 'sensor',
                'reading' => 'reading',
                'time' => 'time',
            ],
        ],
        'member_boxes' => [
            'newTableName' => 'member_boxes',
            'columns' => [
                'member_box_id' => 'id',
                'member_id' => 'user_id',
                'bought_date' => 'bought_date',
                'removed_date' => 'removed_date',
                'state' => 'state',
            ],
        ],
        'member_projects' => [
            'newTableName' => 'member_projects',
            'columns' => [
                'member_project_id' => 'id',
                'member_id' => 'user_id',
                'project_name' => 'project_name',
                'description' => 'description',
                'start_date' => 'start_date',
                'complete_date' => 'complete_date',
                'state' => 'state',
            ],
        ],
        'membership_status_notifications' => [
            'newTableName' => 'membership_status_notifications',
            'columns' => [
                'membership_status_notification_id' => 'id',
                'member_id' => 'user_id',
                'account_id' => 'account_id',
                'time_issued' => 'time_issued',
                'time_cleared' => 'time_cleared',
                'cleared_reason' => 'cleared_reason',
            ],
        ],
        'pins' => [
            'newTableName' => 'pins',
            'columns' => [
                'pin_id' => 'id',
                'pin' => 'pin',
                'date_added' => 'date_added',
                'expiry' => 'expiry',
                'state' => 'state',
                'member_id' => 'user_id',
            ],
        ],
        'products' => [
            'newTableName' => 'products',
            'columns' => [
                'product_id' => 'id',
                'price' => 'price',
                'barcode' => 'barcode',
                'available' => 'available',
                'shortdesc' => 'short_description',
                'longdesc' => 'long_description',
            ],
        ],
        'purchase_payment' => [
            'newTableName' => 'purchase_payment',
            'columns' => [
                'transaction_id_purchase' => 'transaction_id_purchase',
                'transaction_id_payment' => 'transaction_id_payment',
                'amount' => 'amount',
            ],
        ],
        'rfid_tags' => [
            'newTableName' => 'rfid_tags',
            'columns' => [
                'rfid_id' => 'id',
                'member_id' => 'user_id',
                'rfid_serial' => 'rfid_serial',
                'rfid_serial_legacy' => 'rfid_serial_legacy',
                'state' => 'state',
                'last_used' => 'last_used',
                'friendly_name' => 'friendly_name',
            ],
        ],
        'service_status' => [
            'newTableName' => 'service_status',
            'columns' => [
                'service_name' => 'service_name',
                'status' => 'status',
                'status_str' => 'status_str',
                'query_time' => 'query_time',
                'reply_time' => 'reply_time',
                'restart_time' => 'restart_time',
                'description' => 'description',
            ],
        ],
        'temperature' => [
            'newTableName' => 'temperature',
            'columns' => [
                'name' => 'name',
                'dallas_address' => 'dallas_address',
                'temperature' => 'temperature',
                'time' => 'time',
            ],
        ],
        'vmc_details' => [
            'newTableName' => 'vending_machines',
            'columns' => [
                'vmc_id' => 'id',
                'vmc_description' => 'description',
                'vmc_type' => 'type',
                'vmc_connection' => 'connection',
                'vmc_address' => 'address',
            ],
        ],
        // 'vmc_ref' => [
        //     'newTableName' => 'vending_locations',
        //     'columns' => [
        //         'vmc_ref_id' => 'id',
        //         'vmc_id' => 'vending_machine_id',
        //         'loc_encoded' => 'encoding',
        //         'loc_name' => 'name',
        //     ],
        // ],
        // 'vmc_state' => [
        //     'newTableName' => 'product_vending_location',
        //     'columns' => [
        //         'vmc_ref_id' => 'vending_location_id',
        //         'product_id' => 'product_id',
        //     ],
        // ],
        'transactions' => [
            'newTableName' => 'transactions',
            'columns' => [
                'transaction_id' => 'id',
                'member_id' => 'user_id',
                'transaction_datetime' => 'transaction_datetime',
                'amount' => 'amount',
                'transaction_type' => 'transaction_type',
                'transaction_status' => 'transaction_status',
                'transaction_desc' => 'transaction_desc',
                'product_id' => 'product_id',
                'recorded_by' => 'recorded_by',
            ],
        ],
        'tl_tool_usages' => [
            'newTableName' => 'tool_usages',
            'columns' => [
                'usage_id' => 'id',
                'member_id' => 'user_id',
                'tool_id' => 'tool_id',
                'usage_start' => 'start',
                'usage_duration' => 'duration',
                'usage_active_time' => 'active_time',
                'usage_status' => 'status',
            ],
        ],
        // 'tl_tools' => [
        //     'newTableName' => 'tools',
        //     'columns' => [
        //         'tool_id' => 'id',
        //         'tool_name' => 'name',
        //         'tool_status' => 'status',
        //         'tool_restrictions' => 'restrictions',
        //         'tool_status_text' => 'status_text',
        //         'tool_pph' => 'pph',
        //         'tool_booking_length' => 'booking_length',
        //         'tool_length_max' => 'length_max',
        //         'tool_bookings_max' => 'bookings_max',
        //     ],
        // ],
        'vend_log' => [
            'newTableName' => 'vend_logs',
            'columns' => [
                'vend_tran_id' => 'id',
                'transaction_id' => 'transaction_id',
                'vmc_id' => 'vending_machine_id',
                'rfid_serial' => 'rfid_serial',
                'member_id' => 'user_id',
                'enq_datetime' => 'enqueued_time',
                'req_datetime' => 'request_time',
                'success_datetime' => 'success_time',
                'cancelled_datetime' => 'cancelled_time',
                'failed_datetime' => 'failed_time',
                'amount_scaled' => 'amount_scaled',
                'position' => 'position',
                'denied_reason' => 'denied_reason',
            ],
        ],
        'zone_occupancy' => [
            'newTableName' => 'zone_occupants',
            'columns' => [
                'zone_id' => 'zone_id',
                'member_id' => 'user_id',
                'time_entered' => 'time_entered',
            ],
        ],
        'zone_occupancy_log' => [
            'newTableName' => 'zone_occupancy_logs',
            'columns' => [
                'zone_occ_log_id' => 'id',
                'zone_id' => 'zone_id',
                'member_id' => 'user_id',
                'time_exited' => 'time_exited',
                'time_entered' => 'time_entered',
            ],
        ],
        'zones' => [
            'newTableName' => 'zones',
            'columns' => [
                'zone_id' => 'id',
                'zone_description' => 'description',
                'zone_short_name' => 'short_name',
                'permission_code' => 'permission_code',
            ],
        ],
    ];

    /**
     * @var ToolManager
     */
    protected $toolManager;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hms:database:migrateInstrumentaion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data form old instrumentation database';

    /**
     * Create a new command instance.
     *
     * @param ToolManager $toolManager
     *
     * @return void
     */
    public function __construct(ToolManager $toolManager)
    {
        parent::__construct();
        $this->toolManager = $toolManager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Please provide details for connecting to the instrumentation database.');
        $instrumentationHost = $this->anticipate('Host? [127.0.0.1]', ['127.0.0.1']) ?: '127.0.0.1';
        $instrumentationDatabase = $this->anticipate('Database name? [instrumentation]', ['instrumentation']) ?: 'instrumentation';
        $instrumentationUser = $this->anticipate('User? [hms]', ['hms']) ?: 'hms';
        $instrumentationPassword = $this->secret('Password? [secret]') ?: 'secret';

        $start = Carbon::now();
        config(['database.connections.instrumentation' => [
            'driver' => 'mysql',
            'host' => $instrumentationHost,
            'port' => '3306',
            'database' => $instrumentationDatabase,
            'username' => $instrumentationUser,
            'password' => $instrumentationPassword,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'strict' => true,
            'engine' => null,
        ]]);

        DB::purge('instrumentation');
        DB::connection('instrumentation')->statement('RESET QUERY CACHE;');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Simple migrations first
        $this->migrateSimpleMappings();

        // Now for the more complex ones
        $this->migrateStatusUpdates();
        $this->migrateMembers();
        $this->migrateMemberStatus();
        $this->migrateTools();
        $this->migrateMemberTools();
        $this->migrateHmsEmails();
        $this->migrateVeningLocations();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info('Total run took ' . $start->diff(Carbon::now())->format('%H:%i:%s'));
    }

    protected function migrateStatusUpdates()
    {
        $oldTableName = 'status_updates';
        $newTableName = 'role_updates';
        $columns = [
            'id' => 'id',
            'member_id' => 'user_id',
            'admin_id' => 'update_by_user_id',
            'old_status' => 'removed_role_id',
            'new_status' => 'added_role_id',
            'timestamp' => 'created_at',
        ];

        $this->info('Migrating ' . $oldTableName);
        $startTime = Carbon::now();
        $oldData = DB::connection('instrumentation')->table($oldTableName)->get();

        /*
         *   1   Prospective Member
         *   2   Waiting for contact details
         *   3   Waiting for Membership Admin to approve contact details > member.approval
         *   4   Waiting for standing order payment > member.payment
         *   5   Current Member > member.current
         *   6   Ex Member > member.ex
         *   7   Banned Member > member.banned
         */
        $roleIds = [
            3 => DB::table('roles')->where('name', 'member.approval')->value('id'),
            4 => DB::table('roles')->where('name', 'member.payment')->value('id'),
            5 => DB::table('roles')->where('name', 'member.current')->value('id'),
            6 => DB::table('roles')->where('name', 'member.ex')->value('id'),
            7 => DB::table('roles')->where('name', 'member.banned')->value('id'),
        ];

        $roleUpdates = [];

        foreach ($oldData as $row) {
            // deal with old status first
            if ($row->old_status > 2) {
                $roleUpdates[] = [
                    'user_id' => $row->member_id,
                    'added_role_id' => null,
                    'removed_role_id' => $roleIds[$row->old_status],
                    'created_at' => $row->timestamp,
                    'update_by_user_id' => $row->admin_id,
                ];
            }

            if ($row->new_status > 2) {
                $roleUpdates[] = [
                    'user_id' => $row->member_id,
                    'added_role_id' => $roleIds[$row->new_status],
                    'removed_role_id' => null,
                    'created_at' => $row->timestamp,
                    'update_by_user_id' => $row->admin_id,
                ];
            }
        }

        DB::statement("TRUNCATE TABLE $newTableName;");

        $dataChunks = array_chunk($roleUpdates, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table($newTableName)->insert($dataChunk);
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    protected function migrateMembers()
    {
        $this->info('Migrating members');
        /* user table
            id
            firstname
            lastname
            username
            email
            remember_token
            deleted_at
            created_at
            updated_at
            account_id
            email_verified_at

           profile table
            user_id
            join_date
            unlock_text
            credit_limit
            address_1
            address_2
            address_3
            address_city
            address_county
            address_postcode
            contact_number
            created_at
            updated_at
            date_of_birth
            balance
         */
        $startTime = Carbon::now();
        $oldData = DB::connection('instrumentation')->table('members')->get();

        $users = [];
        $profiles = [];
        foreach ($oldData as $row) {
            if ($row->member_id == 45 || $row->member_id == 128) {
                // drop old bar camp and YRS users
                continue;
            }
            if ($row->member_status <= 2) {
                continue;
            }

            $user = [
                'id' => $row->member_id,
                'firstname' => $row->firstname,
                'lastname' => $row->surname,
                'username' => $row->username,
                'email' => $row->email,
                'created_at' => $row->join_date != '0000-00-00' ? $row->join_date : Carbon::now(),
                'updated_at' => Carbon::now(),
                'account_id' => $row->account_id,
            ];
            $profile = [
                'user_id' => $row->member_id,
                'join_date' => $row->join_date != '0000-00-00' ? $row->join_date : null,
                'unlock_text' => $row->unlock_text,
                'credit_limit' => 2000,
                'address_1' => $row->address_1,
                'address_2' => $row->address_2,
                'address_city' => $row->address_city,
                'address_postcode' => $row->address_postcode,
                'contact_number' => $row->contact_number,
                'created_at' => $row->join_date != '0000-00-00' ? $row->join_date : Carbon::now(),
                'updated_at' => Carbon::now(),
                'balance' => $row->balance,
            ];

            $users[] = $user;
            $profiles[] = $profile;
        }

        DB::statement('TRUNCATE TABLE user');

        $dataChunks = array_chunk($users, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('user')->insert($dataChunk);
        }

        DB::statement('TRUNCATE TABLE profile');

        $dataChunks = array_chunk($profiles, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('profile')->insert($dataChunk);
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    protected function migrateMemberStatus()
    {
        $this->info('Migrating members status');
        /*
         *   1   Prospective Member
         *   2   Waiting for contact details
         *   3   Waiting for Membership Admin to approve contact details > member.approval
         *   4   Waiting for standing order payment > member.payment
         *   5   Current Member > member.current
         *   6   Ex Member > member.ex
         *   7   Banned Member > member.banned
         */
        $roleIds = [
            3 => DB::table('roles')->where('name', 'member.approval')->value('id'),
            4 => DB::table('roles')->where('name', 'member.payment')->value('id'),
            5 => DB::table('roles')->where('name', 'member.current')->value('id'),
            6 => DB::table('roles')->where('name', 'member.ex')->value('id'),
            7 => DB::table('roles')->where('name', 'member.banned')->value('id'),
            'superUser' => DB::table('roles')->where('name', 'user.super')->value('id'),
        ];

        $startTime = Carbon::now();
        $oldData = DB::connection('instrumentation')->table('members')->get();

        $roleUsers = [];
        foreach ($oldData as $row) {
            if ($row->member_id == 45 || $row->member_id == 128) {
                // drop old bar camp and YRS users
                continue;
            }

            if ($row->member_status <= 2) {
                continue;
            }

            $roleUsers[] = [
                'role_id' => $roleIds[$row->member_status],
                'user_id' => $row->member_id,
            ];

            if ($row->member_id == 1 || $row->member_id == 2) {
                $roleUsers[] = [
                    'role_id' => $roleIds['superUser'],
                    'user_id' => $row->member_id,
                ];
            }
        }

        DB::statement('TRUNCATE TABLE role_user');

        $dataChunks = array_chunk($roleUsers, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('role_user')->insert($dataChunk);
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    protected function migrateTools()
    {
        $this->info('Migrating Tools');
        $startTime = Carbon::now();
        $oldTools = DB::connection('instrumentation')->table('tl_tools')->get();
        DB::statement('TRUNCATE TABLE tools');
        DB::table('permission_role')
            ->leftJoin('roles', 'roles.id', '=', 'permission_role.role_id')
            ->where('name', 'LIKE', 'tools.%')
            ->delete();
        DB::table('roles')
            ->where('name', 'LIKE', 'tools.%')
            ->delete();

        DB::table('permissions')
            ->where('name', 'LIKE', 'tools.%.use')
            ->orWhere('name', 'LIKE', 'tools.%.book')
            ->orWhere('name', 'LIKE', 'tools.%.induct')
            ->orWhere('name', 'LIKE', 'tools.%.book.induction')
            ->orWhere('name', 'LIKE', 'tools.%.maintain')
            ->orWhere('name', 'LIKE', 'tools.%.book.maintenance')
            ->orWhere('name', 'LIKE', 'tools.%.inductor.grant')
            ->delete();

        foreach ($oldTools as $row) {
            $tool = $this->toolManager->create(
                $row->tool_name,
                $row->tool_restrictions,
                $row->tool_pph,
                $row->tool_booking_length,
                $row->tool_length_max,
                $row->tool_bookings_max
            );

            $this->toolManager->enableTool($tool);

            DB::table('tools')->where('id', $tool->getId())->update(['id' => $row->tool_id]);
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    protected function migrateMemberTools()
    {
        $this->info('Migrating member tools');

        $startTime = Carbon::now();
        $oldData = DB::connection('instrumentation')->table('tl_members_tools')->get();

        $tools = DB::table('tools')->get()->toArray();
        $toolRoleIds = [];

        foreach ($tools as $tool) {
            $toolRoleIds[$tool->id] = [
                'MAINTAINER'    => DB::table('roles')
                                        ->where('name', 'LIKE', 'tools.' . camel_case($tool->name) . '.maintainer')
                                        ->value('id'),
                'INDUCTOR'      => DB::table('roles')
                                        ->where('name', 'LIKE', 'tools.' . camel_case($tool->name) . '.inductor')
                                        ->value('id'),
                'USER'          => DB::table('roles')
                                        ->where('name', 'LIKE', 'tools.' . camel_case($tool->name) . '.user')
                                        ->value('id'),
            ];
        }

        // strip any old grants
        $flatToolRoleIds = DB::table('roles')->where('name', 'LIKE', 'tools.%')->pluck('id');
        DB::table('role_user')
            ->whereIn('role_id', $flatToolRoleIds)
            ->delete();

        DB::table('role_updates')
            ->whereIn('added_role_id', $flatToolRoleIds)
            ->delete();

        $roleUsers = [];
        $roleUpdates = [];
        foreach ($oldData as $row) {
            switch ($row->mt_access_level) {
                case 'MAINTAINER':
                    $roleUsers[] = [
                        'role_id' => $toolRoleIds[$row->tool_id]['MAINTAINER'],
                        'user_id' => $row->member_id,
                    ];

                    $roleUpdates[] = [
                        'user_id' => $row->member_id,
                        'added_role_id' => $toolRoleIds[$row->tool_id]['MAINTAINER'],
                        'created_at' => $row->mt_date_inducted,
                        'update_by_user_id' => $row->member_id_induct,
                    ];
                    // No break
                case 'INDUCTOR':
                    $roleUsers[] = [
                        'role_id' => $toolRoleIds[$row->tool_id]['INDUCTOR'],
                        'user_id' => $row->member_id,
                    ];

                    $roleUpdates[] = [
                        'user_id' => $row->member_id,
                        'added_role_id' => $toolRoleIds[$row->tool_id]['INDUCTOR'],
                        'created_at' => $row->mt_date_inducted,
                        'update_by_user_id' => $row->member_id_induct,
                    ];
                    // No break
                default:
                    $roleUsers[] = [
                        'role_id' => $toolRoleIds[$row->tool_id]['USER'],
                        'user_id' => $row->member_id,
                    ];

                    $roleUpdates[] = [
                        'user_id' => $row->member_id,
                        'added_role_id' => $toolRoleIds[$row->tool_id]['USER'],
                        'created_at' => $row->mt_date_inducted,
                        'update_by_user_id' => $row->member_id_induct,
                    ];

                    break;
            }
        }

        $dataChunks = array_chunk($roleUsers, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('role_user')->insert($dataChunk);
        }

        $dataChunks = array_chunk($roleUpdates, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('role_updates')->insert($dataChunk);
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    protected function migrateHmsEmails()
    {
        $oldTableName = 'hms_emails';
        $newTableName = 'emails';
        $columns = [
            'hms_email_id' => 'id',
            // 'member_id' => '',
            'subject' => 'subject',
            'timestamp' => 'sent_at',
        ];

        $this->info('Migrating ' . $oldTableName);
        $startTime = Carbon::now();
        $oldData = DB::connection('instrumentation')->table($oldTableName)->get();

        $emailUser = [];
        $filteredData = $oldData->filter(function ($row, $key) {
            $user = DB::table('user')->where('id', $row->member_id)->first();

            return ! is_null($user);
        });

        $newData = $filteredData->map(function ($row, $key) use ($columns, $newTableName, &$emailUser) {
            $newRow = [];
            foreach ($columns as $oldName => $newName) {
                $newRow[$newName] = $row->$oldName;
            }

            // go fetch user from user table so we can rebuild a plausible email address
            $user = DB::table('user')->where('id', $row->member_id)->first();
            $newRow['to_address'] = serialize(["$user->email" => $user->firstname . ' ' . $user->lastname]);

            // We don't have a full record of the email so these will have to be empty
            $newRow['body'] = '';
            $newRow['full_string'] = '';

            // push user_id out to new join table
            $emailUser[] = [
                'email_id' => $newRow['id'],
                'user_id' => $row->member_id,
            ];

            return $newRow;
        });

        DB::statement("TRUNCATE TABLE $newTableName;");

        $dataChunks = array_chunk($newData->toArray(), 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table($newTableName)->insert($dataChunk);
        }

        DB::statement('TRUNCATE TABLE email_users;');

        $dataChunks = array_chunk($emailUser, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('email_users')->insert($dataChunk);
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    protected function migrateVeningLocations()
    {
        $oldTableName = 'vmc_ref';
        $newTableName = 'vending_locations';
        $columns = [
            'vmc_ref_id' => 'id',
            'vmc_id' => 'vending_machine_id',
            'loc_encoded' => 'encoding',
            'loc_name' => 'name',
        ];

        $this->info('Migrating ' . $oldTableName);
        $startTime = Carbon::now();
        $oldData = DB::connection('instrumentation')->table($oldTableName)->get();

        $newData = $oldData->map(function ($row, $key) use ($columns, $newTableName) {
            $newRow = [];
            foreach ($columns as $oldName => $newName) {
                $newRow[$newName] = $row->$oldName;
            }

            // From old state for find product_id for $newRow['id']
            $state = DB::connection('instrumentation')->table('vmc_state')->where('vmc_ref_id', $row->vmc_ref_id)->first();

            $newRow['product_id'] = $state->product_id;

            return $newRow;
        });

        DB::statement("TRUNCATE TABLE $newTableName;");

        $dataChunks = array_chunk($newData->toArray(), 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table($newTableName)->insert($dataChunk);
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    protected function migrateSimpleMappings()
    {
        $simpleStart = Carbon::now();
        $this->info('Running ' . count($this->mappings) . ' simple migrations');
        foreach ($this->mappings as $oldTableName => $mapping) {
            $this->simpleMigration($oldTableName, $mapping);
        }
        $this->info('Finished simple migrations took ' . $simpleStart->diff(Carbon::now())->format('%H:%i:%s'));
    }

    protected function simpleMigration($oldTableName, $mapping)
    {
        $this->info('Migrating ' . $oldTableName);
        $newTableName = $mapping['newTableName'];
        $startTime = Carbon::now();
        $oldData = DB::connection('instrumentation')->table($oldTableName)->get();

        $newData = $oldData->map(function ($row, $key) use ($mapping, $newTableName) {
            $newRow = [];
            foreach ($mapping['columns'] as $oldName => $newName) {
                if (array_key_exists($oldName, $row)) {
                    $newRow[$newName] = $row->$oldName;

                    // bank transactions
                    if ($newTableName == 'bank_transactions' && $newName == 'amount') {
                        $newRow[$newName] *= 100;
                    }
                }
            }

            return $newRow;
        });

        DB::statement("TRUNCATE TABLE $newTableName;");

        $dataChunks = array_chunk($newData->toArray(), 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table($newTableName)->insert($dataChunk);
        }
        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }
}
