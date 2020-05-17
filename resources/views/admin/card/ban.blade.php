@can('membership.banMember')
<div class="card">
  <div class="card-header">Change membership</div>
  <div class="list-group list-group-flush">
    @if ($user->hasRoleByName(HMS\Entities\Role::MEMBER_TEMPORARYBANNED) || $user->hasRoleByName(HMS\Entities\Role::MEMBER_BANNED))
    <button type="button" class="list-group-item list-group-item-action" data-toggle="confirmation" data-placement="bottom" aria-label="Reinstate Membership">
      <form action="{{ route('users.admin.reinstate', $user->getId()) }}" method="POST" style="display: inline">
        @method('PATCH')
        @csrf
      </form>
      Reinstate Membership
    </button>
    @endif
    @unless($user->hasRoleByName(HMS\Entities\Role::MEMBER_TEMPORARYBANNED))
    <button type="button" class="list-group-item list-group-item-action" data-toggle="confirmation" data-placement="bottom" aria-label="Temporay Ban Member">
      <form action="{{ route('users.admin.temporaryBan', $user->getId()) }}" method="POST" style="display: inline">
        @method('PATCH')
        @csrf
      </form>
      Temporay Ban Member
    </button>
    @endunless
    @unless($user->hasRoleByName(HMS\Entities\Role::MEMBER_BANNED))
    <button type="button" class="list-group-item list-group-item-action" data-toggle="confirmation" data-placement="bottom" aria-label="Ban Member">
      <form action="{{ route('users.admin.ban', $user->getId()) }}" method="POST" style="display: inline">
        @method('PATCH')
        @csrf
      </form>
      Ban Member
    </button>
    @endunless
  </div>
</div>
@endcan
