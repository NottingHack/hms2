@extends('layouts.app')

@section('pageTitle', $vendingMachine->getDescription() . ': Reconcile Jam')

@section('content')
<div class="container">
  @forelse ($vendLogs as $vendLog)
  @php
  $transaction = $vendLog->getTransaction()
  @endphp
  @if ($loop->first)
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>VendLog Id</th>
          <th>Transaction Id</th>
          <th>Date</th>
          <th>User</th>
          <th>Type</th>
          <th>Amount</th>
          <th>Description</th>
          <th class="w-15">Actions</th>
        </tr>
      </thead>
      <tbody>
  @endif
        <tr>
          <td>{{ $vendLog->getId() }}</td>
          <td>{{ $transaction->getId() }}</td>
          <td>{{ $transaction->getTransactionDatetime()->toDateTimeString() }}</td>
          <td>
            <span class="align-middle">
              {{ $transaction->getUser()->getFullname() }}
              <div class="btn-group float-right d-none d-md-inline" role="group" aria-label="View User">
                <a href="{{ route('users.admin.show', $transaction->getUser()->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                </div>
            </span>
          </td>
          <td>{{ $transaction->getTypeString() }}</td>
          <td>@format_pennies($transaction->getAmount())</td>
          <td>{{ $transaction->getDescription() }}</td>
          <td>
              <form role="form" method="POST" action="{{ route('snackspace.vending-machines.logs.reconcile', ['vendingMachine' => $vendingMachine->getId(), 'vendLog' => $vendLog->getId()]) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success btn-sm mb-1" name="action" value="complete"><i class="fas fa-check" aria-hidden="true"></i> Complete</button>
                <button type="submit" class="btn btn-danger btn-sm mb-1" name="action" value="abort"><i class="fas fa-times" aria-hidden="true"></i> Abort</button>
              </form>
          </td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>
  <div class="pagination-links">
    {{ $vendLogs->links() }}
  </div>
  @endif
  @empty
  <p>No Jams for this Machine.</p>
  @endforelse
</div>
@endsection
