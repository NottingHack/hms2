@extends('layouts.app')

@section('pageTitle', 'Joint Accounts')

@section('content')
<div class="container">
  <p>
    These are all the current joint accounts. To make any changes to a single joint account, please click the View button.
  </p>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Payment Reference</th>
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
          <td class="actions"><a class="btn btn-primary" href="{{ route('banking.accounts.show', ['account' => $jointAccount->getId()]) }}"><i class="far fa-eye" aria-hidden="true"></i> View</a></td>
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
  <member-search action="banking.accounts.show" placeholder="Search for an Account..." :with-account="true" :return-account-id="true"></member-search>
</div>
@endsection
