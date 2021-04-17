@extends('layouts.app')

@section('pageTitle')
Voting Status
@endsection

@section('content')
<div class="container">
  @content('governance.voting.index', 'main')
  <h2>Current Status</h2>
  <p>
    Your current calculated voting status is <strong>{{ $votingStatus }}</strong><br>
    This is made up of the result (Voting / Non-Voting) and a reason (Stated, Physical, ...) for how HSM arrived at that outcome. The definitions for the reasons are explained at the bottom of this page and the wiki includes a flow chart of the HMS process.
  </p>
  <hr>
  <h2>Stated Voting Preference</h2>
  <p>
    Here you can see your current state voting preference and update it if you like.<br>
    A stated preference is one of the following.
    <dl>
      <dt>Voting:</dt>
      <dd>You wish to be counted as <strong>Voting Member</strong> of the {{ config('branding.space_type') }}</dd>
      <dt>Non-voting:</dt>
      <dd>You wish to be counted as <strong>Non-voting Member</strong> of the {{ config('branding.space_type') }}</dd>
      <dt>Automatic:</dt>
      <dd>Let the process decide automatically</dd>
    </dl>
  </p>
  <table class="table table-borderless">
    <tbody>
      <tr>
        <th scope="row" class="w-25">Current stated preference:</th>
        <td>{{ $votingPreferenceString }}</td>
      </tr>
      <tr>
        <th scope="row">Date preference was stated:</th>
        <td>
          @if ($votingPreferenceStatedAt)
           {{ $votingPreferenceStatedAt->toDateTimeString() }}{{ $votingPreferenceStatedAt->isAfter(Carbon\Carbon::now()->subMonthsNoOverflow(6)) ? '' : ', Over six months ago' }}
          @else
          Never stated
          @endif
        </td>
      </tr>
    </tbody>
  </table>
  <h3>State a voting preference</h3>
  <form action="{{ route('governance.voting.update') }}" method="POST">
    @csrf
    @method('PATCH')
    <div class="btn-group btn-block btn-group-lg" role="group" aria-label="Voting Preference">
      <button type="submit" class="btn btn-primary @if ($votingPreference == "VOTING") active @endif" name='preference' value="VOTING">Voting</button>
      <button type="submit" class="btn btn-primary @if ($votingPreference == "NONVOTING") active @endif" name='preference' value="NONVOTING">Non-voting</button>
      <button type="submit" class="btn btn-primary @if ($votingPreference == "AUTOMATIC") active @endif" name='preference' value="AUTOMATIC">Automatic</button>
    </div>
  </form>
  <p>Re-stating the same preference will update the date to now.</p>
  <br>
  <hr>
  <h5>Outcome reasons</h5>
  <dl>
    <dt>Stated</dt>
    <dd>You have stated a preference in the last six months</dd>
    <dt>Absentee</dt>
    <dd>You communicated you absence for an AGM or EGM in the last six months</dd>
    <dt>Attended</dt>
    <dd>You attended an AGM or EGM in the last six months</dd>
    <dt>Proxy</dt>
    <dd>You registered a proxy for an AGM or EGM in the last six months</dd>
    <dt>Physical</dt>
    <dd>You have access the space in the last six months</dd>
    <dt>Automatic</dt>
    <dd>Having done none of the above your status was automatically calculated</dd>
  </dl>
</div>
@endsection
