@extends('layouts.app')

@section('pageTitle', 'Your Numbers')

@section('content')
<div class="container">
  <a class="btn btn-primary btn-block" href="{{ route('phones.extensions.register') }}" role="button">Register a Number</a>
  <br>

  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Extension</th>
          <th>Type</th>
          <th>Description</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($extensions as $extension)
        <tr>
          <td>
            {{ $extension->getExtension() }}
            @if ($extension->getPhoneword())
              ({{ $extension->getPhoneword() }})
            @endif
          </td>
          <td>
            {{ $extension->getTypeString() }}
          </td>
          <td>
            {{ $extension->getDescription() }}
          </td>
          <td>
            <a class="btn btn-danger btn-sm" href="javascript:void(0);" onclick="$(this).find('form').submit();" role="button">
            <form action="{{ route('phones.extensions.delete', $extension->getExtension()) }}" method="POST" style="display: inline">
              @method('DELETE')
              @csrf
            </form>
            Delete</a>
            @if ($extension->getType() !== 'CUSTOM')
            <a class="btn btn-primary btn-sm" href="{{ route('phones.extensions.setup', $extension->getExtension()) }}" role="button">Setup</a>
            @endif
            <a class="btn btn-primary btn-sm" href="{{ route('phones.extensions.edit', $extension->getExtension()) }}" role="button">Edit</a>
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
