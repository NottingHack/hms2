@extends('layouts.app')

@section('content')
<h1>{{ $templateName}}</h1>
<!-- TODO: shade this area -->
<div>
    <pre>{{ $template}}</pre>
</div>
<div>
@can('labelTemplate.edit')
     <a href="{{ route('labels.edit', $templateName) }}">Edit</a>
     - <a href="javascript:void(0);" onclick="$(this).find('form').submit();">
        <form action="{{ route('labels.destroy', $templateName) }}" method="POST">
         {{ method_field('DELETE') }}
         {{ csrf_field() }}
         Remove
        </form>
        </a>
@endcan
</div>
@endsection
