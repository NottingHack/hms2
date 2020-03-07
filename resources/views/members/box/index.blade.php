@extends('layouts.app')

@section('pageTitle')
Boxes for {{ $user->getFirstname() }}
@endsection

@section('content')
<div class="container">
  <p>All members are entitled to a member's box in the storage room, but we have limited space.</p>

  <p>If there is an empty box available (please check in real life first!) you can purchase it using the "Buy new box" button below. This will debit your Snackspace account by @money($boxCost, 'GBP') and assign a box to you. The system will also check that there is space available for your box.</p>

  <p><strong>Note:</strong> The system does not know if there is an actual empty box available for you, and will debit you either way - please check first!</p>

  <p>Once you have bought a box, please print a label (using the link below) and place it on the front of your box so it can be identified.</p>
</div>
@if ($user == \Auth::user())
@can('box.buy.self')
<div class="container">
  <a href="{{ route('boxes.create') }}" class="btn btn-primary btn-block"><i class="fas fa-shopping-cart" aria-hidden="true"></i> Buy new box</a>
</div>
@endcan
@else
@can('box.issue.all')
<div class="container">
  <a href="{{ route('users.boxes.issue', $user->getId()) }}" class="btn btn-primary btn-block"><i class="fas fa-plus" aria-hidden="true"></i> Issue new box</a>
</div>
@endcan
@endif

<br>
<div class="container">
  @if (count($boxes) > 0)
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Box Id</th>
          <th>Bought Date</th>
          <th>Removed Date</th>
          <th>State</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($boxes as $box)
        <tr>
          <td data-title="Box Id">{{ $box->getId() }}</td>
          <td data-title="Bought Date">{{ $box->getBoughtDate()->toDateString() }}</td>
          <td data-title="Removed Date">{{ $box->getRemovedDate() ? $box->getRemovedDate()->toDateString() : '' }}&nbsp;</td>
          <td data-title="State">{{ $box->getStateString() }}</td>
          <td data-title="Actions" class="actions">
            @can('box.printLabel.self')
            @if (SiteVisitor::inTheSpace() && $box->getState() == \HMS\Entities\Members\BoxState::INUSE)
            <a href="{{ route('boxes.print', $box->getId()) }}" class="btn btn-primary btn-sm mb-1"><i class="fas fa-print" aria-hidden="true"></i> Print Box Label</a><br>
            @endif
            @endcan
            @can('box.edit.self')
            @if ($box->getState() == \HMS\Entities\Members\BoxState::INUSE)
            @if ($box->getUser() == \Auth::user())
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary btn-sm">
              <form action="{{ route('boxes.markRemoved', $box->getId()) }}" method="POST" style="display: none">
                @method('PATCH')
                @csrf
              </form>
              <i class="fas fa-minus-circle" aria-hidden="true"></i> Mark Removed
            </a>
            @else
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary btn-sm mb-1">
              <form action="{{ route('boxes.markAbandoned', $box->getId()) }}" method="POST" style="display: none">
                @method('PATCH')
                @csrf
              </form>
              <i class="far fa-frown" aria-hidden="true"></i> Mark Abandoned
            </a><br>
            @endif
            @endif
            @if ($box->getState() != \HMS\Entities\Members\BoxState::INUSE)
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary btn-sm mb-1">
              <form action="{{ route('boxes.markInUse', $box->getId()) }}" method="POST" style="display: none">
                @method('PATCH')
                @csrf
              </form>
              <i class="fal fa-play" aria-hidden="true"></i> Mark In Use
            </a>
            @endif
            @endcan
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div classs="pagination-links">
    {{ $boxes->links() }}
  </div>
  @endif
</div>
@endsection
