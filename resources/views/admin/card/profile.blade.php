@can('profile.view.all')
@if($user->getProfile())
<div class="card">
  <div class="card-header">Address details</div>
  <table class="table">
    <tbody>
      <tr>
        <th scope="row">Address:</th>
        <td>
          {{ $user->getProfile()->getAddress1() }}<br>
          @if($user->getProfile()->getAddress2())
          {{ $user->getProfile()->getAddress2() }}<br>
          @endif
          @if($user->getProfile()->getAddress3())
          {{ $user->getProfile()->getAddress3() }}<br>
          @endif
          {{ $user->getProfile()->getAddressCity() }}<br>
          @if($user->getProfile()->getAddressCounty())
          {{ $user->getProfile()->getAddressCounty() }}<br>
          @endif
          {{ $user->getProfile()->getAddressPostCode() }}
        </td>
      </tr>
      <tr>
        <th scope="row">Contact Number:</th>
        <td>{{ $user->getProfile()->getContactNumber() }}</td>
      </tr>
      @if($user->getProfile()->getDateOfBirth())
      <tr>
        <th scope="row">Date of Birth:</th>
        <td>{{ $user->getProfile()->getDateOfBirth()->toDateString() }}</td>
      </tr>
      @endif
    </tbody>
  </table>
</div>
@endif
@endcan
