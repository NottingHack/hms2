@extends('layouts.app')

@section('pageTitle', 'Edit Label Template')

@section('content')
<div class="container">
  <div class="card">
    <h4 class="card-header">{{ $templateName }}</h4>
    <div class="card-body">
      <pre class="card-text" v-pre>{{ $template }}</pre>
    </div>
    <div class="card-footer">
      @can('labelTemplate.edit')
      <a class="btn btn-primary btn-sm btn-sm-spacing" href="{{ route('labels.edit', $templateName) }}" class="button"><i class="fas fa-pencil fa-lg" aria-hidden="true"></i> Edit</a>
      <a class="btn btn-danger btn-sm btn-sm-spacing" href="javascript:void(0);" onclick="$(this).find('form').submit();" class="alert button">
        <form action="{{ route('labels.destroy', $templateName) }}" method="POST" style="display: inline">
          {{ method_field('DELETE') }}
          {{ csrf_field() }}
        </form>
        <i class="fas fa-trash fa-lg" aria-hidden="true"></i> Remove
      </a>
      @endcan
      @if (SiteVisitor::inTheSpace())
      @can('labelTemplate.print')
      <a class="btn btn-primary btn-sm btn-sm-spacing" href="{{ route('labels.showPrint', $templateName) }}" class="button"><i class="fas fa-print" aria-hidden="true"></i> Print</a>
      @endcan
      @endif
    </div>
  </div>
</div>
@endsection
