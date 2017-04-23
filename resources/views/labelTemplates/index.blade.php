@extends('layouts.app')

@section('content')
<h1>Label Templates</h1>
<p>Label templates printed via the thermal printer.<br/>
Templates are written in EPL2<br/>
Approx 780w x 600h</p>
<ul>
@foreach ($labelTemplates as $labelTemplate)
    <li>
        {{ $labelTemplate->getTemplateName() }}
    @can('labelTemplate.edit')

         - <a href="{{ route('labels.show', $labelTemplate->getTemplateName()) }}">View</a>
         - <a href="{{ route('labels.edit', $labelTemplate->getTemplateName()) }}">Edit</a>
         - <a href="javascript:void(0);" onclick="$(this).find('form').submit();">
            <form action="{{ route('labels.destroy', $labelTemplate->getTemplateName()) }}" method="POST" style="display: inline">
             {{ method_field('DELETE') }}
             {{ csrf_field() }}
             Remove
            </form>
            </a>
    @endcan
    @if (SiteVisitor::inTheSpace())
        @can('labelTemplate.print')
         - <a href="{{ route('labels.showPrint', $labelTemplate->getTemplateName()) }}">Print</a>
        @endcan
    @endif
   </li>
@endforeach
</ul>
@can('labelTemplate.create')
<div>
    <a href="{{ route('labels.create') }}">Add new template</a>
</div>
@endcan
<div classs="pagination-links">
    {{ $labelTemplates->links() }}
</div>
@endsection
