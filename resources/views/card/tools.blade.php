<div class="card">
  <div class="card-header">Tool Bookings</div>
  <booking-calendar-list
  classs="card-body"
  bookings-url="{{ route('api.bookings.index', ['tool' => '_ID_']) }}"
  :initial-bookings="{{ json_encode($bookings) }}"
  :remove-card-class="true"
  ></booking-calendar-list>
  <div class="card-footer">
    <a href="{{ route('tools.index') }}" class="btn btn-primary mb-1">Add Booking</a>
    <a href="https://goo.gl/Jl59IM" class="btn btn-primary mb-1" target="_blank">Request an Indcution</a>
  </div>
</div>
