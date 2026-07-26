@extends('layouts.app')

@section('pageTitle', 'Directory')

@section('content')
<div class="container">
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Extension</th>
          <th>Description</th>
          <th>Type</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($extensions as $extension)
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
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div classs="pagination-links center">
    {{ $extensions->links() }}
  </div>
</div>
@endsection
