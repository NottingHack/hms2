<div {{-- class="col-lg-4 mb-3" --}}>
  <div class="card">
    <div class="card-header">
      Space access
    </div>
    <ul class="list-group list-group-flush">
      <li class="list-group-item">Door codes: ...</li>
      <li class="list-group-item">You have x RFID cards</li>
    </ul>
    <div class="card-footer">
      <a href="#" class="btn btn-primary">Manage RFID Cards</a>
      @can('pins.reactivate')
      <a href="#" class="btn btn-primary">Reactivate Pin</a>
      @endcan
    </div>
  </div>
</div>

