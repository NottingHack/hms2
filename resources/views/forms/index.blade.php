@extends('layouts.app')

@section('content')
@section('pageTitle', 'Forms')

<div class="container">
  @can('forms.edit')
  <a type="button" class="btn btn-primary btn-sm" href="{{ route('forms.new') }}">Create a new form</a>
  @endcan

  @foreach ($forms as $form)
  @if ($loop->first)
  <div class="table-responsive mt-4">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th scope="col">Form</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
  @endif
        @canany(['forms.respond', 'forms.' . $form->getPermissionName() . '.respond', 'forms.edit', 'forms.' . $form->getPermissionName() . '.edit', 'forms.viewResponses', 'forms.' . $form->getPermissionName() . '.viewResponses' ])
        <tr>
          <td class="text-nowrap">
            @if ($form->getMaxResponses() > 0 && $userResponseCounts[$form->getId()] >= $form->getMaxResponses())
            {{ $form->getName() }} <div><small class="text-muted">(You cannot respond to this form again)</small></div>
            @else
            <a href="{{ route('forms.view', $form->getId()) }}">{{ $form->getName() }}</a>
            @endif
          </td>
          <td>
            @canany(['forms.viewResponses', 'forms.' . $form->getPermissionName() . '.viewResponses'])
            <a type="button" class="btn btn-primary btn-sm" href="{{ route('forms.responses', $form->getId()) }}">
              View Responses
              @if ($formResponseCounts[$form->getId()] > 0)
              <span class="badge badge-light" title="Responses pending review">
                {{ $formResponseCounts[$form->getId()] }}
              </span>
              @endif
            </a>
            @endcan
            @canany(['forms.edit', 'forms.' . $form->getPermissionName() . '.edit'])
            <a type="button" class="btn btn-primary btn-sm" href="{{ route('forms.edit', $form->getId()) }}">Edit Form</a>
            @endcan
            @can('forms.edit')
            <a type="button" class="btn btn-danger btn-sm" href="{{ route('forms.permissions', $form->getId()) }}">Manage Access</a>
            @endcan
          </td>
        </tr>
        @endcan
  @endforeach
      </tbody>
    </table>

  <br />
  <div class="pagination-links">
    {{ $forms->links() }}
  </div>
</div>

@endsection
