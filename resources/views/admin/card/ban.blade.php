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

    <div class="modal fade" id="temporaryBanModal" tabindex="-1" role="dialog" aria-labelledby="temporaryBanModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="temporaryBanModalLabel">Temporarily Ban {{$user->getFullname()}}</h4>
          </div>
          <form action="{{ route('users.admin.temporaryBan', $user->getId()) }}" method="POST" style="display: inline">
            @method('PATCH')
            @csrf

            <div class="modal-body">
              <div class="form-group">
                <label for="reason" class="form-label">Reason</label>
                <textarea placeholder="Reason for ban" class="form-control" id="reason" name="reason" required autofocus></textarea>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" id="confirm">Temporarily Ban</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <button type="button" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#temporaryBanModal" data-placement="bottom" aria-label="Temporay Ban Member">
      Temporay Ban Member
    </button>
    @endunless

    @unless($user->hasRoleByName(HMS\Entities\Role::MEMBER_BANNED))

      <div class="modal fade" id="banModal" tabindex="-1" role="dialog" aria-labelledby="banModal" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="banModalLabel">Ban {{$user->getFullname()}}</h4>
            </div>
            <form action="{{ route('users.admin.ban', $user->getId()) }}" method="POST" style="display: inline">
              @method('PATCH')
              @csrf

              <div class="modal-body">
                <div class="form-group">
                  <label for="reason" class="form-label">Reason</label>
                  <textarea placeholder="Reason for ban" class="form-control" id="reason" name="reason" required autofocus></textarea>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="confirm">Ban</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <button type="button" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#banModal" data-placement="bottom" aria-label="Ban Member">
        Ban Member
      </button>
    @endunless
  </div>
</div>
@endcan
