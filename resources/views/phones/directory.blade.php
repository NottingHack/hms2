@extends('layouts.app')

@section('pageTitle', 'Directory')

@section('content')
<div class="container">
  <div class="dropdown show">
    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Filter
    </a>

    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
      <a class="dropdown-item @if (!$categoryFilter) active @endif" href="{{ route('phones.directory') }}">
        <i class="fas fa-asterisk fa-fw"></i>
        All Extensions
      </a>
      <div class="dropdown-divider"></div>
      @foreach ($categories as $category => $description)
      <a class="dropdown-item @if ($categoryFilter === $category) active @endif" href="{{ route('phones.directory') }}?category={{ $category }}">
        @if ($category === 'MEMBER')
        <i class="fa fa-user fa-fw"></i>
        @elseif ($category === 'AREA')
        <i class="fa fa-building fa-fw"></i>
        @elseif ($category === 'SERVICE')
        <i class="fa fa-robot fa-fw"></i>
        @endif
        {{ $description }}
      </a>
      @endforeach
    </div>
  </div><br>

  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th class="w-5"></th>
          <th class="w-15">Extension</th>
          <th>Description</th>
          <th class="w-25">Type</th>
          @can('phones.edit.all')
          <th class="w-10">Admin</th>
          @endcan
        </tr>
      </thead>
      <tbody>
        @foreach ($extensions as $extension)
        @if (! $extension->getHidden() || Auth::user()->can('phones.view.directory.all'))
        <tr>
          <td>
            @if ($extension->getCategory() === 'MEMBER')
            <span class="text-primary" title="This extension is for a specific member."><i class="fa fa-user fa-fw"></i></span>
            @elseif ($extension->getCategory() === 'AREA')
            <span class="text-primary" title="This is a shared extension for an area of the space."><i class="fa fa-building fa-fw"></i></span>
            @elseif ($extension->getCategory() === 'SERVICE')
            <span class="text-primary" title="This is an automated service."><i class="fa fa-robot fa-fw"></i></span>
            @endif
          </td>
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
            @if ($extension->getHidden())
            <span class="text-primary" title="This number is hidden to most members."><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
            @endif
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
