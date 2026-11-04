@extends('layouts.app')

@section('content')
@section('pageTitle', $form->getName())

<div class="container-fluid">
  <div class="dropdownt">
    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownNumRows" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Display Options
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownNumRows">
      @foreach ([15, 50, 100] as $rows)
      <a class="dropdown-item" href="?perpage={{ $rows }}@if ($hidden)&hidden=true @endif">{{ $rows }} Rows per Page</a>
      @endforeach
      <div class="dropdown-divider"></div>
      @if ($hidden)
      <a href="?perpage={{ $perpage }}" class="dropdown-item">Hide Archived</a>
      @else
      <a href="?perpage={{ $perpage }}&hidden=true" class="dropdown-item">Show Archived</a>
      @endif
    </div>
  </div>
  <br />

  @foreach ($formResponses as $formResponse)
  @if ($loop->first)
  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          @foreach ($columns as $column)
          <th scope="col">{{ $column }}</th>
          @endforeach

          <th scope="col">Submission Date</th>
          <th scope="col">Author</th>
          <th scope="col">Comment</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
  @endif
        <tr>
        @foreach ($columns as $column)
          <td>
          @if (array_key_exists($column, $formResponse->getResponseJson()))
          @if (gettype($formResponse->getResponseJson()[$column]) === 'boolean')
            {{ $formResponse->getResponseJson()[$column] ? 'Yes' : 'No' }}
          @elseif (gettype($formResponse->getResponseJson()[$column]) === 'string')
          @if (str_starts_with($formResponse->getResponseJson()[$column], 'data:image/'))
            <img width="100" src="{{ $formResponse->getResponseJson()[$column]  }}" />
          @else
            {{ $formResponse->getResponseJson()[$column] }}
          @endif
          @elseif (gettype($formResponse->getResponseJson()[$column]) === 'array')
            {{ implode(', ', $formResponse->getResponseJson()[$column]) }}
          @endif
          @if (array_key_exists($column . '-Comment', $formResponse->getResponseJson()))
            ({{ $formResponse->getResponseJson()[$column . '-Comment'] }})
          @endif
          @endif
          </td>
        @endforeach
          <td class="text-nowrap">{{ $formResponse->getCreatedAt() }}</td>
          <td class="text-nowrap">{{ $formResponse->getResponder()->getFullName() }}</td>
          <td class="text-nowrap">
            <form-comment-field
              form-response-id="{{ $formResponse->getId() }}"
              comment="{{ $formResponse->getComment() }}" />
          </td>
          <td class="text-nowrap">
            @if (! $formResponse->getHidden())
              <form-archive-button form-response-id="{{ $formResponse->getId() }}" />
            @endif
          </td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>
  @endif
  @endforeach

  <br />
  <div class="pagination-links">
    {{ $formResponses->links() }}
  </div>

</div>
@endsection
