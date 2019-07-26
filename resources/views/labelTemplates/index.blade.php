@extends('layouts.app')

@section('pageTitle', 'Label Templates')

@section('content')
<div class="container">
  <p>Label templates printed via the thermal printer.<br> Templates are written in EPL2<br> Approx 780w x 600h</p>
  <br>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($labelTemplates as $labelTemplate)
        <tr>
          <td>{{ $labelTemplate->getTemplateName() }}</td>

          <td>
            @can('labelTemplate.edit')
            <a class="btn btn-primary btn-sm mb-1" href="{{ route('labels.show', $labelTemplate->getTemplateName()) }}"><i class="far fa-eye" aria-hidden="true"></i> View</a>
            <a class="btn btn-primary btn-sm mb-1" href="{{ route('labels.edit', $labelTemplate->getTemplateName()) }}"><i class="fas fa-pencil fa-lg" aria-hidden="true"></i> Edit</a>
            <a class="btn btn-danger btn-sm mb-1" href="javascript:void(0);" onclick="$(this).find('form').submit();">
              <form action="{{ route('labels.destroy', $labelTemplate->getTemplateName()) }}" method="POST" style="display: inline">
                @method('DELETE')
                @csrf
              </form>
              <i class="fas fa-trash fa-lg" aria-hidden="true"></i> Remove
            </a>
            @endcan
            @if (SiteVisitor::inTheSpace())
            @can('labelTemplate.print')
            <a class="btn btn-primary btn-sm mb-1" href="{{ route('labels.showPrint', $labelTemplate->getTemplateName()) }}"><i class="fas fa-print" aria-hidden="true"></i> Print</a>
            @endcan
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @can('labelTemplate.create')
  <a class="btn btn-primary btn-block" href="{{ route('labels.create') }}"><i class="fas fa-plus" aria-hidden="true"></i> Add new template</a>
  @endcan
  <div classs="pagination-links">
    {{ $labelTemplates->links() }}
  </div>
</div>
@endsection
