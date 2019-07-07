@extends('layouts.app')

@section('pageTitle', $tool->getName().' Settings')

@section('content')
<div class="container">
  <h1>{{ $tool->getName() }}</h1>
  <hr>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <tbody>
        <tr>
          <th>Current Status</th>
          <td>{{ $tool->getStatus() }}</td>
        </tr>
        <tr>
          <th>Induction required</th>
          <td>{{ $tool->isRestricted() ? "Yes" : "No" }}</td>
        </tr>
        <tr>
          <th>Cost per hour</th>
          <td>@format_pennies($tool->getPph())</td>
        </tr>
        <tr>
          <th>Minimum booking length</th>
          <td>{{ $tool->getBookingLength() }} Minutes</td>
        </tr>
        <tr>
          <th>Maximum booking length</th>
          <td>{{ $tool->getLengthMax() }} Minutes</td>
        </tr>
        <tr>
          <th>Maximum number of simultaneous bookings per user</th>
          <td>{{ $tool->getBookingsMax() }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  @can('tools.edit')
  <a href="{{ route('tools.edit', $tool->getId()) }}" class="btn btn-info btn-block"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
  <br>
  @endcan

  @can('tools.destroy')
  <button class="btn btn-danger btn-block" data-toggle="confirmation" data-placement="bottom">
    <form action="{{ route('tools.destroy', $tool->getId()) }}" method="POST" style="display: inline">
      @method('DELETE')
      @csrf
    </form>
    <i class="fas fa-trash fa-lg" aria-hidden="true"></i> Remove
  </button>
  <br>
  @endcan

</div>
@endsection
