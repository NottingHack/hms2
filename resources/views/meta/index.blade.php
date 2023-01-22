@extends('layouts.app')

@section('pageTitle', 'Meta Values')

@section('content')
<div class="container">
  <div class="alert alert-warning">
    Editing Meta values could break things.<br>
    You should only be editing these value if you really know what they are controlling!
  </div>
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Key</th>
          <th>Value</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($metas as $meta)
        <tr>
          <td data-title="Key">{{ $meta->getKey() }}</td>
          <td data-title="Value">{{ $meta->getValue() }}</td>
          <td data-title="Actions" class="actions">
            @can('meta.edit')
            <a class="btn btn-primary btn-sm" href="{{ route('metas.edit', $meta->getKey()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
            @endcan
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div classs="pagination">
    {{ $metas->links() }}
  </div>
</div>
@endsection
