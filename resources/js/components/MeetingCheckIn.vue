<template>
  <div>
    <div class="card-deck">
      <div class="card">
        <h4 class="card-header">Quorum Requirements</h4>
        <table class="table">
          <tbody>
            <tr>
              <th scope="row">Current Member Count:</th>
              <td>{{ currentMembers }}</td>
            </tr>
            <tr>
              <th scope="row">Voting Member Count:</th>
              <td>{{ votingMembers }}</td>
            </tr>
            <tr>
              <th scope="row">Quorum Required:</th>
              <td>{{ quorum }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card">
        <h4 class="card-header">Current Attendance</h4>
        <table class="table">
          <tbody>
            <tr>
              <th scope="row">
                <span class="align-middle">
                  Proxies Registered:&nbsp;
                  <div class="btn-group float-right" role="group" aria-label="View Proxies Registered">
                    <a :href="proxiesRoute" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                  </div>
                </span>
              </th>
              <td>{{ proxies }}</td>
            </tr>
            <tr>
              <th scope="row">
                <span class="align-middle">
                  Attendees in the room:&nbsp;
                  <div class="btn-group float-right" role="group" aria-label="View Proxies Registered">
                    <a :href="attendeesRoute" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                  </div>
                </span>
              </th>
              <td>{{ attendees }}</td>
            </tr>
            <tr>
              <th scope="row">Proxies Represented:</th>
              <td>{{ representedProxies }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="card">
        <h4 class="card-header">Total Checked-in</h4>
        <div class="card-body align-items-center d-flex justify-content-center @if ($checkInCount >= $meeting->getQuorum()) bg-success @else bg-danger @endif">
          <h1 class="card-text">{{ checkInCount }}</h1>
        </div>
      </div>
    </div>
    <hr>
    <h2>Member Check-in</h2>
    <p>
      Use the search bellow to find a member to check in.<br>
      You can search via Name, Username, Email or Post Code.<br>
      Search via Username or Email will give the smallest set of results.
    </p>
      <member-select-two
        ref="mst"
        v-model="userId"
        :name="null"
        :current-only="true"
        />
      <br>
      <button class="btn btn-primary btn-block" :disabled="buttonDisable" @click="checkInUser"><i class="fas fa-user-check fa-lg" aria-hidden="true"></i> Check-in</button>
    </form>
  </div>
</template>

<script>
  export default {
    props: {
      meetingId: Number,
    },

    data() {
      return {
        currentMembers: 0,
        votingMembers: 0,
        quorum: 0,
        attendees: 0,
        proxies: 0,
        representedProxies: 0,
        checkInCount: 0,
        userId: '',
        buttonDisable: false,
      };
    },

    computed: {
      proxiesRoute() {
        return this.route('governance.proxies.index', {'meeting': this.meetingId});
      },
      attendeesRoute() {
        return this.route('governance.meetings.attendees', {'meeting': this.meetingId});
      },
    },

    methods: {
      checkInUser(event) {
        this.buttonDisable = true;
        let checkIn = {
          user_id: this.userId,
        };

        axios.post(this.route('api.governance.meetings.check-in-user', {'meeting': this.meetingId}), checkIn)
         .then((response) => {
            if (response.status == '200') {
              this.attendeeCheckInEvent(response.data);
              this.clear();
              this.buttonDisable = false;
            }
          })
          .catch((error) => {
            flash('Error checking in user', 'danger');
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
              // else if HTTP_CONFLICT to many bookings or over lap
              // else if HTTP_FORBIDDEN on enough permissions
              console.log('checkInUser: Response error', error.response.data, error.response.status, error.response.headers);
            } else if (error.request) {
              // The request was made but no response was received
              // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
              // http.ClientRequest in node.js
              console.error('checkInUser: Request error', error.request);
            } else {
              // Something happened in setting up the request that triggered an Error
              console.error('checkInUser: Error', error.message);
            }
            this.buttonDisable = false;
          });
      },

      attendeeCheckInEvent(attendeeCheckIn) {
        // console.log('Echo sent attendeeCheckIn event', attendeeCheckIn);
        this.updateConuts(attendeeCheckIn);
        flash(attendeeCheckIn.checkInUser.name + ' - ' + attendeeCheckIn.checkInUser.message);
      },

      clear() {
        this.$refs.mst.clear();
      },

      updateConuts(newCounts) {
        this.currentMembers = newCounts.currentMembers;
        this.votingMembers = newCounts.votingMembers;
        this.quorum = newCounts.quorum;
        this.attendees = newCounts.attendees;
        this.proxies = newCounts.proxies;
        this.representedProxies = newCounts.representedProxies;
        this.checkInCount = newCounts.checkInCount;
      },

      fetchConuts() {
        axios.get(this.route('api.governance.meetings.show', {'meeting': this.meetingId}))
          .then((response) => {
            if (response.status == '200') {
              this.updateConuts(response.data);
            }
          })
          .catch((error) => {
            flash('Error getting counts', 'danger');
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
              // else if HTTP_CONFLICT to many bookings or over lap
              // else if HTTP_FORBIDDEN on enough permissions
              console.log('fetchConuts: Response error', error.response.data, error.response.status, error.response.headers);
            } else if (error.request) {
              // The request was made but no response was received
              // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
              // http.ClientRequest in node.js
              console.error('fetchConuts: Request error', error.request);
            } else {
              // Something happened in setting up the request that triggered an Error
              console.error('fetchConuts: Error', error.message);
            }
          });
      },

      echoInit() {
        Echo.channel('meetings.' + this.meetingId + '.attendeeCheckIn')
          .listen('Governance.AttendeeCheckIn', this.attendeeCheckInEvent);
      },

      echoDeInit() {
        Echo.leave('meetings.' + this.meetingId + '.attendeeCheckIn');
      },
    }, // end of methods

    mounted() {
      this.fetchConuts()
      this.echoInit();
    },

    beforeDestroy() {
      this.echoDeInit();
    },
  }
</script>
