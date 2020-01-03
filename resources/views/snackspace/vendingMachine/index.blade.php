@extends('layouts.app')

@section('pageTitle', 'Vending Machines')

@section('content')
<div class="container">
  <p>Here you can set up vending machines as needed.</p>
  <h2>Vending Machines</h2>
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($vendingMachines as $vendingMachine)
        <tr>
          <td data-title="Name">{{ $vendingMachine->getDescription() }}</td>
          <td calss='actions'>
            {{-- <a class="btn btn-primary" href="{{ route('snackspace.vending-machines.show', $vendingMachine->getId()) }}"><i class="fas fa-eye" aria-hidden="true"></i> View</a>
            @can('snackspace.vendingMachine.edit')
            <a class="btn btn-primary" href="{{ route('snackspace.vending-machines.edit', $vendingMachine->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
            @endcan --}}
            @can('snackspace.vendingMachine.locations.assign')
            <a class="btn btn-primary" href="{{ route('snackspace.vending-machines.locations.index', $vendingMachine->getId()) }}"><i class="fas fa-grip-vertical"></i> Locations</a>
            @endcan
            {{-- @can('snackspace.vendingMachine.reconcile')
            <a class="btn btn-primary" href="{{ route('snackspace.vending-machines.logs.jams', ['vendingMachine' => $vendingMachine->getId()]) }}"><i class="fas fa-file-check" aria-hidden="true"></i> Reconcile jam</a>
            @endcan --}}
            {{-- @can('snackspace.transaction.view.all')
            <a class="btn btn-primary" href="{{ route('snackspace.vending-machines.logs.show', ['vendingMachine' => $vendingMachine->getId()]) }}"><i class="fas fa-list" aria-hidden="true"></i> View logs</a>
            @endcan --}}
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <h2>Payment Machines</h2>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($paymentMachines as $vendingMachine)
        <tr>
          <td data-title="Name">{{ $vendingMachine->getDescription() }}</td>
          <td calss='actions'>
            {{-- <a class="btn btn-primary" href="{{ route('snackspace.vending-machines.show', $vendingMachine->getId()) }}"><i class="fas fa-eye" aria-hidden="true"></i> View</a>
            @can('snackspace.vendingMachine.edit')
            <a class="btn btn-primary" href="{{ route('snackspace.vending-machines.edit', $vendingMachine->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
            @endcan --}}
            @can('snackspace.vendingMachine.reconcile')
            <a class="btn btn-primary" href="{{ route('snackspace.vending-machines.logs.jams', ['vendingMachine' => $vendingMachine->getId()]) }}"><i class="fas fa-file-check" aria-hidden="true"></i> Reconcile jam</a>
            @endcan
            @can('snackspace.transaction.view.all')
            <a class="btn btn-primary" href="{{ route('snackspace.vending-machines.logs.show', ['vendingMachine' => $vendingMachine->getId()]) }}"><i class="fas fa-list" aria-hidden="true"></i> View logs</a>
            @endcan
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
