<?php

namespace App\Listeners;

use Exception;
use HMS\Entities\Role;
use LWK\ViMbAdmin\Model\Link;
use LWK\ViMbAdmin\Model\Alias;
use LWK\ViMbAdmin\Model\Domain;
use LWK\ViMbAdmin\Model\Mailbox;
use App\Events\Roles\RoleCreated;
use LWK\ViMbAdmin\ViMbAdminClient;
use App\Events\Roles\UserAddedToRole;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\Roles\UserRemovedFromRole;
use Illuminate\Contracts\Queue\ShouldQueue;

class ViMbAdminSubscriber implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * ViMAdmin client, used to talk to the external API.
     *
     * @var LWK\ViMbAdmin\ViMbAdminClient
     */
    protected $client;

    /**
     * Create the event listener.
     *
     * @param LWK\ViMbAdmin\ViMbAdminClient $client
     */
    public function __construct(ViMbAdminClient $client)
    {
        $this->client = $client;
    }

    /**
     * Handle role created events.
     * If the updateTeamEmail field is set we need to create a new mailbox.
     *
     * @param  RoleCreated $event
     */
    public function onRoleCreated(RoleCreated $event)
    {
        if ($event->role->getEmail()) {
            // See if there is allrady an alias for this role
            $alias = $this->getAliasForRole($event->role, true);
            if ($alias instanceof Alias) {
                return;
            }

            $mailboxEmail = $event->role->getEmail();
            if ( ! filter_var($mailboxEmail, FILTER_VALIDATE_EMAIL)) {
                // bugger this should not happen. throw an error??
                throw new Exception('Role email address '.$mailboxEmail.' is not valid');
            }

            $domainName = explode('@', $mailboxEmail)[1];
            $domain = $this->client->findDomains($domainName)[0];
            if ( ! $domain instanceof Domain) {
                throw new Exception('Domain for '.$domainName.' does not exist in ViMAdmin and we cant create it');
            }

            $mailbox = Mailbox::create($mailboxEmail, $event->role->getDisplayname(), $domain);

            $response = $this->client->createMailbox($mailbox);
            if ( ! $response instanceof Mailbox) {
                throw new Exception('Failed to create Mailbox for Role: '.$event->role->getName());
            }
        }
    }

    /**
     * Handle user added to role events.
     * If the updateTeamEmail field is set we need to add the user to the alias.
     *
     * @param  UserAddedToRole $event
     */
    public function onUserAddedToRole(UserAddedToRole $event)
    {
        if ($event->role->getEmail()) {
            $alias = $this->getAliasForRole($event->role);
            $alias->addForwardAddress($event->user->getEmail());

            // save the updated alias back to the external API
            $response = $this->client->updateAlias($alias);
            if ( ! $response instanceof Link) {
                throw new Exception('Alias update failed with Error: '.$response);
            }
        }
    }

    /**
     * Handle user removed from role events.
     * If the updateTeamEmail field is set we need to remove the user from the alias.
     *
     * @param  UserRemovedFromRole $event
     */
    public function onUserRemovedFromRole(UserRemovedFromRole $event)
    {
        if ($event->role->getEmail()) {
            $alias = $this->getAliasForRole($event->role);
            $alias->removeForwardAddress($event->user->getEmail());

            // save the updated alias back to the external API
            $response = $this->client->updateAlias($alias);
            if ( ! $response instanceof Link) {
                throw new Exception('Alias update failed with Error: '.$response);
            }
        }
    }

    /**
     * Given a role update alias with a newly calculated set of goto addresses.
     *
     * @param  Role   $role
     * @param  bool $skipException
     * @return Alias|Error
     */
    public function getAliasForRole(Role $role, $skipException = flase)
    {
        $aliasEmail = $role->getEmail();
        if ( ! filter_var($aliasEmail, FILTER_VALIDATE_EMAIL)) {
            // bugger this should not happen. throw an error??
            throw new Exception('Role email address '.$aliasEmail.' is not valid');
        }

        $domainName = explode('@', $aliasEmail)[1];

        // now we have done our prep, time to grab the alias from the external API
        $alias = $this->client->findAliasesForDomain($domainName, $aliasEmail);
        if ( ! $skipException && ! $alias instanceof Alias) {
            throw new Exception('Unable to get Alias for '.$aliasEmail);
        }

        return $alias;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
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
    }

    /*
     * Deal with failed updates somehow.
     * @param  OrderShipped $event
     * @param  \Exception   $exception
     */
    // public function failed(OrderShipped $event, $exception)
    // {
    //     // TODO: notify software team?
    // }
}
