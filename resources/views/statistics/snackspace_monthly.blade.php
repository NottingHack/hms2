@extends('layouts.app')

@section('pageTitle', 'RFID purchases and payments')

@section('content')
<div class="container">
  <p>Summary of amount spent on Vending machine / Snackspace purchases, Laser usage and payments made.</p>

    <p>Note 1: Only purchases made using RFID are counted in "Items vended" and "Vend value".</p>
    <div class="table-responsive">
    <table class="table">
      <thead>
        <th>Year</th>
        <th>Month</th>
        <th>Items vended</th>
        <th>Snacks Vended</th>
        <th>Drinks Vended</th>
        <th>Vend value</th>
        <th>Avg item cost</th>
        <th>Laser charges</th>
        <th>Payments</th>
      </thead>
      <tbody>
        @foreach($snackspaceMonthly as $month)
        <tr>
          <td>{{ $month['Year'] }}</td>
          <td>{{ $month['Month'] }}</td>
          <td>{{ $month['Items vended'] }}</td>
          <td>{{ $month['Snacks Vended'] }}</td>
          <td>{{ $month['Drinks Vended'] }}</td>
          <td>{{ $month['Vend value'] }}</td>
          <td>{{ $month['Avg item cost'] }}</td>
          <td>{{ $month['Laser charges'] }}</td>
          <td>{{ $month['Payments'] }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
