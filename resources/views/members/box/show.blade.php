@extends('layouts.app')

@section('pageTitle')
Box {{ $box->getId() }}
@endsection

@section('content')
<div class="container">
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Box Id</th>
          @if ($box->getUser() != \Auth::user())
          <th>Owner</t>
          @endif
          <th>Bought Date</th>
          <th>Removed Date</th>
          <th>State</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td data-title="Box Id">{{ $box->getId() }}</td>
          @if ($box->getUser() != \Auth::user())
          <th data-title="Owner">
            {{ $box->getUser()->getFullname() }} <a class="float-right btn-sm btn-primary mb-1" href="{{ route('users.admin.show', $box->getUser()->getId()) }}"><i class="fa fa-eye"></i></a>
          </t>
          @endif
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
      </tbody>
    </table>
  </div>
</div>
@endsection
