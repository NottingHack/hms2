@extends('layouts.app')

@section('pageTitle')
Viewing block {{ $contentBlock->getView() }}:{{ $contentBlock->getBlock() }}
@endsection

@section('content')
<div class="container">
  <div class="table-responsive">
    <table class="table table-bordered">
      <tbody>
        <tr>
          <th class="w-15">Type</th>
          <td>{{ $contentBlock->getTypeString() }}</td>
        </tr>
        <tr>
          <th>View:</th>
          <td>{{ $contentBlock->getView() }}</td>
        </tr>
        <tr>
          <th>Block name:</th>
          <td>{{ $contentBlock->getBlock() }}</td>
        </tr>
        <tr>
          <th>Content (raw):</th>
          <td>{{ $contentBlock->getContent() }}</td>
        </tr>
        <tr>
          <th>Content (rendered):</th>
          <td class="table-light">{!! $contentBlock->getContent() !!}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <br>
  @can('contentBlock.edit')
  <a href="{{ route('content-blocks.edit', $contentBlock->getId()) }}" class="btn btn-info btn-block"> <i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
  @endcan
  <br>
</div>
@endsection
