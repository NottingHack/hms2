@extends('layouts.app')

@section('pageTitle', 'Joint Accounts')

@section('content')
<div class="container">
  <p>
    Words about the accounts below being a list of all current joint accounts.
  </p>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Payment Refrence</th>
          <th>Users</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($jointAccounts as $jointAccount)
        <tr>
          <td>{{ $jointAccount->getId() }}</td>
          <td>{{ $jointAccount->getPaymentRef() }}</td>
          <td>
          @foreach ($jointAccount->getUsers() as $user)
            {{ $user->getFullname() }}
            @if (! $loop->last)
                ,
            @endif
          @endforeach
          </td>
          <td class="actions"><a class="btn btn-primary" href="{{ route('banking.accounts.show', $jointAccount->getId()) }}"><i class="fas fa-eye" aria-hidden="true"></i> View</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="pagination-links">
    {{ $jointAccounts->links() }}
  </div>
  <hr>
  <p>Search for an account to view</p>
  <member-search action="{{ route('banking.accounts.show', ['user' => '_ID_']) }}" placeholder="Search for an Account..." :with-account="true" :return-account-id="true"></member-search>
</div>
@endsection
