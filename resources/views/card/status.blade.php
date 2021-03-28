<div class="card">
  <div class="card-header">Membership Status</div>
  <table class="table">
    <tbody>
      <tr>
        <th scope="row" class="w-25">Status:</th>
        <td>{{ $user->getMemberStatusString() }}</td>
      </tr>
      @feature('voting_status')
      @can('governance.voting.canVote')
      <tr>
        <th scope="row">Voting Status:</th>
        <td class="align-middle">
          <span class="align-middle">
            {{ $votingStatus }}&nbsp;
            <div class="btn-group float-right" role="group" aria-label="View Voting Status">
              <a href="{{ route('governance.voting.index') }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
            </div>
          </span>
        </td>
      </tr>
      @endcan
      @endfeature
    </tbody>
  </table>
</div>
