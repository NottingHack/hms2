@can('forms.index')
<div class="card">
  <div class="card-header">Forms</div>
  <ul class="list-group list-group-flush">
    @foreach ($forms as $form)
    @can(['forms.' . $form->getPermissionName() . '.respond'])
    <a class="list-group-item list-group-item-action"
      href="{{ route('forms.view', $form->getId()) }}">{{ $form->getName() }}
      @canany(['forms.viewResponses', 'forms.' . $form->getPermissionName() . '.viewResponses'])
      @if ($formResponseCounts[$form->getId()] > 0)
      <span class="badge badge-primary" title="Responses pending review">
        {{ $formResponseCounts[$form->getId()] }}
      </span>
      @endif
      @endcan
    </a>
    @endcan
    @endforeach
  </ul>
</div>
@endcan
