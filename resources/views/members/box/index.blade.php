@extends('layouts.app')

@section('pageTitle')
Boxes for {{ $user->getFirstname() }}
@endsection

@section('content')
<div class="container">
  <p>All members are entitled to a member's box in the storage room, but we have limited space.</p>

  <p>If there is an empty box available (please check in real life first!) you can purchase it using the "Buy new box" button below. This will debit your Snackspace account by £@format_pennies($boxCost) and assign a box to you. The system will also check that there is space available for your box.</p>

  <p><strong>Note:</strong> The system does not know if there is an actual empty box available for you, and will debit you either way - please check first!</p>

  <p>Once you have bought a box, please print a label (using the link below) and place it on the front of your box so it can be identified.</p>
</div>
@if ($user == \Auth::user())
@can('box.buy.self')
<div class="container">
  <div class="card">
    <a href="{{ route('boxes.create') }}" class="btn btn-primary"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Buy new box</a>
  </div>
</div>
@endcan
@else
@can('box.issue.all')
<div class="container">
  <div class="card">
    <a href="{{ route('user.boxes.issue', $user->getId()) }}" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Issue new box</a>
  </div>
</div>
@endcan
@endif

<br>
<div class="container">
  <div class="table-responsive">
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
          <td>{{ $box->getId() }}</td>
          <td>{{ $box->getBoughtDate()->toDateString() }}</td>
          <td>{{ $box->getRemovedDate() ? $box->getRemovedDate()->toDateString() : '' }}</td>
          <td>{{ $box->getStateString() }}</td>
          <td>
            @can('box.printLabel.self')
            @if (SiteVisitor::inTheSpace() && $box->getState() == \HMS\Entities\Members\BoxState::INUSE)
            <a href="{{ route('boxes.print', $box->getId()) }}" class="btn btn-primary btn-sm btn-sm-spacing"><i class="fa fa-print" aria-hidden="true"></i> Print Box Label</a><br>
            @endif
            @endcan
            @can('box.edit.self')
            @if ($box->getState() == \HMS\Entities\Members\BoxState::INUSE)
            @if ($box->getUser() == \Auth::user())
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary btn-sm">
              <form action="{{ route('boxes.markRemoved', $box->getId()) }}" method="POST" style="display: none">
                {{ method_field('PATCH') }}
                {{ csrf_field() }}
              </form>
              <i class="fa fa-minus-circle" aria-hidden="true"></i> Mark Removed
            </a>
            @else
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary btn-sm btn-sm-spacing">
              <form action="{{ route('boxes.markAbandoned', $box->getId()) }}" method="POST" style="display: none">
                {{ method_field('PATCH') }}
                {{ csrf_field() }}
              </form>
              <i class="fa fa-frown-o" aria-hidden="true"></i> Mark Abandoned
            </a><br>
            @endif
            @endif
            @if ($box->getState() != \HMS\Entities\Members\BoxState::INUSE)
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary btn-sm btn-sm-spacing">
              <form action="{{ route('boxes.markInUse', $box->getId()) }}" method="POST" style="display: none">
                {{ method_field('PATCH') }}
                {{ csrf_field() }}
              </form>
              <i class="fa fa-play" aria-hidden="true"></i> Mark In Use
            </a>
            @endif
            @endcan
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection