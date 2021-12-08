@extends('layouts.app')

@section('pageTitle')
Proxies you have accepted for {{ $meeting->getTitle() }}
@endsection

@section('content')
<div class="container">
  <p>
    Below is a list of Proxy designations you have accepted for the meeting on {{ $meeting->getStartTime()->toDayDateTimeString() }}
  </p>
</div>
@forelse ($proxies as $proxy)
@if ($loop->first)
<div class="container">
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <th>Principle</th>
        <th>Designated at</th>
        <th>Actions</th>
      </thead>
      <tbody>
@endif
        <tr>
          <td>{{ $proxy->getPrincipal()->getFullname() }}</td>
          <td>{{ $proxy->getCreatedAt()->toDateTimeString() }}</td>
          <td>
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-danger">
              <form action="{{ route('governance.proxies.destroy', ['meeting' => $meeting->getId()]) }}" method="POST" style="display: none">
                @method('DELETE')
                @csrf
                <input type="hidden" name="principal_id" value="{{ $proxy->getPrincipal()->getId() }}">
              </form>
              <i class="fas fa-trash" aria-hidden="true"></i> Cancel Proxy
            </a>
          </td>
        </tr>
@if ($loop->last)
      </tbody>
    </table>
  </div>
</div>
@endif
@empty
<div class="container">
  No Proxies yet accepted.
</div>
@endforelse
@endsection
