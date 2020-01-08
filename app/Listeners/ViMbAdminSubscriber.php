<?php

namespace App\Listeners;

use Exception;
use HMS\Entities\Role;
use LWK\ViMbAdmin\Model\Link;
use LWK\ViMbAdmin\Model\Alias;
use LWK\ViMbAdmin\Model\Error;
use LWK\ViMbAdmin\Model\Domain;
use LWK\ViMbAdmin\Model\Mailbox;
use App\Events\Roles\RoleCreated;
use LWK\ViMbAdmin\ViMbAdminClient;
use HMS\Repositories\UserRepository;
use App\Events\Roles\UserAddedToRole;
use App\Events\Users\UserEmailChanged;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\Roles\UserRemovedFromRole;
use Illuminate\Contracts\Queue\ShouldQueue;

class ViMbAdminSubscriber implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * ViMAdmin client, used to talk to the external API.
     *
     * @var ViMbAdminClient
     */
    protected $client;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Create the event listener.
     *
     * @param ViMbAdminClient $client
     * @param UserRepository $userRepository
     */
    public function __construct(ViMbAdminClient $client, UserRepository $userRepository)
    {
        $this->client = $client;
        $this->userRepository = $userRepository;
    }

    /**
     * Handle role created events.
     * If the updateTeamEmail field is set we need to create a new mailbox.
     *
     * @param RoleCreated $event
     *
     * @throws Exception
     */
    public function onRoleCreated(RoleCreated $event)
    {
        if ($event->role->getEmail()) {
            // See if there is allrady an alias for this role
            $alias = $this->getAliasForRole($event->role);
            if ($alias instanceof Alias) {
                return;
            }

            $mailboxEmail = $event->role->getEmail();
            if (! filter_var($mailboxEmail, FILTER_VALIDATE_EMAIL)) {
                // bugger this should not happen. throw an error??
                throw new Exception('Role email address ' . $mailboxEmail . ' is not valid');
            }

            $domainName = explode('@', $mailboxEmail)[1];
            $domain = $this->client->findDomains($domainName)[0];
            if (! $domain instanceof Domain) {
                throw new Exception('Domain for ' . $domainName . ' does not exist in ViMAdmin and we cant create it');
            }

            $mailbox = Mailbox::create($mailboxEmail, $event->role->getDisplayname(), $domain);

            $response = $this->client->createMailbox($mailbox);
            if (! $response instanceof Mailbox) {
                throw new Exception('Failed to create Mailbox for Role: ' . $event->role->getName());
            }
        }
    }

    /**
     * Handle user added to role events.
     * If the updateTeamEmail field is set we need to add the user to the alias.
     *
     * @param UserAddedToRole $event
     *
     * @throws Exception
     */
    public function onUserAddedToRole(UserAddedToRole $event)
    {
        if ($event->role->getEmail()) {
            $alias = $this->getAliasForRole($event->role);
            $email = strtolower($event->user->getEmail());

            if ($event->role->getName() == Role::TEAM_TRUSTEES) {
                $email = strtolower(
                    $event->user->getFirstname()
                    . '.' . $event->user->getLastname()
                    . '@nottinghack.org.uk'
                );
                // TODO: check $email is now valid (utf8?)

                // TODO: check if there is a mailbox with address $trusteeEmail
                // if not then create one
                    // need to emial password to $user with link ot change it
                    // https://vba.lwk.me/auth/change-password
                    // or add a new view to allow password change from hms
            }

            if (is_null($alias)) {
                // did not find the alias, just quitely fail
                // TODO: trow??
                return;
            }

            $alias->addForwardAddress($email);

            // save the updated alias back to the external API
            $response = $this->client->updateAlias($alias);
            if (! $response instanceof Link) {
                throw new Exception('Alias update failed with Error: ' . $response);
            }
        }
    }

    /**
     * Handle user removed from role events.
     * If the updateTeamEmail field is set we need to remove the user from the alias.
     *
     * @param UserRemovedFromRole $event
     *
     * @throws Exception
     */
    public function onUserRemovedFromRole(UserRemovedFromRole $event)
    {
        if ($event->role->getEmail()) {
            $alias = $this->getAliasForRole($event->role);
            $email = strtolower($event->user->getEmail());

            if ($event->role->getName() == Role::TEAM_TRUSTEES) {
                $email = strtolower(
                    $event->user->getFirstname()
                    . '.' . $event->user->getLastname()
                    . '@nottinghack.org.uk'
                );
                // TODO: check $email is now valid (utf8?)
            }

            if (is_null($alias)) {
                // did not find the alias, just quitely fail
                // TODO: trow??
                return;
            }

            $alias->removeForwardAddress($email);

            // save the updated alias back to the external API
            $response = $this->client->updateAlias($alias);
            if (! $response instanceof Link) {
                throw new Exception('Alias update failed with Error: ' . $response);
            }
        }
    }

    /**
     * Given a role update alias with a newly calculated set of goto addresses.
     *
     * @param Role $role
     *
     * @throws Exception
     *
     * @return null|Alias
     */
    public function getAliasForRole(Role $role)
    {
        $aliasEmail = $role->getEmail();
        if (! filter_var($aliasEmail, FILTER_VALIDATE_EMAIL)) {
            // bugger this should not happen. throw an error??
            throw new Exception('Role email address ' . $aliasEmail . ' is not valid');
        }

        $domainName = explode('@', $aliasEmail)[1];

        // now we have done our prep, time to grab the alias from the external API
        $aliases = $this->client->findAliasesForDomain($domainName, $aliasEmail);
        if ($aliases instanceof Error) {
            throw new Exception('Unable to get Alias for ' . $aliasEmail);
        }

        if (empty($aliases)) {
            return null;
        }

        return $aliases[0];
    }

    /**
     * Update any email aliases when a user changes thier email address.
     *
     * @param UserEmailChanged $event
     *
     * @throws Exception
     */
    public function onUserEmailChanged(UserEmailChanged $event)
    {
        // get a fresh copy of the user as the roles object will not be hydrated
        $user = $this->userRepository->findOneById($event->user->getId());

        $roles = $user->getRoles();

        foreach ($roles as $role) {
            if ($role->getName() == Role::TEAM_TRUSTEES) {
                // skip as they have first.last emial boxes
                continue;
            }
            if ($role->getEmail()) {
                $alias = $this->getAliasForRole($role);
                $alias->removeForwardAddress(strtolower($event->oldEmail));
                $alias->addForwardAddress(strtolower($user->getEmail()));

                // save the updated alias back to the external API
                $response = $this->client->updateAlias($alias);
                if (! $response instanceof Link) {
                    throw new Exception('Alias update failed with Error: ' . $response);
                }
            }
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Roles\RoleCreated',
            'App\Listeners\ViMbAdminSubscriber@onRoleCreated'
        );

        $events->listen(
            'App\Events\Roles\UserAddedToRole',
            'App\Listeners\ViMbAdminSubscriber@onUserAddedToRole'
        );

        $events->listen(
            'App\Events\Roles\UserRemovedFromRole',
            'App\Listeners\ViMbAdminSubscriber@onUserRemovedFromRole'
        );

        $events->listen(
            'App\Events\Users\UserEmailChanged',
            'App\Listeners\ViMbAdminSubscriber@onUserEmailChanged'
        );
    }

    /*
     * Deal with failed updates somehow.
     * @param OrderShipped $event
     * @param \Exception $exception
     */
    // public function failed(OrderShipped $event, $exception)
    // {
    //     // TODO: notify software team?
    // }
}
