@extends('layouts.app')

@section('pageTitle', 'Bookable Areas')

@section('content')
<div class="container">
  <p>Bookalbe areas are ....</p>
  <a href="{{ route('gatekeeper.bookable-area.create') }}" class="btn btn-primary btn-block"><i class="fas fa-plus" aria-hidden="true"></i> Add new bookable area</a>
  <br>
  @foreach($bookableAreas as $buildingName => $bookableAreas)
  <h2>{{ $buildingName }} <small class="text-muted">({{ $buildingAccessStates[$buildingName] }})</small></h2>
  <hr>
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Name</th>
          <th class="d-none d-lg-table-cell">Description</th>
          <th>Max occupancy</th>
          <th>Additional guest occupancy</th>
          <th>Booking colour</th>
          <th>Self bookable</th>
          <th class="w-15">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($bookableAreas as $bookableArea)
        <tr>
          <td data-title="Name">{{ $bookableArea->getName() }}</th>
          <td data-title="Description" class="d-none d-lg-table-cell">{{ $bookableArea->getDescriptionTrimed(50) }}</th>
          <td data-title="Max occupancy">{{ $bookableArea->getMaxOccupancy() }}</th>
          <td data-title="Additional guest occupancy">{{ $bookableArea->getAdditionalGuestOccupancy() }}</th>
          <td data-title="Booking colour">
            <p class="h4"><span class="badge badge-{{ $bookableArea->getBookingColor() }} pb-2">{{ $bookableArea->getBookingColorString() }}</span></p>
          </th>
          <td data-title="Self bookable">{{ $bookableArea->isSelfBookable() ? 'Yes' : 'No'}}</th>
          <td data-title="Actions" class="actions">
            <a href="{{ route('gatekeeper.bookable-area.show', $bookableArea->getId()) }}" class="btn btn-primary btn-sm mb-1"><i class="far fa-eye" aria-hidden="true"></i><span class="d-md-none d-lg-inline"> View</a>
            <a class="btn btn-primary btn-sm mb-1" href="{{ route('gatekeeper.bookable-area.edit', $bookableArea->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i><span class="d-md-none d-lg-inline"> Edit</a>
            <button type="button" class="btn btn-danger btn-sm mb-1" data-toggle="confirmation" data-placement="bottom">
              <form action="{{ route('gatekeeper.bookable-area.destroy', $bookableArea->getId()) }}" method="POST" style="display: inline">
                @method('DELETE')
                @csrf
              </form>
              <i class="fas fa-trash" aria-hidden="true"></i><span class="d-md-none d-lg-inline"> Remove
            </button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endforeach
</div>

@endsection
