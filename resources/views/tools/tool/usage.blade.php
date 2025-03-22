@extends('layouts.app')

@section('pageTitle', $tool->getDisplayName().' Settings')

@section('content')
<div class="container">
  <h1>Usage: {{ $tool->getName() }}</h1>
  @canany(['tools.' . $tool->getPermissionName() . '.maintain', 'tools.inductor.grant'])
  @forelse ($usage as $use)
  @if ($loop->first)
  <div class="table-responsive no-more-tables">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Member</th>
          <th>Start Time</th>
          <th>End Time</th>
        </tr>
      </thead>
      <tbody>
  @endif
        <tr>
          <td>{{ $use->getUser()->getFullname() }}</td>
          <td>{{ $use->getStart() }} </td>
          <td>{{ $use->getStart()->addSeconds($use->getDuration()) }}</td>
        </tr>
  @if( $loop->last)
      </tbody>
    </table>
  </div>
  @endif
  @empty
  <p>
    No usage for this period.
  </p>
  @endforelse
  @endcan

  <h3>Change usage period</h3>
  <form role="form" method="GET" action="{{ route('tools.usage', $tool->getId()) }}">
    <div class="form-group">
      <label for="startDate" class="form-label">Date</label>
      <input class="form-control @error('startDate') is-invalid @enderror" id="startDate" type="date" name="startDate" value="{{ old('startDate', $startDate->toDateString()) }}" required>
      @error('startDate')
      <p class="invalid-feedback">
        <strong>{{ $errors->first('startDate') }}</strong>
      </p>
      @enderror
    </div>
    <div class="form-group">
      <label for="endDate" class="form-label">Date</label>
      <input class="form-control @error('endDate') is-invalid @enderror" id="endDate" type="date" name="endDate" value="{{ old('endDate', $endDate->toDateString()) }}" required>
      @error('endDate')
      <p class="invalid-feedback">
        <strong>{{ $errors->first('endDate') }}</strong>
      </p>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-redo"></i> Update period</button>
  </form>
</div>
@endsection
