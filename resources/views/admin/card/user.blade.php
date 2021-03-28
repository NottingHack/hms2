@canany(['profile.view.limited', 'profile.view.all'])
<div class="card">
  <div class="card-header">Basic details</div>
  <table class="table">
    <tbody>
      <tr>
        <th scope="row">Status:</th>
        <td>
          <span class="align-middle">
            {{ $user->getMemberStatusString() }}&nbsp;
            <div class="btn-group float-right" role="group">
              @canany('profile.view.limited', 'profile.view.all')
              <a href="{{ route('users.admin.role-updates', $user->getId()) }}" class="btn btn-primary btn-sm"><i class="fas fa-history" aria-hidden="true"></i></a>
              @endcan
            </div>
          </span>
        </td>
      </tr>
      @feature('voting_status')
      @can('profile.view.all')
      @if ($user->can('governance.voting.canVote'))
      <tr>
        <th scope="row">Voting Status:</th>
        <td class="align-middle">
            {{ $votingStatus }}
        </td>
      </tr>
      @endif
      @endcan
      @endfeature
      <tr>
        <th scope="row">Name:</th>
        <td>{{ $user->getFullname() }}</td>
      </tr>
      <tr>
        <th scope="row">Username:</th>
        <td>{{ $user->getUsername() }}</td>
      </tr>
      <tr>
        <th scope="row">Email:</th>
        <td>{{ $user->getEmail() }}</td>
      </tr>
      @if ($user->getProfile() && $user->getProfile()->getJoinDate())
      <tr>
        <th scope="row">Member since:</th>
        <td>{{ $user->getProfile()->getJoinDate()->toDateString() }}</td>
      </tr>
      <tr>
        <th scope="row">Unlock text:</th>
        <td>{{ $user->getProfile()->getUnlockText() }}</td>
      </tr>
      @endif
    </tbody>
  </table>
  <div class="card-footer">
    @can('profile.edit.all')
    <a href="{{ route('users.edit', $user->getId()) }}" class="btn btn-primary mb-1§"><i class="fas fa-pencil" aria-hidden="true"></i> Update details</a>
    @elsecan('profile.edit.limited')
    <a href="{{ route('users.admin.edit-email', $user->getId()) }}" class="btn btn-primary mb-1§"><i class="fas fa-pencil" aria-hidden="true"></i> Update email</a>
    @endcan
  </div>
</div>
@endcanany
