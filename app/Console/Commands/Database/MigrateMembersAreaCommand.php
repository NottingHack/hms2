<?php

namespace App\Console\Commands\Database;

use Carbon\Carbon;
use HMS\Entities\Role;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use HMS\Entities\Banking\BankType;
use Illuminate\Support\Facades\DB;
use HMS\Repositories\RoleRepository;
use HMS\User\Permissions\RoleManager;
use HMS\Factories\Gatekeeper\PinFactory;
use HMS\Entities\Gatekeeper\RfidTagState;
use HMS\Factories\Banking\AccountFactory;
use HMS\Entities\Gatekeeper\AccessLogResult;
use HMS\Repositories\Gatekeeper\PinRepository;
use HMS\Repositories\Banking\AccountRepository;

class MigrateMembersAreaCommand extends Command
{
    protected $truncateTables = [
        'access_logs',
        'accounts',
        'addresses',
        'bank_transactions',
        'banks',
        'bells',
        // 'blacklist_usernames',
        'bookable_areas',
        'bookings',
        // 'buildings',
        'door_bell',
        // 'doors',
        // 'electric_meters',
        'electric_readings',
        'email_users',
        'emails',
        'events',
        'failed_jobs',
        // 'floors',
        'invites',
        'invoices',
        // 'label_templates',
        'light_level',
        'light_lighting_pattern',
        'lighting_controllers',
        'lighting_input_channels',
        'lighting_output_channels',
        'lighting_patterns',
        'lights',
        // 'links',
        'meeting_absentee',
        'meeting_attendee',
        'meetings',
        'member_boxes',
        'member_projects',
        'membership_rejected_logs',
        'membership_status_notifications',
        // 'meta',
        'oauth_access_tokens',
        'oauth_auth_codes',
        'oauth_clients',
        'oauth_personal_access_clients',
        'oauth_refresh_tokens',
        'password_resets',
        // 'permission_role',
        // 'permissions',
        'pins',
        'products',
        'profile',
        'proxies',
        'purchase_payment',
        'rfid_tags',
        'role_updates',
        'role_user',
        // 'roles',
        // 'rooms',
        'service_status',
        'snackspace_debts',
        'snackspace_emails',
        'stripe_charges',
        'stripe_events',
        'temperature',
        'temporary_access_bookings',
        'tool_usages',
        'tools',
        'transactions',
        'user',
        'vend_logs',
        'vending_locations',
        'vending_machines',
        'vimbadmin_tokens',
        'webhook_calls',
        'zone_occupancy_logs',
        'zone_occupants',
        // 'zones',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hms:database:migrateMembersArea';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data form old members-area database';

    /**
     * @var null|\DateTimeZone
     */
    private static $utc = null;

    /**
     * @var null|\DateTimeZone
     */
    private static $gmt = null;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (is_null(self::$utc)) {
            self::$utc = new \DateTimeZone('UTC');
        }
        if (is_null(self::$gmt)) {
            self::$gmt = new \DateTimeZone('Europe/London');
        }

        $this->info('Please provide details for connecting to the members-area database.');
        $membersAreaHost = $this->anticipate('Host? [127.0.0.1]', ['127.0.0.1']) ?: '127.0.0.1';
        $membersAreaDatabase = $this->anticipate('Database name? [members]', ['members', 'smi_members']) ?: 'members';
        $membersAreaUser = $this->anticipate('User? [hms]', ['hms', 'homestead']) ?: 'hms';
        $membersAreaPassword = $this->secret('Password? [secret]') ?: 'secret';

        $start = Carbon::now();
        config(['database.connections.members-area' => [
            'driver' => 'mysql',
            'host' => $membersAreaHost,
            'port' => '3306',
            'database' => $membersAreaDatabase,
            'username' => $membersAreaUser,
            'password' => $membersAreaPassword,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'strict' => true,
            'engine' => null,
        ]]);

        DB::purge('members-area');
        DB::connection('members-area')->statement('RESET QUERY CACHE;');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($this->truncateTables as $table) {
            DB::statement('TRUNCATE TABLE ' . $table);
        }

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

        // service injection ;)
        $this->laravel->call([$this, 'migrateRoles']);
        $this->laravel->call([$this, 'migrateUsers']);
        $this->laravel->call([$this, 'migrateTransactionAccounts']);
        $this->laravel->call([$this, 'migrateTransactions']);
        $this->laravel->call([$this, 'migratePayment']);
        $this->laravel->call([$this, 'migrateRoleUser']);
        $this->laravel->call([$this, 'migrateRfidTag']);
        $this->laravel->call([$this, 'migrateRfidentry']);

        $this->laravel->call([$this, 'generatePins']);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info('Total run took ' . $start->diff(Carbon::now())->format('%H:%i:%s'));
    }

    /**
     * Migrate roles.
     *
     * @param RoleManager $roleManager
     * @param RoleRepository $roleRepository
     *
     * @return void
     */
    public function migrateRoles(
        RoleManager $roleManager,
        RoleRepository $roleRepository
    ) {
        $this->info('Migrating roles');
        $startTime = Carbon::now();
        $oldData = DB::connection('members-area')->table('role')->get();

        $roleNamesToSkip = collect([
            'Registered User',
            'Trustee',
            'Member',
            'Admin',
        ]);

        foreach ($oldData as $row) {
            if ($roleNamesToSkip->contains($row->name)) {
                continue;
            }

            if (! $roleRepository->findOneByName('team.' . Str::camel($row->name))) {
                $roleManager->createTeam(
                    'team.' . Str::camel($row->name),
                    $row->name,
                    json_decode($row->meta)->description,
                );
            }
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    /**
     * Migrate the Users.
     *
     * @param  AccountFactory    $accountFactory
     * @param  AccountRepository $accountRepository
     *
     * @return void
     */
    public function migrateUsers(
        AccountFactory $accountFactory,
        AccountRepository $accountRepository
    ) {
        $this->info('Migrating users');
        /* user table
            id
            firstname
            lastname
            username
            email
            password
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
        $oldData = DB::connection('members-area')->table('user')->get();

        $users = [];
        $profiles = [];
        foreach ($oldData as $row) {
            // Create and account with a new ref and build the legacy ref (M000000) for the user id
            $account = $accountFactory->createNewAccount();
            $account->setLegacyRef('M' . str_pad($row->id, 6, '0', STR_PAD_LEFT));
            $accountRepository->save($account);

            $user = [
                'id' => $row->id,
                'firstname' => Str::beforeLast($row->fullname, ' ') ?: '',
                'lastname' => Str::afterLast($row->fullname, ' ') ?: '',
                'username' => $row->username,
                'email' => $row->email,
                'password' => $row->hashed_password,
                'created_at' => $row->createdAt,
                'updated_at' => Carbon::now(self::$utc),
                'account_id' => $account->getId(),
                'email_verified_at' => $row->verified,
            ];

            $profile = [
                'user_id' => $row->id,
                // 'join_date' => null, // need to work this out from other date later
                'unlock_text' => 'Welcome ' . Str::beforeLast($row->fullname, ' ') ?: '',
                'address_1' => $row->address,
                'created_at' => $row->createdAt,
                'updated_at' => Carbon::now(self::$utc),
            ];

            $users[] = $user;
            $profiles[] = $profile;
        }

        $dataChunks = array_chunk($users, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('user')->insert($dataChunk);
        }

        $dataChunks = array_chunk($profiles, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('profile')->insert($dataChunk);
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    public function migrateTransactionAccounts()
    {
        $this->info('Migrating transaction accounts');
        $startTime = Carbon::now();
        $oldData = DB::connection('members-area')->table('transaction_account')->get();

        $settings = collect(DB::connection('members-area')->table('setting')->get());
        $settings->each(function ($row) {
            $row->meta = json_decode($row->meta);
        });

        $banks = [];
        foreach ($oldData as $row) {
            $banks[] = [
                'id' => $row->id,
                'name' => $row->name,
                'sort_code' => substr($row->identifier, 0, 2) . '-' . substr($row->identifier, 2, 2) . '-' . substr($row->identifier, 3, 2),
                'account_number' => substr($row->identifier, 6),
                'account_name' => $settings
                    ->firstWhere('name', 'plugin.members-area-payments') // should really guard null
                    ->meta->settings->payee,
                'type' => BankType::AUTOMATIC,
            ];
        }

        $lastId = end($banks)['id'];

        $banks[] = [
            'id' => ++$lastId,
            'name' => 'Cash',
            'sort_code' => 'CASH',
            'account_number' => 'CASH',
            'account_name' => '',
            'type' => BankType::CASH,
        ];

        $banks[] = [
            'id' => ++$lastId,
            'name' => 'GoCardless',
            'sort_code' => '',
            'account_number' => substr(
                $settings->firstWhere('name', 'plugin.members-area-gocardless') // should really guard null
                    ->meta->settings->merchantId,
                0,
                8 // account_number has max length of 8
            ),
            'account_name' => 'GoCardless',
            'type' => BankType::MANUAL,
        ];

        $dataChunks = array_chunk($banks, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('banks')->insert($dataChunk);
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    public function migrateTransactions()
    {
        $this->info('Migrating transaction');
        $startTime = Carbon::now();
        $oldData = DB::connection('members-area')->table('transaction')->get();

        $bankTransactions = [];
        foreach ($oldData as $row) {
            $bankTransactions[] = [
                'id' => $row->id,
                'transaction_date' => $row->when,
                'description' => $row->description . ' ' . $row->fitid,
                'amount' => $row->amount,
                'bank_id' => $row->transaction_account_id,
                // 'account_id' => $row->, // will populate this when migrating payment
            ];
        }

        $dataChunks = array_chunk($bankTransactions, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('bank_transactions')->insert($dataChunk);
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    public function migratePayment()
    {
        $this->info('Migrating payment');
        $startTime = Carbon::now();
        $oldData = DB::connection('members-area')->table('payment')->get();
        $userAccountId = DB::table('user')->select('id', 'account_id')->get()->pluck('account_id', 'id');
        $bankCashId = DB::table('banks')->select('id')->where('type', BankType::CASH)->value('id');
        $bankGCId = DB::table('banks')->select('id')->where('name', 'GoCardless')->value('id');

        $bankTransactions = [];
        $bankTransactionsAcounts = [];
        foreach ($oldData as $row) {
            if ($row->status != 'paid') {
                continue;
            } elseif ($row->transaction_id) {
                DB::table('bank_transactions')
                    ->where('id', $row->transaction_id)
                    ->update([
                        'account_id' => $userAccountId[$row->user_id],
                    ]);
            } else {
                // should we expand period_count out
                // should transaction_date be when or period_from?

                $description = 'Payment Id: ' . $row->id . ' Period From: ' . $row->period_from . ' Count: ' . $row->period_count;
                if ($row->type == 'GC') {
                    $description .= ' GC BillId: ' . json_decode($row->meta)->gocardlessBillId;
                }

                $bankTransactions[] = [
                    'transaction_date' => $row->when,
                    'description' => $description,
                    'amount' => $row->amount,
                    'bank_id' => $row->type == 'GC' ? $bankGCId : $bankCashId,
                    'account_id' => $userAccountId[$row->user_id],
                ];
            }
        }

        $dataChunks = array_chunk($bankTransactions, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('bank_transactions')->insert($dataChunk);
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    public function migrateRoleUser()
    {
        $this->info('Migrating role user');
        $startTime = Carbon::now();
        $oldData = DB::connection('members-area')->table('role_user')->get();
        $oldData->each(function ($row) {
            $row->meta = json_decode($row->meta);
        });

        $roleIds = [
            Role::MEMBER_APPROVAL => DB::table('roles')->where('name', Role::MEMBER_APPROVAL)->value('id'),
            Role::MEMBER_PAYMENT => DB::table('roles')->where('name', Role::MEMBER_PAYMENT)->value('id'),
            Role::MEMBER_CURRENT => DB::table('roles')->where('name', Role::MEMBER_CURRENT)->value('id'),
            Role::MEMBER_EX => DB::table('roles')->where('name', Role::MEMBER_EX)->value('id'),
            Role::SUPERUSER => DB::table('roles')->where('name', Role::SUPERUSER)->value('id'),
            Role::TEAM_TRUSTEES => DB::table('roles')->where('name', Role::TEAM_TRUSTEES)->value('id'),
        ];
        $memberRoleIds = DB::table('roles')->where('name', 'LIKE', 'member.%')->pluck('id');

        $oldRoleMemberId = DB::connection('members-area')->table('role')->where('name', 'Member')->value('id');
        $oldRoleAdminId = DB::connection('members-area')->table('role')->where('name', 'Admin')->value('id');
        $oldRoles = DB::connection('members-area')->table('role')->get();

        $roleNamesToSkip = collect([
            'Registered User',
            'Trustee',
            'Member',
            'Admin',
        ]);

        $oldToNewIds = [];
        foreach ($oldRoles as $oldRole) {
            if ($roleNamesToSkip->contains($oldRole->name)) {
                continue;
            }

            $oldToNewIds[$oldRole->id] = DB::table('roles')
                ->where('name', 'team.' . Str::camel($oldRole->name))
                ->value('id');
        }

        $roleUsers = collect();
        $roleUpdates = [];
        foreach ($oldData as $row) {
            if (is_null($row->approved) && $row->rejected) {
                // Role was never approved so nothing to do?
                continue;
            }

            switch ($row->role_id) {
                case 1:
                    // Registered User
                    $roleUpdates[] = [
                        'user_id' => $row->user_id,
                        'added_role_id' => $roleIds[Role::MEMBER_APPROVAL],
                        'removed_role_id' => null,
                        'created_at' => $row->createdAt,
                        'update_by_user_id' => null,
                    ];

                    if (is_null($row->approved)) {
                        // Role::MEMBER_APPROVAL

                        // remove any other MEMBER_ Role first
                        $roleUsers = $roleUsers->reject(function ($roleUser) use ($row, $memberRoleIds) {
                            return $roleUser['user_id'] == $row->user_id
                                && $memberRoleIds->contains($roleUser['role_id']);
                        });

                        $roleUsers->push([
                            'role_id' => $roleIds[Role::MEMBER_APPROVAL],
                            'user_id' => $row->user_id,
                        ]);
                    } else {
                        // Role::MEMBER_PAYMENT

                        // remove any other MEMBER_ Role first
                        $roleUsers = $roleUsers->reject(function ($roleUser) use ($row, $memberRoleIds) {
                            return $roleUser['user_id'] == $row->user_id
                                && $memberRoleIds->contains($roleUser['role_id']);
                        });

                        $roleUsers->push([
                            'role_id' => $roleIds[Role::MEMBER_PAYMENT],
                            'user_id' => $row->user_id,
                        ]);

                        $roleUpdates[] = [
                            'user_id' => $row->user_id,
                            'added_role_id' => null,
                            'removed_role_id' => $roleIds[Role::MEMBER_APPROVAL],
                            'created_at' => (new Carbon($row->approved))->toDateTimeString(),
                            'update_by_user_id' => null,
                        ];

                        $roleUpdates[] = [
                            'user_id' => $row->user_id,
                            'added_role_id' => $roleIds[Role::MEMBER_PAYMENT],
                            'removed_role_id' => null,
                            'created_at' => (new Carbon($row->approved))->toDateTimeString(),
                            'update_by_user_id' => null,
                        ];
                    }

                    break;
                case 2:
                    // Trustee Role > Role::TEAM_TRUSTEES

                    // added record
                    $roleUpdates[] = [
                        'user_id' => $row->user_id,
                        'added_role_id' => $roleIds[Role::TEAM_TRUSTEES],
                        'removed_role_id' => null,
                        'created_at' => (new Carbon($row->approved))->toDateTimeString(),
                        'update_by_user_id' => null,
                    ];

                    if ($row->rejected) {
                        // removed record
                        $roleUpdates[] = [
                            'user_id' => $row->user_id,
                            'added_role_id' => null,
                            'removed_role_id' => $roleIds[Role::TEAM_TRUSTEES],
                            'created_at' => (new Carbon($row->rejected))->toDateTimeString(),
                            'update_by_user_id' => $row->meta->rejectedBy,
                        ];
                    } else {
                        // current role
                        $roleUsers->push([
                            'role_id' => $roleIds[Role::TEAM_TRUSTEES],
                            'user_id' => $row->user_id,
                        ]);
                    }

                    break;
                case $oldRoleMemberId:
                    // Member > Role::MEMBER_CURRENT or Role::MEMBER_EX
                    if (is_null($row->approved) && is_null($row->rejected)) {
                        // Role was never approved so nothing to do
                        break;
                    }

                    $roleUpdates[] = [
                        'user_id' => $row->user_id,
                        'added_role_id' => null,
                        'removed_role_id' => $roleIds[Role::MEMBER_PAYMENT],
                        'created_at' => (new Carbon($row->approved))->toDateTimeString(),
                        'update_by_user_id' => null,
                    ];

                    $roleUpdates[] = [
                        'user_id' => $row->user_id,
                        'added_role_id' => $roleIds[Role::MEMBER_CURRENT],
                        'removed_role_id' => null,
                        'created_at' => (new Carbon($row->approved))->toDateTimeString(),
                        'update_by_user_id' => null,
                    ];

                    if ($row->rejected) {
                        $roleUpdates[] = [
                            'user_id' => $row->user_id,
                            'added_role_id' => null,
                            'removed_role_id' => $roleIds[Role::MEMBER_CURRENT],
                            'created_at' => (new Carbon($row->rejected))->toDateTimeString(),
                            'update_by_user_id' => null,
                        ];

                        $roleUpdates[] = [
                            'user_id' => $row->user_id,
                            'added_role_id' => $roleIds[Role::MEMBER_EX],
                            'removed_role_id' => null,
                            'created_at' => (new Carbon($row->rejected))->toDateTimeString(),
                            'update_by_user_id' => null,
                        ];

                        // remove any other MEMBER_ Role first
                        $roleUsers = $roleUsers->reject(function ($roleUser) use ($row, $memberRoleIds) {
                            return $roleUser['user_id'] == $row->user_id
                                && $memberRoleIds->contains($roleUser['role_id']);
                        });

                        $roleUsers->push([
                            'role_id' => $roleIds[Role::MEMBER_EX],
                            'user_id' => $row->user_id,
                        ]);
                    } else {
                        // remove any other MEMBER_ Role first
                        $roleUsers = $roleUsers->reject(function ($roleUser) use ($row, $memberRoleIds) {
                            return $roleUser['user_id'] == $row->user_id
                                && $memberRoleIds->contains($roleUser['role_id']);
                        });

                        $roleUsers->push([
                            'role_id' => $roleIds[Role::MEMBER_CURRENT],
                            'user_id' => $row->user_id,
                        ]);
                    }

                    break;

                case $oldRoleAdminId:
                    // Admin skip for now
                    $this->info('Admin Role skipped, user: ' . $row->user_id);
                    break;

                default:
                    // all other roles
                    if (is_null($row->approved) && is_null($row->rejected)) {
                        // Role was never approved so nothing to do?
                        break;
                    }

                    // added record
                    $roleUpdates[] = [
                        'user_id' => $row->user_id,
                        'added_role_id' => $oldToNewIds[$row->role_id],
                        'removed_role_id' => null,
                        'created_at' => (new Carbon($row->approved))->toDateTimeString(),
                        'update_by_user_id' => null,
                    ];

                    if ($row->rejected) {
                        // removed record
                        $roleUpdates[] = [
                            'user_id' => $row->user_id,
                            'added_role_id' => null,
                            'removed_role_id' => $oldToNewIds[$row->role_id],
                            'created_at' => (new Carbon($row->rejected))->toDateTimeString(),
                            'update_by_user_id' => $row->meta->rejectedBy,
                        ];
                    } else {
                        // A current role

                        $roleUsers->push([
                            'role_id' => $oldToNewIds[$row->role_id],
                            'user_id' => $row->user_id,
                        ]);
                    }

                    break;
            }
        }

        $dataChunks = array_chunk($roleUsers->toArray(), 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('role_user')->insert($dataChunk);
        }

        $dataChunks = array_chunk($roleUpdates, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('role_updates')->insert($dataChunk);
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    public function migrateRfidTag()
    {
        $this->info('Migrating rfid tag');
        $startTime = Carbon::now();
        $oldData = DB::connection('members-area')->table('rfidtag')->get();

        $rfidTags = [];
        foreach ($oldData as $row) {
            $rfidTags[] = [
                'id' => $row->id,
                'user_id' => $row->user_id,
                'rfid_serial' => $row->uid,
                'state' => RfidTagState::ACTIVE,
                'last_used' => $row->updatedAt,
            ];
        }

        $dataChunks = array_chunk($rfidTags, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('rfid_tags')->insert($dataChunk);
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    public function migrateRfidentry()
    {
        $this->info('Migrating rfid entry');
        $startTime = Carbon::now();
        $oldData = DB::connection('members-area')->table('rfidentry')->get();

        $accessLogs = [];
        foreach ($oldData as $row) {
            $accessLogs[] = [
                'id' => $row->id,
                'access_time' => $row->when,
                'rfid_serial' => $row->uid,
                'access_result' => $row->successful ? AccessLogResult::ACCESS_GRANTED : AccessLogResult::ACCESS_DENIED,
                'user_id' => $row->user_id,
                'door_id' => 1,
            ];
        }

        $dataChunks = array_chunk($accessLogs, 1000, true);
        foreach ($dataChunks as $dataChunk) {
            DB::table('access_logs')->insert($dataChunk);
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }

    /**
     * Generate Pins for the Users.
     *
     * @param RoleRepository $roleRepository
     * @param PinFactory     $pinFactory
     * @param PinRepository  $pinRepository
     *
     * @return void
     */
    public function generatePins(
        RoleRepository $roleRepository,
        PinFactory $pinFactory,
        PinRepository $pinRepository
    ) {
        $this->info('Generating pins');
        $startTime = Carbon::now();

        $roles = [Role::MEMBER_CURRENT, Role::MEMBER_YOUNG, Role::MEMBER_EX];
        foreach ($roles as $role) {
            $role = $roleRepository->findOneByName($role);
            foreach ($role->getUsers() as $user) {
                $pin = $pinFactory->createNewEnrollPinForUser($user);
                if (count($user->getRfidTags())) {
                    $pin->setStateCancelled();
                }
                $pinRepository->save($pin);
            }
        }

        $this->info($startTime->diff(Carbon::now())->format('took: %H:%i:%s'));
    }
}
