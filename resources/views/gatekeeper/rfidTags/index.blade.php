@extends('layouts.app')

@section('pageTitle')
RFID cards for {{ $user->getFirstname() }}
@endsection

@section('content')
@if (Route::currentRouteName() == 'users.rfid-tags')
<div class="container">
  <h3>
    RFID cards for {{ $user->getFullname() }} <a class="btn-sm btn-primary mb-2" href="{{ route('users.admin.show', $user->getId()) }}"><i class="fa fa-eye"></i></a>
  </h3>
  <hr>
</div>
@endif

@can('pins.view.all')
@foreach ($pins as $pin)
  @if ($loop->first)
  <div class="container">
    <ul class="list-group list-group-flush">
  @endif
    <li class="list-group-item">
      <div class="row">
        <div class="col-sm">
          <div>RFID card enrolment PIN: <b>{{ $pin->getPin() }}</b> is currently set to <i>{{ $pin->getStateString() }}</i></div>
        </div>
        @if ($pin->getState() == \HMS\Entities\Gatekeeper\PinState::CANCELLED)
        @can('pins.reactivate')
        <div class="col-sm">
          <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary btn-sm">
            <form action="{{ route('pins.reactivate', $pin->getId()) }}" method="POST" style="display: none">
              @method('PATCH')
              @csrf
            </form>
            <i class="fas fa-sync-alt fa-lg" aria-hidden="true"></i> Reactivate PIN for enrolment
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
  <div class="table-responsive no-more-tables">
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
          <td data-title="Card Serial Number">{{ $rfidTag->getBestRfidSerial() }}</td>
          <td data-title="Last Used">{{ $rfidTag->getLastUsed() ? $rfidTag->getLastUsed()->toDateTimeString() : '' }}&nbsp;</td>
          <td data-title="Name">{{ $rfidTag->getFriendlyName() }}&nbsp;</td>
          <td data-title="State">{{ $rfidTag->getStateString() }}</td>
          <td class="actions">
            @can('rfidTags.edit.self')
            <a href="{{ route('rfid-tags.edit', $rfidTag->getId()) }}" class="btn btn-primary btn-sm mb-1"><i class="fas fa-pencil fa-lg" aria-hidden="true"></i> Edit</a><br>
            @endcan
            @can('rfidTags.destroy')
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-danger btn-sm mb-1">
              <form action="{{ route('rfid-tags.destroy', $rfidTag->getId()) }}" method="POST" style="display: none">
                @method('DELETE')
                @csrf
              </form>
              <i class="fas fa-trash fa-lg" aria-hidden="true"></i> Remove
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
