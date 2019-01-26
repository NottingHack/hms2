@extends('layouts.app')

@section('pageTitle')
Account {{ $account->getPaymentRef() }}
@endsection

@section('content')
<div class="container">
  <h2>Id: {{ $account->getId() }}</h2>
  <p>
  Words about how to link and unlink a user from an account, how unlink we create a new refrence for a user
  </p>

  {{-- Users --}}
  <h2>Users</h2>
  <hr>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <tbody>
        @foreach ($account->getUsers() as $user)
        <tr>
          <td>{{ $user->getFullname() }}, {{ $user->getEmail() }}</td>
          <td class="actions"><a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-danger btn-sm" aria-label="delete">
            <form action="{{ route('banking.accounts.unlinkUser', ['account' => $account->getId()]) }}" method="POST" style="display: inline">
              @method('PATCH')
              @csrf
              <input type="hidden" name="user_id" value="{{ $user->getId() }}">

              <input type="hidden" name="new-account" value="1">
            </form>
            <i class="fas fa-trash" aria-hidden="true"></i> Unlink
          </a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <hr>
  <div>
    <form id='linkUser' role="form" method="POST" action="{{ route('banking.accounts.linkUser', ['account' => $account->getId()]) }}">
      @method('PATCH')
      @csrf

      <select name="user_id" class="js-data-member-search-ajax" style="width: 100%">
      </select>


      <div class="form-group text-center">
        <input type="submit" class="btn btn-success" name="save" value="Link User">
      </div>
    </form>
  </div>
</div>
@endsection
