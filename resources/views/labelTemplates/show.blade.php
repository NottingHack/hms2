@extends('layouts.app')

@section('pageTitle', 'Edit Label Template')

@section('content')
<div class="container">
<div class="card">
  <h4 class="card-header"> {{ $templateName}} </h4>
  <div class="card-body">
    <pre class="card-text">{{ $template}}</pre>
    </div>
    <div class="card-footer">
    @can('labelTemplate.edit')
     <a class="btn btn-primary btn-sm btn-sm-spacing" href="{{ route('labels.edit', $templateName) }}" class="button">Edit</a>
      <a class="btn btn-primary btn-sm btn-sm-spacing" href="javascript:void(0);" onclick="$(this).find('form').submit();" class="alert button">
        <form action="{{ route('labels.destroy', $templateName) }}" method="POST" style="display: inline">
         {{ method_field('DELETE') }}
         {{ csrf_field() }}
         Remove
        </form>
        </a>
@endcan
@if (SiteVisitor::inTheSpace())
    @can('labelTemplate.print')
      <a class="btn btn-primary btn-sm btn-sm-spacing" href="{{ route('labels.showPrint', $templateName) }}" class="button">Print</a>
    @endcan
@endif
  </div>
</div>
    
    </div>
@endsection
