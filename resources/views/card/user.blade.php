<div {{-- class="col-lg-4 mb-3" --}}>
  <div class="card">
    <div class="card-header">
      Basic details
    </div>
    <table class="table {{-- table-bordered --}}">
      <tbody>
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
        @if($user->getProfile()->getJoinDate())
        <tr>
          <th scope="row">Member since:</th>
          <td>{{ $user->getProfile()->getJoinDate()->toDateString() }}</td>
        </tr>
        @endif
      </tbody>
    </table>
    <div class="card-footer">
      <a href="#" class="btn btn-primary">Update details</a>
      <a href="#" class="btn btn-primary">Change password</a>
    </div>
  </div>
</div>
