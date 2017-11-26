@extends('layouts.app')

@section('pageTitle', 'Links')

@section('content')
<p>Useful links for members</p>

@can('link.create')
<div class="nav">
  <div class="nav-item">
    <a href="{{ route('links.create') }}" class="nav-link btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Add new link</a>
  </div>
</div>
@endcan

<ul class="list-unstyled">
  @foreach ($links as $link)
  <li>
    <div class="card my-3">
      <div class="card-body">
        <div class="d-flex card-title">
          <div class="link-name">
            <a href="{{ $link->getLink() }}" target="_blank">{{ $link->getName() }}</a>
          </div>

          @can('link.edit')
          <ul class="list-inline ml-auto">
            <li class="list-inline-item"><a href="{{ route('links.edit', $link->getId()) }}"><i class="fa fa-edit fa-lg" aria-hidden="true"></i> Edit</a></li>
            <li class="list-inline-item">
              <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="alert">
                <form action="{{ route('links.destroy', $link->getId()) }}" method="POST" style="display: none">
                  {{ method_field('DELETE') }}
                  {{ csrf_field() }}
                </form>
                <i class="fa fa-trash fa-lg" aria-hidden="true"></i> Remove
              </a>
            </li>
          </ul>
          @endcan
        </div>
        @if ($link->getDescription())
        <div class="link-description">
          {{ $link->getDescription() }}
        </div>
        @endif
      </div>
    </div>
  </li>
  @endforeach
</ul>

<div classs="pagination-links">
  {{ $links->links() }}
</div>
@endsection
