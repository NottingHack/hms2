<div {{-- class="col-lg-4 mb-3" --}}>
  <div class="card">
    <div class="card-header">
      Profile details
    </div>
    <div class="card-body">
      <dl class="row ">
        <dt class="col-sm-4">Address:</dt>
        <dd class="col-sm-8">
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
        </dd>
        <dt class="col-sm-4">Contact Number:</dt>
        <dd class="col-sm-8">{{ $user->getProfile()->getContactNumber() }}</dd>
        @if($user->getProfile()->getDateOfBirth())
        <dt class="col-sm-4">Date of Birth:</dt>
        <dd class="col-sm-8">{{ $user->getProfile()->getDateOfBirth()->toDateString() }}</dd>
        @endif
        <dt class="col-sm-4">Unlock text:</dt>
        <dd class="col-sm-8">{{ $user->getProfile()->getUnlockText() }}</dd>
        @if($user->getAccount())
        <dt class="col-sm-4">Bank refrence:</dt>
        <dd class="col-sm-8">{{ $user->getAccount()->getPaymentRef() }}</dd>
        @endif
      </dl>
    </div>
    <div class="card-footer">
      <a href="#" class="btn btn-primary">Update contact details</a>
    </div>
  </div>
</div>
