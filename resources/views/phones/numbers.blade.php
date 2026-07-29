@extends('layouts.app')

@section('pageTitle', 'Your Numbers')

@section('content')
<div class="container">
  <a class="btn btn-primary btn-block" href="{{ route('phones.extensions.register') }}" role="button">Register a Number</a>
  <br>
  
  @forelse ($extensions as $extension)
  @if ($loop->first)
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th class="w-15">Extension</th>
          <th class="w-25">Type</th>
          <th>Description</th>
          <th class="w-25">Actions</th>
        </tr>
      </thead>
      <tbody>
  @endif
        <tr>
          <td>
            {{ $extension->getExtension() }}
            @if ($extension->getPhoneword())
            <span class="badge badge-success">{{ $extension->getPhoneword() }}</span>
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
              Delete
            </a>
            <a class="btn btn-primary btn-sm" href="{{ route('phones.extensions.edit', $extension->getExtension()) }}" role="button">Edit</a>
            @if ($extension->getType() !== 'CUSTOM')
            <a class="btn btn-success btn-sm" href="{{ route('phones.extensions.setup', $extension->getExtension()) }}" role="button">Setup</a>
            @endif
            @if ($extension->getHidden())
            <span class="text-primary" title="This number is ex-directory."><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
            @endif
          </td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>
  
  <div class="pagination-links center">
    {{ $extensions->links() }}
  </div>    
  @endif
  @empty
  <div class="alert alert-light" role="alert">
    Oh no! You don't have any extensions yet. Click the big button above to register one.
  </div>
  @endforelse

</div>
@endsection
