@extends('layouts.app')

@section('pageTitle', 'Meta Values')

@section('content')
<div class="container">
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
          <a class="btn btn-primary btn-sm" href="{{ route('metas.edit', $meta->getKey()) }}">Edit</a>
          @endcan
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div classs="pagination">
    {{ $metas->links() }}
  </div>
</div>
@endsection
