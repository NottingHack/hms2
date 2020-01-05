<?php

namespace HMS\Tools;

use HMS\Entities\User;
use HMS\Entities\Tools\Tool;
use HMS\Entities\Tools\ToolState;
use HMS\Factories\Tools\ToolFactory;
use HMS\Repositories\RoleRepository;
use HMS\User\Permissions\RoleManager;
use HMS\Repositories\PermissionRepository;
use HMS\Repositories\Tools\ToolRepository;
use LaravelDoctrine\ACL\Permissions\Permission;

class ToolManager
{
    const MAINTAINER = 'MAINTAINER';
    const INDUCTOR = 'INDUCTOR';
    const USER = 'USER';

    const GRANT_STRINGS = [
        self::MAINTAINER => 'Maintainer',
        self::INDUCTOR => 'Inductor',
        self::USER => 'User',
    ];

    const PERMISSION_NAME_TEMPLATES = [
        'tools._TOOL_PERMISSION_NAME_.use',                 // can turn on the machin_e
        'tools._TOOL_PERMISSION_NAME_.book',                // can make a tool booking
        'tools._TOOL_PERMISSION_NAME_.induct',              // can induct a member on a tool
        'tools._TOOL_PERMISSION_NAME_.book.induction',      // can book an induction
        'tools._TOOL_PERMISSION_NAME_.maintain',            // can put machine in maintenance mode
        'tools._TOOL_PERMISSION_NAME_.book.maintenance',    // can book a maintenance slot
        'tools._TOOL_PERMISSION_NAME_.inductor.grant',      // can give user inductor role
        'tools.search.users',               // assigend to tool sepcfic mainter roles, allows search for grant
    ];

    const ROLE_TEMPLATES = [
        'tools._TOOL_PERMISSION_NAME_.user' => [
            'name'          => 'Tool: _TOOL_NAME_ User',
            'description'   => 'Can make bookings and use the tool',
            'retained'      => true,
            'permissions'   => [
                'tools._TOOL_PERMISSION_NAME_.use',
                'tools._TOOL_PERMISSION_NAME_.book',
            ],
        ],
        'tools._TOOL_PERMISSION_NAME_.inductor' => [
            'name'          => 'Tool: _TOOL_NAME_ Inductor',
            'description'   => 'Can induct others in the us of the tool',
            'permissions'   => [
                'tools._TOOL_PERMISSION_NAME_.use',
                'tools._TOOL_PERMISSION_NAME_.book',
                'tools._TOOL_PERMISSION_NAME_.induct',
                'tools._TOOL_PERMISSION_NAME_.book.induction',
            ],
        ],
        'tools._TOOL_PERMISSION_NAME_.maintainer' => [
            'name'          => 'Tool: _TOOL_NAME_ Maintainer',
            'description'   => 'Maintainer of the tool, can disable the tool and can appoint inductors',
            'permissions'   => [
                'tools._TOOL_PERMISSION_NAME_.use',
                'tools._TOOL_PERMISSION_NAME_.book',
                'tools._TOOL_PERMISSION_NAME_.maintain',
                'tools._TOOL_PERMISSION_NAME_.book.maintenance',
                'tools._TOOL_PERMISSION_NAME_.inductor.grant',
                'tools.search.users',
            ],
        ],
    ];

    /**
     * @var ToolRepository
     */
    protected $toolRepository;

    /**
     * @var ToolFactory
     */
    protected $toolFactory;

    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var RoleManager
     */
    protected $roleManager;

    /**
     * Create a new Tool Manager instance.
     *
     * @param ToolRepository $toolRepository
     * @param ToolFactory $toolFactory
     * @param PermissionRepository $permissionRepository
     * @param RoleRepository $roleRepository
     * @param RoleManager $roleManager
     */
    public function __construct(
        ToolRepository $toolRepository,
        ToolFactory $toolFactory,
        PermissionRepository $permissionRepository,
        RoleRepository $roleRepository,
        RoleManager $roleManager
    ) {
        $this->toolRepository = $toolRepository;
        $this->toolFactory = $toolFactory;
        $this->permissionRepository = $permissionRepository;
        $this->roleRepository = $roleRepository;
        $this->roleManager = $roleManager;
    }

    /**
     * Function to create a new tool and setup the permissions.
     *
     * @param string $name          Tool name
     * @param bool   $restricted    Does this tool require an induction
     * @param int    $pph           Cost per hour in pence
     * @param int    $bookingLength Default booking length for this tool, minutes
     * @param int    $lengthMax     Maximum amount of time a booking can be made for, minutes
     * @param int    $bookingsMax   Maximum number of bookings a user can have at any one time
     *
     * @return Tool
     */
    public function create(
        string $name,
        bool $restricted,
        int $pph,
        int $bookingLength,
        int $lengthMax,
        int $bookingsMax = 1
    ) {
        // TODO: check tool name is unique

        $_tool = $this->toolFactory->create(
            $name,
            $restricted,
            $pph,
            $bookingLength,
            $lengthMax,
            $bookingsMax
        );

        $this->toolRepository->save($_tool);

        $replace = [
            '_TOOL_PERMISSION_NAME_' => $_tool->getPermissionName(),
            '_TOOL_NAME_' => $_tool->getName(),
        ];

        // Create tool permissions first
        $permissionNames = str_replace(array_keys($replace), array_values($replace), self::PERMISSION_NAME_TEMPLATES);

        $permissions = [];
        foreach ($permissionNames as $permissionName) {
            // see if the permission already exists
            $permission = $this->permissionRepository->findOneByName($permissionName);
            if (is_null($permission)) {
                // permission does not exist
                $permission = new Permission($permissionName);
                $this->permissionRepository->save($permission);
            }
            $permissions[$permissionName] = $permission;
        }

        // Now create tool roles and add permissions
        $rolesTemplates = json_decode(
            str_replace(
                array_keys($replace),
                array_values($replace),
                json_encode(self::ROLE_TEMPLATES)
            ),
            true
        );

        foreach ($rolesTemplates as $roleName => $role) {
            if (! $this->roleRepository->findOneByName($roleName)) {
                $this->roleManager->createRoleFromTemplate($roleName, $role, $permissions);
            }
        }

        // TODO: fire ToolCreated event?
        return $_tool;
    }

    /**
     * Update a specific Tool.
     *
     * @param Tool $tool the Tool we want to update
     * @param array $details fields that need updating
     */
    public function update(Tool $tool, array $details)
    {
        if (isset($details['toolName'])) {
            // TODO: check tool name is unique

            $oldName = $tool->getName();
            $tool->setName($details['toolName']);

            // TODO: update the role and permission names
        }

        if (isset($details['restricted'])) {
            $tool->setRestricted();
        } else {
            $tool->setUnRestricted();
        }
        if (isset($details['cost'])) {
            $tool->setPph($details['cost']);
        }
        if (isset($details['bookingLength'])) {
            $tool->setBookingLength($details['bookingLength']);
        }
        if (isset($details['lengthMax'])) {
            $tool->setBookingLength($details['lengthMax']);
        }
        if (isset($details['bookingsMax'])) {
            $tool->setBookingLength($details['bookingsMax']);
        }

        $this->toolRepository->save($tool);
    }

    /**
     * Enable a tool.
     *
     * @param Tool $tool
     */
    public function enableTool(Tool $tool)
    {
        $tool->setStatus(ToolState::FREE);
        $tool->setStatusText(null);
        $this->toolRepository->save($tool);

        // TODO: fire ToolEnable event
    }

    /**
     * Disable a tool for maintenance.
     *
     * @param Tool $tool
     * @param string $reason
     * @param bool $notify Should we tell user with a booking for this tool.
     */
    public function disableTool(Tool $tool, string $reason, bool $notify = false)
    {
        $tool->setStatus(ToolState::DISABLED);
        $tool->setStatusText($reason);
        $this->toolRepository->save($tool);

        // TODO: fire ToolDisabled event, to notify any users with current booking
    }

    /**
     * Remove a tool and its permissions.
     *
     * @param Tool $tool
     */
    public function removeTool(Tool $tool)
    {
        $roleNames = str_replace(
            '_TOOL_PERMISSION_NAME_',
            $tool->getPermissionName(),
            array_keys(self::ROLE_TEMPLATES)
        );

        foreach ($roleNames as $roleName) {
            $this->roleRepository->removeOneByName($roleName);
        }

        $this->toolRepository->remove($tool);

        // TODO: fire remove tool event?
    }

    /**
     * Grant a given user access to a Tool.
     *
     * @param Tool $tool
     * @param string $grantType
     * @param User $user
     *
     * @return string
     */
    public function grant(Tool $tool, string $grantType, User $user)
    {
        $roleName = 'tools.' . $tool->getPermissionName() . '.' . strtolower($grantType);

        if ($user->hasRoleByName($roleName)) {
            return $user->getFullname() . ' is already a '
                . self::GRANT_STRINGS[$grantType]
                . ' of the ' . $tool->getName();
        }

        $this->roleManager->addUserToRoleByName($user, $roleName);

        return $user->getFullname() . ' appointed as a '
                . self::GRANT_STRINGS[$grantType]
                . ' for the ' . $tool->getName();
    }
}
