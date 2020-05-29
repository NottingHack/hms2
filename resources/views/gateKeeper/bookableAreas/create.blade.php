@extends('layouts.app')

@section('pageTitle', 'Create Bookable Area')

@section('content')
<div class="container">
  <form role="form" method="POST" action="{{ route('gatekeeper.bookable-area.store') }}">
    @csrf

    <div class="form-group">
      <label for="building_id" class="form-label">Building</label>
      <select-two
        :invalid="{{ $errors->has('building_id') ? 'true' : 'false' }}"
        id='building_id'
        name='building_id'
        placeholder="Select a building..."
        :options='@json($buildingOptions)'
        style="width: 100%"
        {{ old('building_id') ? ':value="' . old('building_id') . '"' : '' }}
        >
      </select-two>
    </div>

    <div class="form-group">
      <label for="name" class="form-label">Name</label>
      <input class="form-control @error('name') is-invalid @enderror" id="name" type="text" name="name" value="{{ old('name') }}" maxlength="50" aria-describedby="nameHelpBlock" required >
      <small id="nameHelpBlock" class="form-text text-muted">
        Short name for area
      </small>
      <div class="invalid-feedback">
        {{ $errors->first('name') }}
      </div>
    </div>

    <div class="form-group">
      <label for="description" class="form-label">Description</label>
      <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
      <div class="invalid-feedback">
        {{ $errors->first('description') }}
      </div>
    </div>

    <div class="form-group">
      <label for="maxOccupancy" class="form-label">Max occupancy</label>
      <input class="form-control @error('maxOccupancy') is-invalid @enderror" id="maxOccupancy" type="number" min="1" name="maxOccupancy" value="{{ old('maxOccupancy', 1) }}" aria-describedby="maxOccupancyHelpBlock" required>
      <small id="maxOccupancyHelpBlock" class="form-text text-muted">
        Max number of people allowed in this area at one time. Minimum 1
      </small>
      <div class="invalid-feedback">
        {{ $errors->first('maxOccupancy') }}
      </div>
    </div>

    <div class="form-group">
      <label for="additionalGuestOccupancy" class="form-label">Additional guest occupancy</label>
      <input class="form-control @error('additionalGuestOccupancy') is-invalid @enderror" id="additionalGuestOccupancy" type="number" min="0" name="additionalGuestOccupancy" value="{{ old('additionalGuestOccupancy', 0) }}" aria-describedby="additionalGuestOccupancyHelpBlock" required>
      <small id="additionalGuestOccupancyHelpBlock" class="form-text text-muted">
        Additional Guests allowed past past max occupancy (only used when max occupancy is 1). Minimum 0
      </small>
      <div class="invalid-feedback">
        {{ $errors->first('additionalGuestOccupancy') }}
      </div>
    </div>

    <label for="bookingColor" class="form-label">Booking colour</label>
    <fieldset class="form-group">
      @foreach(HMS\Entities\GateKeeper\BookableAreaBookingColor::COLOR_STRINGS as $color => $string)
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="bookingColor" value="{{ $color }}">
        <label class="form-check-label h4"><span class="badge badge-{{ $color }} pb-2">{{ $string }}</span></label>
      </div>
      @endforeach
      <small id="bookingColorHelpBlock" class="form-text text-muted">
        Color used to show this area in the Access Calendar
      </small>
      <div class="invalid-feedback">
        {{ $errors->first('bookingColor') }}
      </div>
    </fieldset>

    <div class="form-group form-check">
      <input id="selfBookable" class="form-check-input @error('selfBookable') is-invalid @enderror" type="checkbox" name="selfBookable" {{ old('selfBookable', 0) ? 'checked="checked"' : '' }}>
      <label class="form-check-label" for="selfBookable">
        Can this be Self Booked?
      </label>
      <div class="invalid-feedback">
        {{ $errors->first('selfBookable') }}
      </div>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Add Bookable Area</button>
  </form>
</div>
@endsection
