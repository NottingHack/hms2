<template>
  <div class="vue-remove">
    <div class="table-responsive vld-parent" ref="buildingTable">
      <table class="table table-bordered">
        <thead>
          <th class="w-50">Name</th>
          <th>Access State</th>
          <th>Self Booking Max Occupancy</th>
        </thead>
        <tbody>
          <tr v-for="building in buildings">
            <td><span class="align-middle">{{ building.name }}</span></td>
            <td>
              <span class="align-middle">
                {{ building.accessStateString }}&nbsp;
                <div class="btn-group float-right" role="group" aria-label="Manage Project">
                  <button type="button" class="btn btn-primary btn-sm" @click="showAccessModal(building)"><i class="fas fa-pencil fa-sm" aria-hidden="true"></i></button>
                </div>
              </span>
            </td>
            <td>
              <span class="align-middle">
                {{ building.selfBookMaxOccupancy }}&nbsp;
                <div class="btn-group float-right" role="group" aria-label="Manage Project">
                  <button type="button" class="btn btn-primary btn-sm" @click="showOccupancyModal(building)"><i class="fas fa-pencil fa-sm" aria-hidden="true"></i></button>
                </div>
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Access state modal -->
    <div ref="accessModal" class="modal fade bd-example-modal-lg" tabindex="-1" :aria-labelledby="$id('accessLabel')" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content vld-parent" ref="accessModalContent">
          <div class="modal-header">
            <h5 class="modal-title" :id="$id('accessLabel')">Change Access State for {{ editingName }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>The current access state for {{ editingName }} is <strong>{{ editingAccessStateString }}</strong></p>
            <dl>
              <dt>Fully open</dt>
              <dd>
                <p>The building is fully open to all members, they can come and go 24/7. The access booking calendar will not be seen in HMS.</p>
                <p v-show="editingAccessState!='FULL_OPEN'">Changing to this state will hide the calendar and remove any future bookings.</p>
                <button
                  type="button"
                  class="btn btn-block"
                  :class="[editingAccessState=='FULL_OPEN' ? 'btn-primary' : 'btn-danger']"
                  :disabled="editingAccessState=='FULL_OPEN'"
                  data-toggle="change-access"
                  data-access-state="FULL_OPEN"
                  >
                  Change to Fully open
                </button>
              </dd>
              <dt>Booked access</dt>
              <dd>
                <p>Members need to self book access to enter the building. This is done through the access booking calendar in HMS. Access bookings are checked against global limits and bookable area limits and automatically approved/rejected by HMS.</p>
                <p v-show="editingAccessState!='SELF_BOOK'">Changing to this state will stop regular 24/7 access and show the calendar for members. Future bookings will remain unchanged.</p>
                <button
                  type="button"
                  class="btn btn-block"
                  :class="[editingAccessState=='SELF_BOOK' ? 'btn-primary' : 'btn-danger']"
                  :disabled="editingAccessState=='SELF_BOOK'"
                  data-toggle="change-access"
                  data-access-state="SELF_BOOK"
                  >
                  Change to Booked access
                </button>
              </dd>
              <dt>Requested access</dt>
              <dd>
                <p>Members can request an access booking, which needs approval before they can enter the building. This is done through the Access booking calendar in HMS. access bookings are checked against global limits and bookable area limits automatically but final approval/rejection is required from a Trustee</p>
                <p v-show="editingAccessState!='REQUESTED_BOOK'">Changing to this state will stop regular 24/7 access and show the calendar for members. Any non-Trustee future bookings will be reset to unapproved.</p>
                <button
                  type="button"
                  class="btn btn-block"
                  :class="[editingAccessState=='REQUESTED_BOOK' ? 'btn-primary' : 'btn-danger']"
                  :disabled="editingAccessState=='REQUESTED_BOOK'"
                  data-toggle="change-access"
                  data-access-state="REQUESTED_BOOK"
                  >
                  Change to Requested access
                </button>
              </dd>
              <dt>Closed</dt>
              <dd>
                <p>The building is closed to all but Trustees. The Access booking calendar will not be seen in HMS. Trustees may however grant access to others as they see fit using the access calendar.</p>
                <p v-show="editingAccessState!='CLOSED'">Changing to this state will stop regular 24/7 access and hide the calendar. Any non-Trustee future bookings will be removed.</p>
                <button
                  type="button"
                  class="btn btn-block"
                  :class="[editingAccessState=='CLOSED' ? 'btn-primary' : 'btn-danger']"
                  :disabled="editingAccessState=='CLOSED'"
                  data-toggle="change-access"
                  data-access-state="CLOSED"
                  >
                  Change to Closed
                </button>
              </dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <!-- Max occupancy modal -->
    <div ref="occupancyModal" class="modal fade" tabindex="-1" :aria-labelledby="$id('occupancyLabel')" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" :id="$id('occupancyLabel')">Set max occupancy for {{ editingName }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form :id="$id('updateOccupancy')" @submit.prevent="updateOccupancy">
              <div class="form-group">
                <label :for="$id('selfBookMaxOccupancy')" class="col-form-label">Self Booking Max Occupancy:</label>
                <input type="number" class="form-control" :id="$id('selfBookMaxOccupancy')" v-model.number="editingSelfBookMaxOccupancy" min="1">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" :form="$id('updateOccupancy')">Update</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import Loading from 'vue-loading-overlay';
  Vue.use(Loading);

  export default {
    data() {
      return {
        buildings: [],
        isLoading: true,
        editingId: '',
        editingName: '',
        editingAccessState: '',
        editingAccessStateString: '',
        editingSelfBookMaxOccupancy: '',
      };
    },

    methods: {
      loading(isLoading, fullPage=false) {
        this.isLoading = isLoading;
        if (isLoading && this.loader == null) {
          this.loader = this.$loading.show({
            container: fullPage ? null : this.$refs.buildingTable,
            color: '#195905',
          });
        } else if (this.loader !== null) {
          this.loader.hide();
          this.loader = null;
        }
      },

      fetchBuildings() {
        this.loading(true);

        axios.get(this.route('api.gatekeeper.buildings.index'))
          .then((response) => {
            if (response.status == '200') { // HTTP_OK
              this.buildings = response.data.data; // axios data, then laravel api resource data
            } else {
              flash('Error fetching buildings', 'danger');
              console.log('fetchBuildings', response.data);
              console.log('fetchBuildings', response.status);
              console.log('fetchBuildings', response.statusText);
            }

            this.loading(false);
          })
          .catch((error) => {
            flash('Error fetching Buildings', 'danger');
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
              // else if HTTP_CONFLICT to many bookings or over lap
              // else if HTTP_FORBIDDEN on enough permissions
              console.log('fetchBuildings: Response error', error.response.data, error.response.status, error.response.headers);

            } else if (error.request) {
              // The request was made but no response was received
              // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
              // http.ClientRequest in node.js
              console.error('fetchBuildings: Request error', error.request);
            } else {
              // Something happened in setting up the request that triggered an Error
              console.error('fetchBuildings: Error', error.message);
            }

            this.loading(false);
          });
      },

      showAccessModal(building) {
        this.editingId = building;
        this.editingName = building.name;
        this.editingAccessState = building.accessState;
        this.editingAccessStateString = building.accessStateString;
        this.editingSelfBookMaxOccupancy = building.selfBookMaxOccupancy;

        $(this.$refs.accessModal).modal('show');
      },

      showOccupancyModal(building) {
        this.editingId = building.id;
        this.editingName = building.name;
        this.editingAccessState = building.accessState;
        this.editingAccessStateString = building.accessStateString;
        this.editingSelfBookMaxOccupancy = building.selfBookMaxOccupancy;

        $(this.$refs.occupancyModal).modal('show');
      },

      updateOccupancy(event) {
        console.log('updateOccupancy', event);
        this.loading(true, true);

        axios.patch(this.route('api.gatekeeper.buildings.update-occupancy', this.editingId)
, {
            selfBookMaxOccupancy: this.editingSelfBookMaxOccupancy
          })
          .then((response) => {
            if (response.status == '200') { // HTTP_OK
              // response.data is the BuildingResource
              let updatedBuilding = response.data.data; // axios data, then laravel api resource data

              Object.assign(this.buildings[this.buildings.findIndex(buidling => buidling.id === updatedBuilding.id)], updatedBuilding);

              $(this.$refs.occupancyModal).modal('hide');

              flash('Max occupancy updated');
              console.log('updateOccupancy', 'Max occupancy updated OK');
            } else {
              flash('Error updating max occupancy', 'danger');
              console.log('updateOccupancy', response.data);
              console.log('updateOccupancy', response.status);
              console.log('updateOccupancy', response.statusText);
            }
          })
          .catch((error) => {
            flash('Error updating max occupancy', 'danger');
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
              // else if HTTP_CONFLICT to many bookings or over lap
              // else if HTTP_FORBIDDEN on enough permissions
              console.log('updateOccupancy: Response error', error.response.data, error.response.status, error.response.headers);
            } else if (error.request) {
              // The request was made but no response was received
              // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
              // http.ClientRequest in node.js
              console.error('updateOccupancy: Request error', error.request);
            } else {
              // Something happened in setting up the request that triggered an Error
              console.error('updateOccupancy: Error', error.message);
            }
          })
          .then(() => {
            this.loading(false);
          });
      },

      changeAccessTo(newAccessState) {
        // console.log('changeAccessTo', newAccessState);

        this.loading(true, true);

        axios.patch(this.route('api.gatekeeper.buildings.update-access-state', this.editingId)
, {
            accessState: newAccessState
          })
          .then((response) => {
            if (response.status == '200') { // HTTP_OK
              // response.data is the BuildingResource
              let updatedBuilding = response.data.data; // axios data, then laravel api resource data
              Object.assign(this.buildings[this.buildings.findIndex(buidling => buidling.id === updatedBuilding.id)], updatedBuilding);

              $(this.$refs.accessModal).modal('hide');

              flash('Access state updated');
              console.log('changeAccessTo', 'Max occupancy updated OK');
            } else {
              flash('Error updating access state', 'danger');
              console.log('changeAccessTo', response.data);
              console.log('changeAccessTo', response.status);
              console.log('changeAccessTo', response.statusText);
            }
          })
          .catch((error) => {
            flash('Error updating access state', 'danger');
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
              // else if HTTP_CONFLICT to many bookings or over lap
              // else if HTTP_FORBIDDEN on enough permissions
              console.log('changeAccessTo: Response error', error.response.data, error.response.status, error.response.headers);
            } else if (error.request) {
              // The request was made but no response was received
              // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
              // http.ClientRequest in node.js
              console.error('changeAccessTo: Request error', error.request);
            } else {
              // Something happened in setting up the request that triggered an Error
              console.error('changeAccessTo: Error', error.message);
            }
          })
          .then(() => {
            this.loading(false);
          });

      },

      modalHidden() {
        this.editingId = '';
        this.editingName = '';
        this.editingAccessState = '';
        this.editingAccessStateString = '';
        this.editingSelfBookMaxOccupancy = '';
        this.loading(false);
      },
    }, // end of methods

    mounted() {
      this.$nextTick(function() {
        // remove unwanted element
        $('.vue-remove').contents().unwrap();

        this.fetchBuildings();

        // setup confirmations for accessModal
        $('[data-toggle=change-access]').confirmation({
          // container: 'body',
          rootSelector: '[data-toggle=change-access]',
          popout: true,
          singleton: true,
          btnOkClass: 'btn btn-sm btn-danger'
        }).on('click', (event) => {
          this.changeAccessTo(event.target.dataset.accessState);
        });
      });

      // setup modals
      $(this.$refs.accessModal).modal('handleUpdate');
      $(this.$refs.accessModal).on('hidden.bs.modal', this.modalHidden);
      $(this.$refs.occupancyModal).modal('handleUpdate');
      $(this.$refs.occupancyModal).on('hidden.bs.modal', this.modalHidden);
    },
  }
</script>
