@extends('layouts.app')

@section('pageTitle', 'Tool Statistics')

@section('content')
<div class="container">
  <p>Basic usage statistics for tools.</p>
  @foreach($tools as $name => $stats)
  @if ($loop->first)
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th rowspan="2">Name</th>
          <th rowspan="2">Inducted Users</th>
          <th rowspan="2">Inductors</th>
          <th rowspan="2">Maintainers</th>
          <th colspan="2">This Month</th>
          <th colspan="2">Last Month</th>
        </tr>
        <tr>
          <th>Booked</th>
          <th>Used</th>
          <th>Booked</th>
          <th>Used</th>
        </tr>
      </thead>
      <tbody>
  @endif
        <tr>
          <td data-title="Name">{{ $name }}</td>
          <td data-title="Inducted Users">{{ $stats['userCount'] }}</td>
          <td data-title="Inductors">{{ $stats['inductorCount'] }}</td>
          <td data-title="Maintainers">{{ $stats['maintainerCount'] }}</td>
          <td data-title="This Month: Booked">{{ $stats['bookedThisMonth'] }}</td>
          <td data-title="This Month: Used">{{ $stats['usedThisMonth'] }}</td>
          <td data-title="Last Month: Booked">{{ $stats['bookedLastMonth'] }}</td>
          <td data-title="Last Month: Used">{{ $stats['usedLastMonth'] }}</td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>
  @endif
  @endforeach
</div>
@endsection
