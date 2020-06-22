@extends('layouts.app')

@section('pageTitle', 'Bookable Area ' . $bookableArea->getName())

@section('content')
<div class="container">
  <p>Additional guest occupancy is only relevant when Max occupancy is 1</p>
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <tbody>
        <tr>
          <th class="w-15">Name</th>
          <td data-title="Name">{{ $bookableArea->getName() }}</td>
        </tr>
        <tr>
          <th>Description</th>
          <td data-title="Description"><p>{!! $bookableArea->getDescription() !!}</p></td>
        </tr>
        <tr>
          <th>Max occupancy</th>
          <td data-title="Max occupancy">{{ $bookableArea->getMaxOccupancy() }}</td>
        </tr>
        <tr>
          <th>Additional guest occupancy</th>
          <td data-title="Additional guest occupancy">{{ $bookableArea->getAdditionalGuestOccupancy() }}</td>
        </tr>
        <tr>
          <th>Booking colour</th>
          <td data-title="Booking colour">
            <p class="h4"><span class="badge badge-{{ $bookableArea->getBookingColor() }} pb-2">{{ $bookableArea->getBookingColorString() }}</span></p>
          </td>
        </tr>
        <tr>
          <th>Self bookable</th>
          <td data-title="Self bookable">{{ $bookableArea->isSelfBookable() ? 'Yes' : 'No'}}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <a class="btn btn-primary btn-block" href="{{ route('gatekeeper.bookable-area.edit', $bookableArea->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
  <button type="button" class="btn btn-danger btn-block" data-toggle="confirmation" data-placement="bottom">
    <form action="{{ route('gatekeeper.bookable-area.destroy', $bookableArea->getId()) }}" method="POST" style="display: inline">
      @method('DELETE')
      @csrf
    </form>
    <i class="fas fa-trash" aria-hidden="true"></i>&nbsp;Remove
  </button>
</div>
@endsection
