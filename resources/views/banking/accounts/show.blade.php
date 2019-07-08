@extends('layouts.app')

@section('pageTitle')
Account {{ $account->getPaymentRef() }}
@endsection

@section('content')
<div class="container">
  <p>These are the details for this joint account.</p>
  <p>To view the individual member's details, please click the eye icon next to the member's name.</p>
  <p>To unlink a member from this joint account, please click the Un-Link button. This will produce a new reference code or the individual user.</p>
  <p>To add a member to this joint account, please search for the member in the search bar below, and click Link User.</p>

  <h3>Users linked to this account</h3>
  <hr>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <tbody>
        <thead>
          <th>Name</th>
          <th>Email</th>
          <th>Action</th>
        </thead>
        @foreach ($account->getUsers() as $user)
        <tr>
          <td>
            <span class="align-middle">
              {{ $user->getFullname() }}
              <div class="btn-group float-right" role="group" aria-label="View User">
                <a href="{{ route('users.admin.show', $user->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                </div>
            </span>
          </td>
          <td>{{ $user->getEmail() }}</td>
          <td class="actions">
            @if($loop->count > 1)
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-danger btn-sm" aria-label="delete">
              <form action="{{ route('banking.accounts.unlinkUser', ['account' => $account->getId()]) }}" method="POST" style="display: inline">
                @method('PATCH')
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->getId() }}">
                {{-- TODO: rework this this to open a model like membership.showDetails has --}}
                <input type="hidden" name="new-account" value="1">
              </form>
              <i class="fas fa-unlink" aria-hidden="true"></i> Un-Link
            </a>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <hr>
  <form id='linkUser' role="form" method="POST" action="{{ route('banking.accounts.linkUser', ['account' => $account->getId()]) }}">
    @method('PATCH')
    @csrf
    <div class="form-group">
      <label>Select a user to link to this account</label>
      {{-- TODO: should we limit this to with-account? --}}
      <member-select-two :with-account="false"></member-select-two>
    </div>
    <div class="form-group text-center">
      <button class="btn btn-success"type="submit"><i class="fas fa-link" aria-hidden="true"></i> Link User</button>
    </div>
  </form>
  <hr>
  @if(Gate::allows('bankTransactions.view.all'))
  @forelse ($bankTransactions as $bankTransaction)
  @if ($loop->first)
  <p>Any payments that have been matched to this account ar listed below.</p>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Date</th>
          <th>Amount</th>
          <th>Bank Account</th>
        </tr>
      </thead>
      <tbody>
  @endif
        <tr>
          <td>{{ $bankTransaction->getTransactionDate()->toDateString() }}</td>
          <td><span class="money">@format_pennies($bankTransaction->getAmount())</span></td>
          <td>{{ $bankTransaction->getBank()->getName() }}</td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>

  <div class="pagination-links">
    {{ $bankTransactions->links() }}
  </div>
  @endif
  @empty
  <p>No payments matched to this account.</p>
  @endforelse
  @elseif(Auth::user() != $user && Gate::allows('bankTransactions.view.limited'))
  @if($bankTransactions->count() > 0)
  <div class="card">
    <h5 class="card-header">Last Payment Date:</h5>
    <div class="card-body">{{ $bankTransactions[0]->getTransactionDate()->toDateString() }}</div>
  </div>
  @else
  <p>No payments matched to this account.</p>
  @endif
  @endif
</div>
@endsection
