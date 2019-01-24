@extends('layouts.app')

@section('pageTitle', 'Events for ' . $service->getName())

@section('content')
<div class="container">
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Time</th>
          <th>Type</th>
        </tr>
      </thead>
      <tbody>
        @foreach($events as $event)
        <tr>
          <td class="text-monospace">{{ $event->getTime()->toDateTimeString() }}</td>
          <td>{{ $event->getType() }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="pagination-links">
    {{ $events->links() }}
  </div>
</div>
@endsection
