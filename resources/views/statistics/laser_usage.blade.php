@extends('layouts.app')

@section('pageTitle', 'Laser cutter usage')

@section('content')
<div class="container">
  <p>Summary of laser cutter usage time by month.</p>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <th title="Year">Year</th>
        <th title="Month">Month</th>
        <th title="Time (hh:mm:ss)">Time (hh:mm:ss)</th>
        <th title="A0 (hh:mm:ss)">A0 (hh:mm:ss)</th>
        <th title="A2 (hh:mm:ss)">A2 (hh:mm:ss)</th>
        <th title="Charged Time (hh:mm:ss)">Charged Time (hh:mm:ss)</th>
        <th title="Charged Income (£)">Charged Income (£)</th>
        <th title="Distinct users">Distinct users</th>
        <th title="Members inducted">Members inducted</th>
      </thead>
      <tbody>
        @foreach ($laserUsage as $month)
        <tr>
          <td>{{ $month->year }}</td>
          <td>{{ $month->month }}</td>
          <td>{{ $month->total_time }}</td>
          <td>{{ $month->a0_time }}</td>
          <td>{{ $month->a2_time }}</td>
          <td>{{ $month->charged_time }}</td>
          <td>{{ $month->charged_income }}</td>
          <td>{{ $month->distinct_users }}</td>
          <td>{{ $month->members_inducted }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
