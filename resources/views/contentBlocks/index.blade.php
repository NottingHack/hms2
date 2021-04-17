@extends('layouts.app')

@section('pageTitle', 'Content blocks')

@section('content')
<div class="container">
  <p>
    From here a limited set for page content chunks can be edited with out needing to PR changes to HMS.<br>
    There are two types, <strong>Page</strong> & <strong>Email</strong>, this related to where this chuck is use and weather it should be written as pure HTML for display on a site page, or as markdown for email where it will be rendered to both text and HTML.
  </p>
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Type</th>
          <th>View</th>
          <th>Block</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($contentBlocks as $contentBlock)
        <tr>
          <td data-title="Type">{{ $contentBlock->getTypeString() }}</td>
          <td data-title="View">{{ $contentBlock->getView() }}</td>
          <td data-title="Block">{{ $contentBlock->getBlock() }}</td>
          <td data-title="Actions" class="actions">
            <a class="btn btn-primary btn-sm" href="{{ route('content-blocks.show', $contentBlock->getId()) }}"><i class="fas fa-eye" aria-hidden="true"></i> View</a>
            @can('contentBlock.edit')
            <a class="btn btn-primary btn-sm" href="{{ route('content-blocks.edit', $contentBlock->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
            @endcan
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div classs="pagination">
    {{ $contentBlocks->links() }}
  </div>
  <hr>
  <dl>
    <dt>Type</dt>
    <dd>
      <ul>
        <li><strong>Page</strong>: pure HTML for display on site</li>
        <li><strong>Email</strong>: markdown for rendering to both text and HTML</li>
      </ul>
    </dd>
    <dt>View</dt>
    <dd>This is the blade file this chunk is use in</dd>
    <dt>Block</dt>
    <dd>Name for the block of content within the page</dd>
  </dl>
</div>
@endsection
