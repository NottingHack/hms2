@extends('layouts.app')

@section('pageTitle')
Proxies for {{ $meeting->getTitle() }}
@endsection

@section('content')
<div class="container">
  <p>
    A person designated is called a "proxy" and the person designating him or her is called a "principal".
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
      </thead>
      <tbody>
@endif
        <tr>
          <td data-title="Proxy">
            <span class="align-middle">
              {{ $proxy->getProxy()->getFullname() }}
              <div class="btn-group float-right d-none d-md-inline" role="group" aria-label="View User">
                <a href="{{ route('users.admin.show', $proxy->getProxy()->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                </div>
            </span>
          </td>
          <td data-title="Principle">
            <span class="align-middle">
              {{ $proxy->getPrincipal()->getFullname() }}
              <div class="btn-group float-right d-none d-md-inline" role="group" aria-label="View User">
                <a href="{{ route('users.admin.show', $proxy->getPrincipal()->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                </div>
            </span>
          </td>
          <td data-title="Designated at">{{ $proxy->getCreatedAt()->toDateTimeString() }}</td>
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
