@extends('layouts.app')

@section('pageTitle', 'Meta Values')

@section('content')
<div class="container">
  <div class="table-responsive">
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
          <td>{{ $meta->getKey() }}</td>
          <td>{{ $meta->getValue() }}</td>
          <td>
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
