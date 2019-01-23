@extends('layouts.app')

@section('pageTitle', 'Instrumentaion Services')

@section('content')
<div class="container">
  <div class="table-responsive no-more-tables">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Service</th>
          <th>Status</th>
          <th>Last response</th>
          <th>Last restart</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($services as $service)
        <tr class="{{ $service->getStatus() != 1 ? 'bg-danger text-white' : '' }}">
          <td data-title="Service">{{ $service->getName() }}</td>
          <td data-title="Status">{{ $service->getStatusString() }}</td>
          <td data-title="Last response">{{ $service->getReplyTime()->toDateTimeString() }}</td>
          <td data-title="Last restart">{{ $service->getRestartTime() ? $service->getRestartTime()->toDateTimeString() : '(unknown)' }}</td>
          <td class="actions"><a class="btn btn-primary btn-sm btn-sm-spacing" href="{{ route('instrumentation.service.events', $service->getName() ) }}"><i class="far fa-eye" aria-hidden="true"></i> View Events</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
