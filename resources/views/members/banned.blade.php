@extends('layouts.app')

@section('pageTitle', 'Banned Members')

@section('content')

<div class="container">

  <h4>Permanently Banned Members</h4>

  @forelse ($bannedUsers as $user)
  @if ($loop->first)
  <p>
    These members have been permanently banned and will not be allowed
    back in the space.
  </p>

  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <th>Member Name</th>
        <th>Ban Date</th>
        <th>Reason</th>
      </thead>
      <tbody>
  @endif
        <tr>
          <td>
            {{$user->getFullname()}}
          </td>
          <td data-title="Date" class="text-monospace">
            {{$updates[$user->getId()]->getCreatedAt()}}
          </td>
          <td>
            {{$updates[$user->getId()]->getReason()}}
          </td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>
  @endif
  @empty
  <p>There are no permanently banned members.</p>
  @endforelse

  <h4>Temporarily Banned Members</h4>

  @forelse ($temporaryBannedUsers as $user)
  @if ($loop->first)
  <p>
    These members have been banned temporarily and will be welcomed
    back to the space once their ban has ended.
  </p>

  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <th>Member Name</th>
        <th>Ban Date</th>
        <th>Reason</th>
      </thead>
      <tbody>
  @endif
        <tr>
          <td>
            {{$user->getFullname()}}
          </td>
          <td data-title="Date" class="text-monospace">
            {{$updates[$user->getId()]->getCreatedAt()}}
          </td>
          <td>
            {{$updates[$user->getId()]->getReason()}}
          </td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>
  @endif
  @empty
  <p>There are no temporarily banned members.</p>
  @endforelse

</div>
@endsection
