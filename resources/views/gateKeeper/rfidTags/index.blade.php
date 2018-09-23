@extends('layouts.app')

@section('pageTitle')
Rfid cards for {{ $user->getFirstname() }}
@endsection

@section('content')
@can('pins.view.all')
@foreach ($pins as $pin)
  @if ($loop->first)
  <div>
  <ul class="nomarkers">
  @endif
  <li>
    <div class="card">
      <div class="card-section">
          <div class="row">
              <div class="columns">
                  <div>Rfid card enrolment pin: <b>{{ $pin->getPin() }}</b> is currently set to <i>{{ $pin->getStateString() }}</i></div>
              </div>
              @if ($pin->getState() == \HMS\Entities\GateKeeper\Pin::STATE_CANCELLED)
              @can('pins.reactivate')
              <div class="columns">
                  <ul class="horizontal menu align-right">
                    <li>
                      <a href="javascript:void(0);" onclick="$(this).find('form').submit();">
                        <form action="{{ route('pins.reactivate', $pin->getId()) }}" method="POST" style="display: none">
                          {{ method_field('PATCH') }}
                          {{ csrf_field() }}
                        </form>
                        <i class="fa fa-refresh fa-lg" aria-hidden="true"></i> Reactivate pin for enrolment
                      </a>
                    </li>
                  </ul>
                  </div>
              @endcan
              @endif
          </div>
      </div>
    </div>
  </li>
  @if ($loop->last)
  </ul>
  </div>
  @endif
@endforeach
@endcan

<table>
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
              <ul class="horizontal menu">
                @can('rfidTags.edit.self')
                <li> <a href="{{ route('rfid-tags.edit', $rfidTag->getId()) }}"><i class="fa fa-edit fa-lg" aria-hidden="true"></i> Edit</a></li>
                @endcan
                @can('rfidTags.destroy')
                <li>
                  <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="alert">
                    <form action="{{ route('rfid-tags.destroy', $rfidTag->getId()) }}" method="POST" style="display: none">
                      {{ method_field('DELETE') }}
                      {{ csrf_field() }}
                    </form>
                    <i class="fa fa-trash fa-lg" aria-hidden="true"></i> Remove
                  </a>
                </li>
                @endcan
              </ul>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<div classs="pagination-links">
    {{ $rfidTags->links() }}
</div>
@endsection
