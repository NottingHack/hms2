@can('tools.view')
<div class="card">
  <div class="card-header">Tool Bookings</div>
  <booking-calendar-list
    classs="card-body"
    :user-id="{{ $user->getId() }}"
    :initial-bookings="{{ json_encode($bookings) }}"
    :tool-ids="{{ json_encode($toolIds) }}"
    :remove-card-class="true"
    ></booking-calendar-list>
  <div class="card-footer">
    {{-- TODO: only if inducted on any tool --}}
    <a href="{{ route('tools.index') }}" class="btn btn-primary mb-1">Add Booking</a>
    <a href="{{ Meta::get('induction_request_html') }}" class="btn btn-primary mb-1" target="_blank">Request an Induction</a>
  </div>
</div>
@endcan
