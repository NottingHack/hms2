@extends('layouts.app')

@section('pageTitle', 'Edit Bookable Area')

@section('content')
<div class="container">
  <form role="form" method="POST" action="{{ route('gatekeeper.bookable-area.update', $bookableArea->getId()) }}">
    @csrf
    @method('PATCH')

    <div class="form-group">
      <label for="building_id" class="form-label">Building</label>
      <input class="form-control" id="building_id" type="text" name="building_id" value="{{ $bookableArea->getBuilding()->getName() }}" aria-describedby="buildingHelpBlock" disabled>
      <small id="buildingHelpBlock" class="form-text text-muted">
        The Building can not be changed.
      </small>
    </div>

    <div class="form-group">
      <label for="name" class="form-label">Name</label>
      <input class="form-control @error('name') is-invalid @enderror" id="name" type="text" name="name" value="{{ old('name', $bookableArea->getName()) }}" maxlength="50" aria-describedby="nameHelpBlock" required >
      <small id="nameHelpBlock" class="form-text text-muted">
        Short name for area
      </small>
      <div class="invalid-feedback">
        {{ $errors->first('name') }}
      </div>
    </div>

    <div class="form-group">
      <label for="description" class="form-label">Description</label>
      <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $bookableArea->getDescription()) }}</textarea>
      <div class="invalid-feedback">
        {{ $errors->first('description') }}
      </div>
    </div>

    <div class="form-group">
      <label for="maxOccupancy" class="form-label">Max occupancy</label>
      <input class="form-control @error('maxOccupancy') is-invalid @enderror" id="maxOccupancy" type="number" min="1" name="maxOccupancy" value="{{ old('maxOccupancy', $bookableArea->getMaxOccupancy()) }}" aria-describedby="maxOccupancyHelpBlock" required>
      <small id="maxOccupancyHelpBlock" class="form-text text-muted">
        Max number of people allowed in this area at one time. Minimum 1
      </small>
      <div class="invalid-feedback">
        {{ $errors->first('maxOccupancy') }}
      </div>
    </div>

    <div class="form-group">
      <label for="additionalGuestOccupancy" class="form-label">Additional guest occupancy</label>
      <input class="form-control @error('additionalGuestOccupancy') is-invalid @enderror" id="additionalGuestOccupancy" type="number" min="0" name="additionalGuestOccupancy" value="{{ old('additionalGuestOccupancy', $bookableArea->getAdditionalGuestOccupancy()) }}" aria-describedby="additionalGuestOccupancyHelpBlock" required>
      <small id="additionalGuestOccupancyHelpBlock" class="form-text text-muted">
        Additional Guests allowed past past max occupancy (only used when max occupancy is 1). Minimum 0
      </small>
      <div class="invalid-feedback">
        {{ $errors->first('additionalGuestOccupancy') }}
      </div>
    </div>

    <label for="bookingColor" class="form-label">Booking colour</label>
    <fieldset class="form-group">
      @foreach(HMS\Entities\Gatekeeper\BookableAreaBookingColor::COLOR_STRINGS as $color => $string)
      <div class="form-check form-check-inline">
      <input
        class="form-check-input"
        type="radio"
        name="bookingColor"
        value="{{ $color }}"
        {{ old('bookingColor', $bookableArea->getBookingColor()) == $color ? 'checked="checked"' : '' }}
        >
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
      <input id="selfBookable" class="form-check-input @error('selfBookable') is-invalid @enderror" type="checkbox" name="selfBookable" {{ old('selfBookable', $bookableArea->isSelfBookable()) ? 'checked="checked"' : '' }}>
      <label class="form-check-label" for="selfBookable">
        Can this be Self Booked?
      </label>
      <div class="invalid-feedback">
        {{ $errors->first('selfBookable') }}
      </div>
    </div>

    <button type="submit" class="btn btn-success btn-block">Update</button>
  </form>
</div>
@endsection
