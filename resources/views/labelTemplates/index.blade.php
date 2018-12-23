@extends('layouts.app')

@section('pageTitle', 'Label Templates')

@section('content')
<div class="container">
  <p>Label templates printed via the thermal printer.<br> Templates are written in EPL2<br> Approx 780w x 600h</p>
  <br>
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th scope="col">Name</th>
        <th scope="col">Actions</th>
      </tr>
    </thead>
    @foreach ($labelTemplates as $labelTemplate)
    <tr>
      <td>{{ $labelTemplate->getTemplateName() }}</td>

      <td>
        @can('labelTemplate.edit')
        <a class="btn btn-primary btn-sm btn-sm-spacing" href="{{ route('labels.show', $labelTemplate->getTemplateName()) }}"><i class="fa fa-eye" aria-hidden="true"></i> View</a>
        <a class="btn btn-primary btn-sm btn-sm-spacing" href="{{ route('labels.edit', $labelTemplate->getTemplateName()) }}"><i class="fa fa-edit fa-lg" aria-hidden="true"></i> Edit</a>
        <a class="btn btn-danger btn-sm btn-sm-spacing" href="javascript:void(0);" onclick="$(this).find('form').submit();">
          <form action="{{ route('labels.destroy', $labelTemplate->getTemplateName()) }}" method="POST" style="display: inline">
            {{ method_field('DELETE') }}
            {{ csrf_field() }}
            <i class="fa fa-trash fa-lg" aria-hidden="true"></i> Remove
          </form>
        </a>
        @endcan
        @if (SiteVisitor::inTheSpace())
        @can('labelTemplate.print')
        <a class="btn btn-primary btn-sm btn-sm-spacing" href="{{ route('labels.showPrint', $labelTemplate->getTemplateName()) }}"><i class="fa fa-print" aria-hidden="true"></i> Print</a>
        @endcan
        @endif
      </td>
    </tr>
    @endforeach
  </table>
  @can('labelTemplate.create')
  <div class="card">
    <a class="btn btn-primary" href="{{ route('labels.create') }}" class="button"><i class="fa fa-plus" aria-hidden="true"></i> Add new template</a>
  </div>
  @endcan
  <div classs="pagination-links">
    {{ $labelTemplates->links() }}
  </div>
</div>
@endsection
