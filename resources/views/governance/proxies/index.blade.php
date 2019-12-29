@extends('layouts.app')

@section('pageTitle')
Proxies for {{ $meeting->getTitle() }}
@endsection

@section('content')
<div class="container">
  <p>
    A person designated is called a "proxy" and the person designating him or her is called a "principal".x
  </p>
</div>
@forelse ($meeting->getProxies() as $proxy)
@if ($loop->first)
<div class="container">
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <th>Proxy</th>
        <th>Principle</th>
        <th>Designated at</th>
        <th>Actions</th>
      </thead>
      <tbody>
@endif
        <tr>
          <td>{{ $proxy->getProxy()->getFullname() }}</td>
          <td>{{ $proxy->getPrincipal()->getFullname() }}</td>
          <td>{{ $proxy->getCreatedAt()->toDateTimeString() }}</td>
          <td></td>
        </tr>
@if ($loop->last)
      </tbody>
    </table>
  </div>
</div>
@endif
@empty
<div class="container">
  No Proxies yet designated.
</div>
@endforelse
@endsection
