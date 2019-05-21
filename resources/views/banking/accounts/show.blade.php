@extends('layouts.app')

@section('pageTitle')
Account {{ $account->getPaymentRef() }}
@endsection

@section('content')
<div class="container">
  <p>
  Words about how to link and unlink a user from an account, how unlink we create a new refrence for a user
  </p>

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
          <td>{{ $user->getFullname() }}</td>
          <td>{{ $user->getEmail() }}</td>
          <td class="actions"><a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-danger btn-sm" aria-label="delete">
            <form action="{{ route('banking.accounts.unlinkUser', ['account' => $account->getId()]) }}" method="POST" style="display: inline">
              @method('PATCH')
              @csrf
              <input type="hidden" name="user_id" value="{{ $user->getId() }}">
              {{-- TODO: rework this this to open a model like membership.showDetails has --}}
              <input type="hidden" name="new-account" value="1">
            </form>
            <i class="fas fa-unlink" aria-hidden="true"></i> Un-Link
          </a></td>
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
  </div>
</div>
@endsection
