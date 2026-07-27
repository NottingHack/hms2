@extends('layouts.app')

@section('pageTitle', 'Directory')

@section('content')
<div class="container">
  <div class="dropdown show">
    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Filter
    </a>

    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
      <a class="dropdown-item" href="{{ route('phones.directory') }}">All Extensions</a>
      @foreach ($categories as $category => $description)
      <a class="dropdown-item" href="{{ route('phones.directory') }}?category={{ $category }}">{{ $description }}</a>
      @endforeach
    </div>
  </div><br>

  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Extension</th>
          <th>Description</th>
          <th>Type</th>
          @can('phones.edit.all')
          <th>Admin</th>
          @endcan
        </tr>
      </thead>
      <tbody>
        @foreach ($extensions as $extension)
        @if (! $extension->getHidden() || Auth::user()->can('phones.view.directory.all'))
        <tr>
          <td>
            {{ $extension->getExtension() }}
            @if ($extension->getPhoneword())
              <span class="badge badge-success">{{ $extension->getPhoneword() }}</span>
            @endif
          </td>
          <td>
            {{ $extension->getDescription() }}
          </td>
          <td>
            {{ $extension->getTypeString() }}
          </td>
          @can('phones.edit.all')
          <td>
            <a class="btn btn-primary btn-sm" href="{{ route('phones.extensions.edit', $extension->getExtension()) }}" role="button">Edit</a>
          </td>
          @endcan
        </tr>
        @endif
        @endforeach
      </tbody>
    </table>
  </div>

  <div classs="pagination-links center">
    {{ $extensions->links() }}
  </div>
</div>
@endsection
