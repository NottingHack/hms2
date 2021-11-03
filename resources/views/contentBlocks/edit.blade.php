@extends('layouts.app')

@section('pageTitle')
Editing block for {{ $contentBlock->getView() }}:{{ $contentBlock->getBlock() }}
@endsection

@section('content')
<div class="container">
  <p>The following can not be changed at this time</p>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <tbody>
        <tr>
          <th class="w-15">Type</th>
          <td>{{ $contentBlock->getTypeString() }}</td>
        </tr>
        <tr>
          <th>View:</th>
          <td>
            {{ $contentBlock->getView() }} <a href="{{ $contentBlock->getViewGithubUrl() }}" class="btn btn-primary btn-sm mb-1" target="_blank">View Template on Github</a>
          </td>
        </tr>
        <tr>
          <th>Block name:</th>
          <td>{{ $contentBlock->getBlock() }}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <hr>
  <ul>
    <li>For Type <strong>Page</strong> content below should be HTML</li>
    <li>For Type <strong>Email</strong> content below should be Markdown</li>
  </ul>
  <form role="form" method="POST" action="{{ route('content-blocks.update', $contentBlock->getId()) }}">
    @csrf
    @method('PATCH')

    <div class="form-group">
      <label for="content" class="form-label">Content</label>
      <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10">{{ old('content', $contentBlock->getContent()) }}</textarea>
      @error('content')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <button type="submit" class="btn btn-success btn-block">Update</button>
  </form>
</div>
@endsection
