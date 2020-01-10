@extends('layouts.app')

@section('pageTitle')
Box Audit
@endsection

@section('content')
<div class="container">
  @foreach ($boxes as $box)
  @if ($loop->first)
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>User</th>
          <th>Box Id</th>
          <th>Bought Date</th>
          <th>Removed Date</th>
          <th>State</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
  @endif
        <tr>
          <td>
            <span class="align-middle">
              {{ $box->getUser()->getFullname() }}
              <div class="btn-group float-right" role="group" aria-label="View User">
                <a href="{{ route('users.admin.show', $box->getUser()->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                </div>
            </span>
          </td>
          <td data-title="Box Id">{{ $box->getId() }}</td>
          <td data-title="Bought Date">{{ $box->getBoughtDate()->toDateString() }}</td>
          <td data-title="Removed Date">{{ $box->getRemovedDate() ? $box->getRemovedDate()->toDateString() : '' }}&nbsp;</td>
          <td data-title="State">{{ $box->getStateString() }}</td>
          <td data-title="Actions" class="actions">
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-primary btn-sm mb-1">
              <form action="{{ route('boxes.markAbandoned', $box->getId()) }}" method="POST" style="display: none">
                @method('PATCH')
                @csrf
              </form>
              <i class="far fa-frown" aria-hidden="true"></i> Mark Abandoned
            </a><br>
          </td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>
  <div classs="pagination-links">
    {{ $boxes->links() }}
  </div>
  @endif
  @endforeach
</div>
@endsection
