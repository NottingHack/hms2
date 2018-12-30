@extends('layouts.app')

@section('pageTitle')
Rfid cards for {{ $user->getFirstname() }}
@endsection

@section('content')
@can('pins.view.all')
@foreach ($pins as $pin)
  @if ($loop->first)
  <div class="container">
    <ul class="list-group list-group-flush">
  @endif
    <li class="list-group-item">
      <div class="row">
        <div class="col-sm">
          <div>Rfid card enrolment pin: <b>{{ $pin->getPin() }}</b> is currently set to <i>{{ $pin->getStateString() }}</i></div>
        </div>
        @if ($pin->getState() == \HMS\Entities\GateKeeper\PinState::CANCELLED)
        @can('pins.reactivate')
        <div class="col-sm">
          <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary btn-sm btn-sm-spacing ">
            <form action="{{ route('pins.reactivate', $pin->getId()) }}" method="POST" style="display: none">
              {{ method_field('PATCH') }}
              {{ csrf_field() }}
            </form>
            <i class="fa fa-refresh fa-lg" aria-hidden="true"></i> Reactivate pin for enrolment
          </a>
        </div>
        @endcan
        @endif
      </div>
    </li>
  @if ($loop->last)
    </ul>
  </div>
  <br>
  @endif
@endforeach
@endcan

<div class="container">
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Card Serial Number</th>
          <th>Last Used</th>
          <th>Name</th>
          <th>State</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($rfidTags as $rfidTag)
        <tr>
          <td>{{ $rfidTag->getBestRfidSerial() }}</td>
          <td>{{ $rfidTag->getLastUsed() ? $rfidTag->getLastUsed()->toDateTimeString() : '' }}</td>
          <td>{{ $rfidTag->getFriendlyName() }}</td>
          <td>{{ $rfidTag->getStateString() }}</td>
          <td>
            @can('rfidTags.edit.self')
            <a href="{{ route('rfid-tags.edit', $rfidTag->getId()) }}" class="btn btn-primary btn-sm btn-sm-spacing"><i class="fa fa-edit fa-lg" aria-hidden="true"></i> Edit</a><br>
            @endcan
            @can('rfidTags.destroy')
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-danger btn-sm btn-sm-spacing">
              <form action="{{ route('rfid-tags.destroy', $rfidTag->getId()) }}" method="POST" style="display: none">
                {{ method_field('DELETE') }}
                {{ csrf_field() }}
              </form>
              <i class="fa fa-trash fa-lg" aria-hidden="true"></i> Remove
            </a>
            @endcan
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div classs="pagination-links">
      {{ $rfidTags->links() }}
  </div>
</div>
@endsection
