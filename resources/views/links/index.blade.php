@extends('layouts.app')

@section('pageTitle', 'Links')

@section('content')
<p>Useful links for members</p>
<ul>
@foreach ($links as $link)
    <li>
        <a href="{{ $link->getLink() }}" target="_blank">{{ $link->getName() }}</a>
    @if ($link->getDescription())
        - {{ $link->getDescription() }}
    @endif
    @can('link.edit')
         - <a href="{{ route('links.edit', $link->getId()) }}">Edit</a>
         - <a href="javascript:void(0);" onclick="$(this).find('form').submit();">
            <form action="{{ route('links.destroy', $link->getId()) }}" method="POST" style="display: inline">
             {{ method_field('DELETE') }}
             {{ csrf_field() }}
             Remove
            </form>
            </a>
    @endcan
   </li>
@endforeach
</ul>
@can('link.create')
<div>
    <a href="{{ route('links.create') }}">Add new link</a>
</div>
@endcan
<div classs="pagination-links">
    {{ $links->links() }}
</div>
@endsection
