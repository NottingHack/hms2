@extends('layouts.app')

@section('pageTitle', $tool->getDisplayName() . ' ' . HMS\Tools\ToolManager::GRANT_STRINGS[$grantType] . 's')

@section('content')
<div class="container">
  @if (($grantType == "MAINTAINER" && Auth::user()->can('tools.maintainer.grant')) || ($grantType == "INDUCTOR" && (Auth::user()->can('tools.inductor.grant') || Auth::user()->can('tools.' . $tool->getPermissionName() . '.inductor.grant'))) || ($grantType == "USER" && Auth::user()->can('tools.user.grant')))
  <tool-grant-modal :tool-id="{{ $tool->getId() }}" tool-name="{{ $tool->getDisplayName() }}" grant-type="{{ $grantType }}" :block="true"></tool-grant-modal><br>
  @endif
  @forelse ($toolUsers as [$user, $rolUdpate])
  @if ($loop->first)
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <th>Name</th>
        <th class="d-none d-md-table-cell">Date Appointed</th>
        <th class="d-none d-md-table-cell">Appointed By</th>
        <th class="w-25">Actions</th>
      </thead>
      <tbody>
  @endif
        <tr>
          <td data-title="Name">{{ $user->getFullname() }}</td>
          <td data-title="Date Appointed" class="d-none d-md-table-cell">
            @isset($rolUdpate)
            {{ $rolUdpate->getCreatedAt()->toDateTimeString() }}
            @endisset
          </td>
          <td data-title="Appointed By" class="d-none d-md-table-cell">
            @isset($rolUdpate)
            {{ $rolUdpate->getUpdateBy() ? $rolUdpate->getUpdateBy()->getFullname() : '' }}
            @endisset
          </td>
          <td data-title="Actions" class="actions">
          @canany(['profile.view.limited', 'profile.view.all'])
          <a href="{{ route('users.admin.show', $user->getId()) }}" class="btn btn-primary btn-sm mb-1"><i class="far fa-eye" aria-hidden="true"></i> View</a>
          @endcanany
          @if (($grantType == "MAINTAINER" && Auth::user()->can('tools.maintainer.grant')) || ($grantType == "INDUCTOR" && Auth::user()->can('tools.inductor.grant')) || ($grantType == "USER" && Auth::user()->can('tools.user.grant')))
          <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-danger btn-sm mb-1" aria-label="delete">
            <form action="{{ route('tools.revoke.users', ['tool' => $tool->getId(), 'grantType' => $grantType, 'user' => $user->getId()]) }}" method="POST" style="display: inline">
              @method('DELETE')
              @csrf
            </form>
            <i class="fas fa-trash" aria-hidden="true"></i> Remove
          </a>
          @endif
          </td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>
  <div class="pagination-links">
    {{ $users->links() }}
  </div>
  @endif
  @empty
  <p>No users with this access level.</p>
  @endforelse
</div>
@endsection
