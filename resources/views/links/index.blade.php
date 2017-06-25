@extends('layouts.app')

@section('pageTitle', 'Links')

@section('content')
<p>Useful links for members</p>

@can('link.create')
<div>
    <a href="{{ route('links.create') }}" class="button"><i class="fa fa-plus" aria-hidden="true"></i> Add new link</a>
</div>
@endcan

<ul class="nomarkers">
@foreach ($links as $link)
    <li>
        <div class="card">
            <div class="card-section">
                <div class="row">
                    <div class="columns">
                        <div class="link-name">
                            <a href="{{ $link->getLink() }}" target="_blank">{{ $link->getName() }}</a>
                        </div>
                    </div>

                    @can('link.edit')
                    <div class="columns">
                        <ul class="horizontal menu align-right">
                            <li><a href="{{ route('links.edit', $link->getId()) }}"><i class="fa fa-edit fa-lg" aria-hidden="true"></i> Edit</a></li>
                            <li>
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="alert">
                                    <form action="{{ route('links.destroy', $link->getId()) }}" method="POST" style="display: none">
                                        {{ method_field('DELETE') }}
                                        {{ csrf_field() }}
                                    </form>
                                    <i class="fa fa-trash fa-lg" aria-hidden="true"></i> Remove
                                </a>
                            </li>
                        </ul>
                        </div>
                    @endcan
                </div>
                @if ($link->getDescription())
                    <div class="row">
                        <div class="link-description columns">
                            {{ $link->getDescription() }}
                        </div>
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
