@canany(['profile.view.limited', 'profile.view.all'])
<div class="card">
  <div class="card-header">Basic details</div>
  <table class="table">
    <tbody>
      <tr>
        <th scope="row">Status:</th>
        <td>
          @isset($memberStatus)
          {{ $memberStatus->getDisplayName() }}
          @else
          Not a Member
          @endisset
      </td>
      </tr>
      <tr>
        <th scope="row">Name:</th>
        <td>{{ $user->getFullName() }}</td>
      </tr>
      <tr>
        <th scope="row">Username:</th>
        <td>{{ $user->getUsername() }}</td>
      </tr>
      <tr>
        <th scope="row">Email:</th>
        <td>{{ $user->getEmail() }}</td>
      </tr>
      @if($user->getProfile() && $user->getProfile()->getJoinDate())
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
