<template>
  <div class="vue-remove">
    <div class="container">
      <!-- <p>To schedule temporary access for a User please select and drag on the calendar below.</p>
      <p>To re-schedule a booking, click and move the booking to a new time.</p>
      <p>To cancel a booking, click on the booking then confirm cancellation.</p> -->
      <p>Words about the current access state being <strong>{{ building.accessStateString }}</strong></p>
      <p>The maximum concurrent occupancy is currently {{ building.selfBookMaxOccupancy }}</p>
      <p>This building has the following bookable areas.</p>
      <p class="h4">
        <template v-for="bookableArea in bookableAreas" >
          <span :class="['badge', 'badge-' + bookableArea.bookingColor, 'pb-1']">{{ bookableArea.name }}</span>&nbsp;
        </template>
      </p>
      <p v-if="settings.grant != 'ALL'">Some areas can only be booked by Trustees.</p>
    </div>

    <div class="container" ref="calendar">
      <full-calendar
        ref="fullCalendar"

        :plugins="calendarPlugins"
        locale="en-gb"
        timeZone="Europe/London"
        :firstDay=1
        :eventSources="eventSources"
        :rerenderDelay="5"

        @loading="loading"
        @select="select"
        @unselect="unselect"
        :selectAllow="selectAllow"
        @eventClick="eventClick"
        :eventAllow="eventAllow"
        @eventDragStart="removePopoverConfirmation"
        @eventDrop="eventDrop"
        @eventResizeStart="removePopoverConfirmation"
        @eventResize="eventResize"
        :datesDestroy="removePopoverConfirmation"
        :eventRender="eventRender"

        :selectOverlap=true
        :selectable="selectable"
        :selectMirror=true
        unselectCancel=".modal-content"
        :eventOverlap="eventOverlap"
        :defaultView="defaultView"
        noEventsMessage="No bookings to display"
        themeSystem="bootstrap"
        :header="{
          left:   'prev',
          center: 'today',
          right:  'next',
        }"
        :footer="{
          left:   'prev',
          center: 'today',
          right:  'next',
        }"
        :buttonText="{
          today:  'Today',
        }"
        :aspectRatio="aspectRatio"
        :views="{
          timeGrid: {
            // options apply to timeGridWeek and timeGridDay views
            allDaySlot: false,
            nowIndicator: true,
            slotDuration: '00:15',
            slotLabelInterval: '01:00',
            slotLabelFormat: {
              hour12: false,
              hour: '2-digit',
              minute: '2-digit',
            },
            // scrollTime: moment().format('HH:mm'),
            columnHeaderFormat: {
              weekday: 'narrow',
              day: '2-digit',
              month: '2-digit',
              year: '2-digit',
            },
            eventTimeFormat: {
              hour12: false,
              hour: '2-digit',
              minute: '2-digit',
            },
          },
        }"
        />
    </div>

    <!-- Booking Modal -->
    <div ref="bookingModal" class="modal fade" id="addBookingModal" tabindex="-1" role="dialog" aria-labelledby="addBookingLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addBookingLabel">{{ modalTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>{{ settings.bookingInfoText }}</p>
            <div :class="['form-group', buildingError ? 'is-invalid' : '']">
              <label for="building">Building</label>
              <div class="form-control border-0" id="building" >
                {{ building.name }}
                <small v-if="settings.grant == 'ALL'" class="text-muted"> ({{ building.accessStateString }})</small>
              </div>
              <div class="invalid-feedback d-block" role="alert" v-if="buildingError">{{ buildingError }}</div>
            </div>
            <div :class="['form-group', userError ? 'is-invalid' : '']" v-if="settings.grant == 'ALL'">
              <label for="user">Select Member</label>
              <member-select-two
                id="user"
                ref="mst"
                v-model="userId"
                :name="null"
                :current-only="true"
                />
              <div class="invalid-feedback d-block" role="alert" v-if="userError">{{ userError }}</div>
            </div>
            <div :class="['form-group', bookableAreaError ? 'is-invalid' : '']" ref="bookableAreaSelectDiv">
              <label for="user">Select Area</label>
              <select-two
                ref="bast"
                v-model="bookableAreaId"
                :name="null"
                placeholder="Select an area to book"
                :settings="bookableAreaSettings"
                :options="bookableAreaOptions"
                style="width: 100%"

                />
              <div class="invalid-feedback d-block" role="alert" v-if="bookableAreaError">{{ bookableAreaError }}</div>
              <small class="form-text text-muted" v-if="settings.grant != 'ALL'">Select the area of the space you will be occupying.</small>
              <small class="form-text text-muted" v-else>Select the area of the space this member will be occupying.</small>
            </div>
            <div class="form-group" v-if="building.accessState == 'REQUESTED_BOOK' || settings.grant == 'ALL'">
              <label for="notes">{{ (settings.grant == 'ALL') ? 'Notes' : 'Reason for booking' }}</label>
              <input
                type="text"
                v-model="notes"
                class="form-control"
                id="notes"
                maxlength=250
                aria-describedby="notesHelpBlock"
                :required="settings.grant != 'ALL'"
                >
              <div class="invalid-feedback d-block" role="alert" v-if="notesError">{{ notesError }}</div>
              <small id="notesHelpBlock" class="form-text text-muted" v-if="settings.grant != 'ALL' && building.accessState == 'REQUESTED_BOOK'">
                Please give a short reason for your use of the space to help the Trustees review this request.
              </small>
            </div>
            <div class="form-group">
              <label for="datetimepickerstart">Start</label>
              <div class="input-group" ref="datetimepickerstart">
                <date-picker
                  v-model="start"
                  id="datetimepickerstart"
                  :wrap="true"
                  :config="datapickerConfig"
                  @dp-change="startChange"
                  :class="['datetimepicker-readonly', startError ? 'is-invalid' : '']"
                  readonly
                  ></date-picker>
                <div class="input-group-append">
                  <div class="input-group-text rounded-right datepickerbutton"><i class="far fa-calendar-alt"></i></div>
                </div>
              </div>
              <div class="invalid-feedback d-block" role="alert" v-if="startError">{{ startError }}</div>
              <small class="form-text text-muted">Adjust the booking time if needed.</small>
            </div>
            <div class="form-group">
              <label for="datetimepickerend">End</label>
              <div class="input-group" ref="datetimepickerend">
                <date-picker
                  v-model="end"
                  id="datetimepickerend"
                  :wrap="true"
                  :config="datapickerConfig"
                  @dp-change="endChange"
                  :class="['datetimepicker-readonly', endError ? 'is-invalid' : '']"
                  readonly
                  ></date-picker>
                <div class="input-group-append">
                  <div class="input-group-text rounded-right datepickerbutton"><i class="far fa-calendar-alt"></i></div>
                </div>
              </div>
              <div class="invalid-feedback d-block" role="alert" v-if="endError">{{ endError }}</div>
              <small class="form-text text-muted">Adjust the booking time if needed.</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="sumbit" class="btn btn-primary" @click="book">{{ bookingButtonText }}</button>
          </div>
        </div>
      </div>
    </div> <!-- /Booking Modal -->
  </div>
</template>

<script>
  import FullCalendar from '@fullcalendar/vue';
  import timeGridPlugin from '@fullcalendar/timegrid';
  import interactionPlugin from '@fullcalendar/interaction';
  import momentPlugin from '@fullcalendar/moment';
  import momentTimezonePlugin from '@fullcalendar/moment-timezone';
  import bootstrapPlugin from '@fullcalendar/bootstrap';
  import moment from 'moment';
  import humanizeDuration from 'humanize-duration';
  import Loading from 'vue-loading-overlay';
  import datePicker from 'vue-bootstrap-datetimepicker';
  Vue.use(Loading);

  export default {
    components: {
      FullCalendar, // make the <FullCalendar> tag available
      datePicker,
    },

    props: {
      building: Object,
      bookableAreas: Array,
      settings: {
        type: Object,
        default: () => ({
          maxLength: 120,
          maxConcurrentPerUser: 1,
          maxGuestsPerUser: 0,
          minPeriodBetweenBookings: 720,
          bookingInfoText: '',
          userId: null,
          grant: 'NONE',
        }),
      },
    },

    data() {
      return {
        axiosCancle: null,
        calendarApi: null,
        calendarPlugins: [
          timeGridPlugin,
          interactionPlugin,
          momentPlugin,
          momentTimezonePlugin,
          bootstrapPlugin,
        ],
        defaultView: 'timeGridDay',
        dayAspectRatio: 0.8,
        weekAspectRatio: 1.35, // full calender default
        aspectRatio: this.dayAspectRatio,
        interval: null,
        isLoading: true,
        loader: null,
        userId: '',
        bookableAreaId: '',
        notes: '',
        start: '',
        end: '',
        buildingError: false,
        userError: false,
        bookableAreaError: false,
        notesError: false,
        startError: false,
        endError: false,
        datapickerConfig: {
          icons: {
              time: 'far fa-clock',
              date: 'far fa-calendar-alt',
              up: 'fas fa-arrow-up',
              down: 'fas fa-arrow-down',
              previous: 'fas fa-chevron-left',
              next: 'fas fa-chevron-right',
              today: 'far fa-calendar-check',
              clear: 'far fa-trash',
              close: 'far fa-times'
          },
          locale: 'en-gb',
          format: 'YYYY-MM-DD HH:mm',
          ignoreReadonly: true,
          allowInputToggle: true,
          useCurrent: false,
          stepping: 15,
          minDate: moment().add(15, 'minutes'), // can not pick in the past
        },
      };
    },

    computed: {
      eventSources() {
        return [
          {
            events: this.fetchBookings,
            id: 'bookings',
          },
          {
            events: [
              {
                start: moment().startOf('day').toDate(),
                end: moment().toDate(),
                rendering: 'background'
              },
            ],
            id: 'pastEvent',
            editable: false,
            overlap: true,
          },
        ];
      },

      selectable() {
        if (this.settings.grant == "SELF" || this.settings.grant == 'ALL') {
          return true;
        } else {
          // settings.grant == NONE
          return false;
        }
      },

      selectedBookableArea() {
        return this.bookableAreas.find(bookableArea => bookableArea.id == this.bookableAreaId);
      },

      bookableAreaOptions() {
        if (this.settings.grant == 'ALL') {
          return this.bookableAreas;
        } else {
          // need to filter for only areas with selfBookable true
          return this.bookableAreas.filter(bookableArea => bookableArea.selfBookable);
        }
      },

      bookableAreaSettings() {
        const self = this;
        return {
          width: '100%',
          dropdownParent: this.$refs.bookableAreaSelectDiv,
          templateResult: this.formatBookableArea,
          templateSelection: this.formatBookableAreaSelection,
        };
      },

      bookingButtonText() {
        if (this.settings.grant != 'ALL' && this.building.accessState == 'REQUESTED_BOOK') {
          return 'Request Booking';
        } else {
          return 'Add Booking';
        }
      },

      modalTitle() {
        if (this.settings.grant == 'ALL') {
          return 'Schedule a booking for a member';
        } else if (this.building.accessState == 'REQUESTED_BOOK') {
          return 'Request a booking';
        } else {
          return 'Schedule a booking';
        }
      },
    },

    watch: {
      userId(newValue, oldValue) {
        // clear error on any change
        this.startError = false;
        this.endError = false;
        this.userError = this.checkClashByUser();
      },

      bookableAreaId(newValue, oldValue) {
        let start = moment(this.start);
        let end = moment(this.end);
        this.bookableAreaError = this.checkClashByBookalbeArea(start, end, newValue);
      },

      notes(newValue, oldValue) {
        this.notesError = false;
      },
    },

    methods: {
      loading(isLoading) {
        this.isLoading = isLoading;
        if (isLoading && this.loader == null) {
          this.loader = this.$loading.show({
            container: this.$refs.calendar,
            color: '#195905',
          });
        } else if (this.loader !== null) {
          this.loader.hide();
          this.loader = null;
        }
      },

      select(selectionInfo) {
        this.setupBookingConfirmation(selectionInfo);
      },

      unselect(jsEvent, view) {
        this.removePopoverConfirmation();
      },

      selectAllow(selectInfo) {
        if (this.isLoading) {

          return false;
        }

        // Don't allow selection if start is in the past
        if (moment().add(10, 'minutes').diff(selectInfo.start) > 0) {

          return false;
        }

        if (this.settings.grant == "SELF") {
          // need to check maxConcurrentPerUser against userCurrentCountByBuildingId
          if (this.settings.userCurrentCountByBuildingId[this.building.id] >= this.settings.maxConcurrentPerUser) {
            flash('You can only have ' + this.settings.maxConcurrentPerUser + ' concurrent Bookings', 'warning');

            return false;
          }
        }

        // need to check for overlap with other events for this.building.selfBookMaxOccupancy
        // can not yet check area limits as it has not been defined yet
        let start = moment(selectInfo.start);
        let end = moment(selectInfo.end);

        let buildingError = this.checkBuildingLimits(start, end);
        if (buildingError) {
          flash(buildingError, 'warning');
          if (this.settings.grant == 'ALL') {
            return false;
          }
        }

        // check bookable area limits?
        // nope we have not selected an area yet

        // Check length against max booking length
        var duration = moment.duration(moment(selectInfo.end).diff(selectInfo.start));
        if (duration.asMinutes() > this.settings.maxLength) {
          flash('Max booking length is '+ humanizeDuration(this.settings.maxLength * 60000), 'warning');
          if (this.settings.grant != 'ALL') {
            return false;
          }
        }

        return true;
      },

      eventClick(info) {
        if (this.isLoading) {

          return false;
        }

        if (this.settings.grant == "NONE") {
          return false;
        }

        if (this.settings.userId == null) {
          console.error('eventClick: settings.userId not set');

          return false;
        }

        // (is it ours or do we have grant ALL) and does it end in the future?
        if ((info.event.extendedProps.userId == this.settings.userId || this.settings.grant == 'ALL') && moment().diff(info.event.end) < 0) {
          this.setupCancleConfirmation(info);
        }
      },

      eventOverlap(stillEvent, movingEvent) {
        // console.log('eventOverlap', stillEvent, movingEvent);

        // as we have two events rather than call  checkClashByUser we can do a quick check
        if (stillEvent.extendedProps.userId == movingEvent.extendedProps.userId) {
          return false;
        }

        return true;
      },

      eventAllow(dropInfo, draggedEvent) {
        console.log('eventAllow'); //, dropInfo, draggedEvent);
        if (this.isLoading) {

          return false;
        }

        if (moment().diff(draggedEvent.start) > 0) {
          // booking start was already in the past, we only allow resize
          if (draggedEvent.start.getTime() != dropInfo.start.getTime()) {
            // you cannot move the start
            flash('Bookings start cannot be changed', 'warning');

            return false;
          } else {
            // this event has been resized
            // check the end is not in the past now
            if (moment().diff(dropInfo.end) > 0) {
              flash('Booking end cannot be in the past', 'warning');

              return false;
            }
            // end is still in the future, fall through to length check
          }
        } else if (moment().diff(dropInfo.start) > 0) {
          //check it has not been dropped into the past
          flash('Bookings cannot be moved into the past', 'warning');

          return false;
        }

        // check building limits
        let start = moment(dropInfo.start);
        let end = moment(dropInfo.end);

        let buildingError = this.checkBuildingLimits(start.clone(), end.clone(), draggedEvent.id)
        if (buildingError) {
          flash(buildingError, 'warning');
          if (this.settings.grant != 'ALL') {
            return false;
          }
        }

        // check bookable area limits
        let bookableAreaError = this.checkClashByBookalbeArea(start.clone(), end.clone(), draggedEvent.extendedProps.bookableArea.id, draggedEvent.id)
        if (bookableAreaError) {
          flash(bookableAreaError, 'warning');
          if (this.settings.grant != 'ALL') {
            return false
          }
        }

        // check new duration except on if you have grant ALL
        var duration = moment.duration(moment(dropInfo.end).diff(dropInfo.start));
        if (duration.asMinutes() > this.settings.maxLength) {
          flash('Max booking length is '+ humanizeDuration(this.settings.maxLength * 60000), 'warning');
          if (this.settings.grant != 'ALL') {
            return false;
          }
        }

        return true;
      },

      eventDrop(eventDropInfo) {
        // patch the bookings start and end time
        this.patchBooking(eventDropInfo.event, eventDropInfo.revert)
      },

      eventResize(eventResizeInfo) {
        // patch the bookings end time
        this.patchBooking(eventResizeInfo.event, eventResizeInfo.revert)
      },

      eventRender: function (info) {
        if (info.event.extendedProps.notes) {
          $(info.el).tooltip({ title: info.event.extendedProps.notes });
        }
      },

      /**
       * FullCalendar will call this function whenever it needs new event data.
       * This is triggered when the user clicks prev/next or switches views.
       */
      fetchBookings(fetchInfo, successCallback, failureCallback) {
        // TODO: look at caching bookings on the Vue and only do axios call when we don't have the data in the Vue cache
        const self = this;
        const CancelToken = axios.CancelToken;
        if (this.axiosCancle !== null) {
          this.axiosCancle('New events range requested');
        }

        const request = axios.get(this.route('api.gatekeeper.temporary-access-bookings.index'), {
          params: {
            start: fetchInfo.startStr,
            end: fetchInfo.endStr,
            building_id: this.building.id,
          },
          cancelToken: new CancelToken((c) => {
            self.axiosCancle = c;
          }),
        });

        request.then(({ data }) => {
          // need to map over our api response first to prep them for fullcalender
          // pass bookings over to fullcalenders callback
          successCallback(data.data.map(this.mapBookings));
        })
        .catch((thrown) => {
          if (axios.isCancel(thrown)) {
            // console.log('fetchBookings: Request cancelled', thrown.message);
          } else {
            // handle error
            console.log('fetchBookings: Request error', thrown);
            flash('Error fetching bookings', 'danger');
            failureCallback(thrown);
          }
        });
      },

      /**
       * Handle click of Add Booking in modal
       */
      book(event) {
        // console.log('book', event);
        let start = moment(this.start);
        let end = moment(this.end);

        // Validation checks
        let blockAlways = false;
        // is the start date in the future?
        // is the end date after the start date
        // is the duration less than settings.maxLength
        // all of the above should already be taken care of by check in startChange and endChange

        // buildingError
        // (set on time change) allow ALL override

        // userError
        // Has a user been selected?
        if (this.userId == '') {
          this.userError = 'You must select a member.';
          // always block
          blockAlways |= true;
        } else if (this.userError) {
          // (checked on change) always block
          blockAlways |= true;
        }

        // bookableAreaError
        if (this.bookableAreaId == '' ) {
          this.bookableAreaError = 'You must select an area.';
          // always block
          blockAlways |= true;
        }
        // else
        // (checked on change) allow ALL override

        // notesError
        if (this.notes == '') {
          if (this.settings.grant != 'ALL' && this.building.accessState == 'REQUESTED_BOOK') {
            this.notesError = 'You must give a reason';
            blockAlways |= true;
          }
        }

        // startError && endError
        // (set on change) always block
        if (this.startError || this.endError) {
          blockAlways |= true;
        }

        if (blockAlways) { // also covers user, start, end
          return;
        }

        if (this.buildingError || this.bookableAreaError) {
          if (this.settings.grant == 'ALL') {
            // grant ALL can override
            flash('Making this Booking will override some limits', 'warning');
          } else {
            // but block for others
            return;
          }
        }

        let booking = {
          start: start.toISOString(true),
          end: end.toISOString(true),
          user_id: this.userId,
          bookable_area_id: this.bookableAreaId,
          notes: this.notes,
        };

        this.createBooking(booking);
      },

      createBooking(booking) {
        console.log('createBooking', booking);
        this.loading(true);
        axios.post(this.route('api.gatekeeper.temporary-access-bookings.store'), booking)
          .then((response) => {
            if (response.status == '201') { // HTTP_CREATED
              const responseBooking = this.mapBookings(response.data.data);

              $(this.$refs.bookingModal).modal('hide');
              this.removeBookingConfirmation();
              this.calendarApi.unselect();
              const event = this.calendarApi.getEventById(responseBooking.id);
              if (! event) { // make sure the event has not already been added via newBookingEvent
                // TODO: update userCurrentCountByBuildingId
                if (responseBooking.userId == this.settings.userId) {
                  this.settings.userCurrentCountByBuildingId[this.building.id] += 1;
                }

                this.calendarApi.addEvent(responseBooking, 'bookings');
              }

              flash('Booking created');
            } else {
              flash('Error creating booking', 'danger');
              console.log('createBooking', response.data);
              console.log('createBooking', response.status);
              console.log('createBooking', response.statusText);
            }
            this.loading(false);
          })
          .catch((error) => {
            flash('Error creating booking', 'danger');
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
              // else if HTTP_CONFLICT to many bookings or over lap
              this.calendarApi.refetchEvents();  // has some one else booked this slot we should refect to see if they have
              // else if HTTP_FORBIDDEN on enough permissions
              console.log('createBooking: Response error', error.response.data, error.response.status, error.response.headers);

            } else if (error.request) {
              // The request was made but no response was received
              // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
              // http.ClientRequest in node.js
              console.error('createBooking: Request error', error.request);
            } else {
              // Something happened in setting up the request that triggered an Error
              console.error('createBooking: Error', error.message);
            }

            this.loading(false);
          });
      },

      patchBooking(event, revert) {
        let booking = {
          start: moment(event.start).toISOString(true),
          end: moment(event.end).toISOString(true),
        };

        this.loading(true);

        axios.patch(this.route('api.gatekeeper.temporary-access-bookings.update', event.id), booking)
          .then((response) => {
            if (response.status == '200') { // HTTP_OK
              flash('Booking updated');
              console.log('patchBooking', 'Booking Updated OK');
              // think patch does not need anything doing to confirm
            } else {
              flash('Error updating booking', 'danger');
              revert();
              console.log('patchBooking', response.data);
              console.log('patchBooking', response.status);
              console.log('patchBooking', response.statusText);
            }

            this.loading(false);
          })
          .catch((error) => {
            flash('Error updating booking', 'danger');
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
              // else if HTTP_CONFLICT to many bookings or over lap
              this.calendarApi.refetchEvents();  // has some one else booked this slot we
              // else if HTTP_FORBIDDEN on enough permissions
              console.log('patchBooking: Response error', error.response.data, error.response.status, error.response.headers);
            } else if (error.request) {
              // The request was made but no response was received
              // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
              // http.ClientRequest in node.js
              console.error('patchBooking: Request error', error.request);
            } else {
              // Something happened in setting up the request that triggered an Error
              console.error('patchBooking: Error', error.message);
            }

            revert();
            this.loading(false);
          });
      },

      cancelBooking(event) {
        console.log('cancelBooking', event);

        this.loading(true);

        axios.delete(this.route('api.gatekeeper.temporary-access-bookings.destroy', event.id))
          .then((response) => {
            if (response.status == '204') { // HTTP_NO_CONTENT
              flash('Booking cancelled');
              console.log('cancelBooking', 'Booking deleted');

              const foundEvent = this.calendarApi.getEventById(event.id);
              if (foundEvent) { // make sure the event has not already been removed via bookingCancelledEvent
                // update userCurrentCountByBuildingId
                if (event.extendedProps.userId == this.settings.userId) {
                  this.settings.userCurrentCountByBuildingId[this.building.id] -= 1;
                }
                event.remove();
              }
            } else {
              flash('Error cancelling booking', 'danger');
              console.log('cancelBooking', response.data);
              console.log('cancelBooking', response.status);
              console.log('cancelBooking', response.statusText);
            }

            this.loading(false);
          })
          .catch((error) => {
            flash('Error cancelling booking', 'danger');
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
              // else if HTTP_CONFLICT to many bookings or over lap
              // else if HTTP_FORBIDDEN on enough permissions
              console.log('cancelBooking: Response error', error.response.data, error.response.status, error.response.headers);
            } else if (error.request) {
              // The request was made but no response was received
              // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
              // http.ClientRequest in node.js
              console.error('cancelBooking: Request error', error.request);
            } else {
              // Something happened in setting up the request that triggered an Error
              console.error('cancelBooking: Error', error.message);
            }

            this.loading(false);
          });
      },

      /**
       * Display bootstrap confirmation popover for a new selection.
       */
      setupBookingConfirmation(selectionInfo) {
        // update start and end base on the new selection
        this.start = selectionInfo.start; //moment(selectionInfo.start).format('YYYY/MM/DD HH:mm');
        this.end = selectionInfo.end; //moment(selectionInfo.end).format('YYYY/MM/DD HH:mm');

        // and show the modal
        $(this.$refs.bookingModal).modal('show');
      },

      removeBookingConfirmation() {
        this.$refs.mst && this.$refs.mst.clear();
        if (this.settings.grant == 'ALL') {
          this.userId = '';
        } else {
          this.userId = this.settings.userId;
        }
        this.bookableAreaId = '';
        this.notes = '';
        this.userError = false;
        this.startError = false;
        this.endError = false;
        this.bookableAreaError = false;

        this.calendarApi.unselect();
      },

      /**
       * Display bootstrap confirmation popover for a new selection.
       */
      setupCancleConfirmation(info) {
        const self = this;

        // info.el attach booking-selected class to the <a>
        $(info.el).addClass('booking-selected');

        let options = {
          container: 'body',
          rootSelector: '.booking-selected',
          title: 'Would you like to cancel this booking?',
          popout: true,
          singleton: true,
          btnOkClass: 'btn btn-sm btn-danger',
          btnCancelClass: 'btn btn-sm btn-outline-dark',
          onConfirm(type) {
            $(info.el).removeClass('booking-selected');
            self.cancelBooking(info.event);
          },
          onCancel() {
            $(info.el).removeClass('booking-selected');
          },
        };

        $('.booking-selected').confirmation(options);

        $('.booking-selected').confirmation('show');
      },

      /**
       * Remove the previous bootstrap confirmation popover.
       */
      removePopoverConfirmation() {
        $('.popover').remove();
      },

      /**
       * Attached to 'resize' event so we can make the view responsive.
       */
      getWindowResize(event) {
        const windowWidth = document.documentElement.clientWidth;

        if (windowWidth < 767.98) {
          this.defaultView = 'timeGridDay';
          this.aspectRatio = this.dayAspectRatio;
        } else {
          this.defaultView = 'timeGridWeek';
          this.aspectRatio = this.weekAspectRatio;
        }

        if (this.calendarApi !== null) {
          this.calendarApi.changeView(this.defaultView);
          this.removePopoverConfirmation();
        }
      },

      mapBookings(booking) {
        if (booking.bookableArea) {
          booking.className = 'temporary-booking-' + booking.bookableArea.bookingColor.toLowerCase();
        } else if (booking.color) {
          booking.className = 'temporary-booking-' + booking.color.toLowerCase();
        } else {
          booking.className = 'temporary-booking-primary';
        }

        if (! booking.approved) {
          booking.className += ' not-approved';
        }

        if (this.settings.grant != 'NONE'
          && (booking.userId == this.settings.userId || this.settings.grant == 'ALL')
          && moment().diff(booking.start) > 0
          && moment().diff(booking.end) < 0) {
            // this is a booking under now
          booking.durationEditable = true;
        } else if (this.settings.grant != 'NONE'
          && (booking.userId == this.settings.userId || this.settings.grant == 'ALL')
          && moment().diff(booking.start) < 0) {
          booking.editable = true;
        } else {
          booking.className += ' not-editable';
        }

        return booking;
      },

      echoInit() {
        Echo.channel('gatekeeper.temporaryAccessBookings')
          .listen('Gatekeeper.NewBooking', this.newBookingEvent)
          .listen('Gatekeeper.BookingChanged', this.bookingChangedEvent)
          .listen('Gatekeeper.BookingCancelled', this.bookingCancelledEvent);
      },

      echoDeInit() {
        Echo.leave('gatekeeper.temporaryAccessBookings');
      },

      newBookingEvent(newBooking) {
        // console.log('Echo sent newBooking event', newBooking);
        const booking = this.mapBookings(newBooking.booking);
        const event = this.calendarApi.getEventById(booking.id);
        if (event) { // make sure the event has not already been added via createBooking
          return;
        }

        // update userCurrentCountByBuildingId
        if (booking.userId == this.settings.userId) {
          this.settings.userCurrentCountByBuildingId[this.building.id] += 1;
        }

        this.calendarApi.addEvent(booking, 'bookings');
      },

      bookingChangedEvent(bookingChanged) {
        // console.log('Echo sent bookingChanged event', bookingChanged);
        const oldEvet = this.calendarApi.getEventById(bookingChanged.orignalBooking.id);
        if (oldEvet) {
          oldEvet.remove();
        }

        const booking = this.mapBookings(bookingChanged.booking);
        this.calendarApi.addEvent(booking, 'bookings');
      },

      bookingCancelledEvent(bookingCancelled) {
        // console.log('Echo sent bookingCancelled event', bookingCancelled);
        const event = this.calendarApi.getEventById(bookingCancelled.bookingId);
        if (event) {
          // update userCurrentCountByBuildingId
          if (event.extendedProps.userId == this.settings.userId) {
            this.settings.userCurrentCountByBuildingId[this.building.id] -= 1;
          }

          event.remove();
        }
      },

      /**
       * a user cannot overlap there own event
       */
      checkClashByUser() {
        // console.log('checkClashByUser');
        let start = moment(this.start);
        let end = moment(this.end);

        if (this.userId == '') {
          return false;
        } else {
          // does this event overlap an existing event for this user?
          let events = this.calendarApi.getEvents();
          events = events.filter(event => event.extendedProps.userId == this.userId);
          events = events.filter((event) => {
            let eventStart = moment(event.start);
            let eventEnd = moment(event.end);
            return (eventStart.isSameOrBefore(start) && eventEnd.isAfter(start)) ||
              (eventStart.isBefore(end) && eventEnd.isSameOrAfter(end)) ||
              (eventStart.isAfter(start) && eventStart.isBefore(end));
          });

          if (events.length > 0 ) {
            if (this.settings.grant == 'ALL') {
              return 'Booking clash for this member.';
            } else {
              return 'You already have a booking in this period.'
            }
          } else {
            return false;
          }
        }
      },

      /**
       * check the Building selfBookMaxOccupancy for this time period
       */
      checkBuildingLimits(initialStart, initialEnd, ignoreEventId = null) {
        let start = moment(initialStart);
        let end = moment(initialEnd);

        console.log('checkBuildingLimits', start.toISOString(), end.toISOString(), ignoreEventId)
        let fiftenMinutes = moment.duration(15, 'minutes');

        let events = this.calendarApi.getEvents(); // all the current events in the calender
        if (ignoreEventId) {
          events = events.filter(event => event.id != ignoreEventId);
          // console.log('checkBuildingLimits:ignoreEventId', events);
        }

        let result = false;
        // to work through the events by 15 minute slots
        // we will inc start by fiftenMinutes each loop
        do {
          // we need current start + 15 for the filter
          let filterEnd = start.clone().add(fiftenMinutes);

          let filteredEvents = events.filter((event) => {
            let eventStart = moment(event.start);
            let eventEnd = moment(event.end);
            return (eventStart.isSameOrBefore(start) && eventEnd.isAfter(start)) ||
              (eventStart.isBefore(filterEnd) && eventEnd.isSameOrAfter(filterEnd)) ||
              (eventStart.isAfter(start) && eventStart.isBefore(filterEnd));
          });


          // TODO: guests
          // check filteredEvents counts vs selfBookMaxOccupancy
          if (filteredEvents.length >= this.building.selfBookMaxOccupancy) {
            result =  'Maximum building concurrent occupancy limit is ' + this.building.selfBookMaxOccupancy + '.';
            break;
          }

          // adjust start for next loop
          start.add(fiftenMinutes)
        } while (start.isBefore(end));

        return result;
      },

      /**
       * if a booking area has been selected, we can check that overlap does not exceed the area limits
       */
      checkClashByBookalbeArea(initialStart, initialEnd, bookableAreaId, ignoreEventId = null) {
        let start = moment(initialStart);
        let end = moment(initialEnd);

        console.log('checkClashByBookalbeArea', start.toISOString(), end.toISOString(), bookableAreaId, ignoreEventId);
        // need to check for overlap with other events for this.bookableAreaId
        if (bookableAreaId == '') {
          // no area selected
          return false;
        }

        // we want to grab the bookableArea from our data array
        let selectedBookableArea = this.bookableAreas.find(bookableArea => bookableArea.id == bookableAreaId)

        let fiftenMinutes = moment.duration(15, 'minutes');

        let events = this.calendarApi.getEvents(); // all the current events in the calender
        // filter events by this bookable area
        events = events.filter(event => (event.extendedProps.bookableArea && event.extendedProps.bookableArea.id) == selectedBookableArea.id);
        if (ignoreEventId) {
          events = events.filter(event => event.id != ignoreEventId);
          // console.log('checkClashByBookalbeAre:ignoreEventId', events);
        }

        let result = false;
        // to work through the events by 15 minute slots
        // we will inc start by fiftenMinutes each loop
        // TODO: extract this and the other copy to a function that takes events and with callback for the check
        do {
          // we need current start + 15 for the filter
          let filterEnd = start.clone().add(fiftenMinutes);

          let filteredEvents = events.filter((event) => {
            let eventStart = moment(event.start);
            let eventEnd = moment(event.end);
            return (eventStart.isSameOrBefore(start) && eventEnd.isAfter(start)) ||
              (eventStart.isBefore(filterEnd) && eventEnd.isSameOrAfter(filterEnd)) ||
              (eventStart.isAfter(start) && eventStart.isBefore(filterEnd));
          });

          // TODO: guests
          // check filteredEvents counts vs maxOccupancy
          if (filteredEvents.length >= selectedBookableArea.maxOccupancy) {
            result =  'Area maximum concurrent occupancy limit is ' + selectedBookableArea.maxOccupancy + '.';
            break;
          }

          // adjust start for next loop
          start.add(fiftenMinutes)
        } while (start.isBefore(end));

        return result;
      },

      /**
       * Watch for changes of the start date picker.
       * TODO: might this be better as a watch on this.start?
       * Nope this is better as event gives us new and old dates as moment's
       */
      startChange(event) {
        // console.log('start dp.change', event);

        let start = moment(this.start);
        let end = moment(this.end);

        // TODO: check we are not overlapping one of our own events
        // reset start end if so?
        if (this.userError != 'You must select a member.') {
          this.userError = false;
        }
        this.startError = this.checkClashByUser();
        if (this.startError === false) {
          this.endError = false;
        }

        // check building and area limits
        this.buildingError = this.checkBuildingLimits(start, end)
        this.bookableAreaError = this.checkClashByBookalbeArea(start, end, this.bookableAreaId);

        // limit end based on settings.maxLength and new start
        let minEndDate = moment(event.date).add(15, 'minutes');
        let maxEndDate = moment(event.date).add(this.settings.maxLength, 'minutes');

        if ($(this.$refs.datetimepickerend).data('DateTimePicker').minDate().isAfter(minEndDate)) {
          $(this.$refs.datetimepickerend).data('DateTimePicker').minDate(minEndDate);
          $(this.$refs.datetimepickerend).data('DateTimePicker').maxDate(maxEndDate);
        } else {
          $(this.$refs.datetimepickerend).data('DateTimePicker').maxDate(maxEndDate);
          $(this.$refs.datetimepickerend).data('DateTimePicker').minDate(minEndDate);
        }

        let period = moment.duration(moment(this.end).diff(event.oldDate));
        let newEnd = event.date.clone().add(period);

        // check if end is now past maxDate and limit it?
        if (this.end && moment(this.end).isAfter(maxEndDate)) {
          // console.log('limiting end');
          if (event.date.isSameOrBefore(moment(event.oldDate).subtract(1, 'days'))) {
            this.endError = false;
            this.end = newEnd;
          } else {
            this.endError = false;
            this.end = maxEndDate;
          }
        }

        // check if end is now before start
        if (this.end && moment(this.end).isBefore(event.date)) {
          // console.log('end is before start now');
          // update end to same period after start?

          this.endError = false;
          this.end = newEnd;
        }

        // update the selection
        this.$nextTick(function () {
          // console.log('startChange: updating selection to ', this.start, this.end);
          this.calendarApi.select(this.start, this.end);
        });
      },

      /**
       * Watch for changes of the end date picker.
       * TODO: might this be better as a watch on this.end?
       * Nope this is better as event gives us new and old dates as moment's
       */
      endChange(event) {
        // console.log('end dp.change', event);

        let start = moment(this.start);
        let end = moment(this.end);
        if (this.userError != 'You must select a member.') {
          this.userError = false;
        }
        this.endError = this.checkClashByUser();
        if (this.endError === false) {
          this.startError = false;
        }

        // check building and area limits
        this.buildingError = this.checkBuildingLimits(start, end)
        this.bookableAreaError = this.checkClashByBookalbeArea(start, end, this.bookableAreaId);

        // check the end is later than the start
        if (event.date.isBefore(moment(this.start))) {
          // not neeed as startChange sets Min
        }

        // only update is end is after start?
        if (moment(this.end).isAfter(moment(this.start))) {
          // TODO: check we are not overlapping one of our own events
          // reset end end if so


          this.$nextTick(function () {
            // update the selection
            // console.log('endChange: updating selection to ', this.start, this.end);
            this.calendarApi.select(this.start, this.end);
          });
        }
      },

      formatBookableArea(bookableArea) {
        if (! bookableArea.id) {
          return bookableArea.text;
        }

        let markup = '<span class="badge badge-' + bookableArea.bookingColor + ' badge-font-inherit">' + bookableArea.name + '</span>';

        return $(markup);
      },

      formatBookableAreaSelection(bookableArea) {
        if (! bookableArea.id) {
          return bookableArea.text;
        }
        return this.formatBookableArea(bookableArea);
        return bookableArea.name;
      },
    }, // end of methods

    mounted() {
      this.$nextTick(function() {
        // remove unwanted element
        $('.vue-remove').contents().unwrap();

        window.addEventListener('resize', this.getWindowResize);

        //Init
        this.getWindowResize();
      });

      this.calendarApi = this.$refs.fullCalendar.getApi();

      // setup user
      if (this.settings.grant != 'ALL') {
        this.userId = this.settings.userId;
      }

      // workaround for wierd height issue ($nextTick is to soon)
      // might be related https://github.com/fullcalendar/fullcalendar/issues/4650
      setTimeout(() => {
        this.calendarApi.updateSize();

        let scrollTime = '06:00';
        if (moment().isAfter(moment('06:00', 'HH:mm'), 'hour')) {
          scrollTime = moment().format('HH') + ':00:00'
        }

        this.calendarApi.scrollToTime(scrollTime);
      }, 100);

      // Call refetchEventsevery 15 minutes, so past events are shaded
      this.interval = setInterval(function () {
        // TODO: once we have Echo running only really need to call this if there is an event under now ±15
        this.calendarApi.refetchEvents();
        this.removePopoverConfirmation();
        $(this.$refs.datetimepickerstart).data('DateTimePicker').minDate(moment().add(15, 'minutes'));
      }.bind(this), 900000);

      this.echoInit();

      $(this.$refs.bookingModal).modal('handleUpdate');
      // need to setup close event to unselected
      $(this.$refs.bookingModal).on('hidden.bs.modal', this.removeBookingConfirmation);
    },

    beforeDestroy() {
      clearInterval(this.interval);
      window.removeEventListener('resize', this.getWindowResize);
      this.echoDeInit();
    },
  }
</script>

<style lang="scss">
/**
 * TemporaryAccess
 */
@import "~sass/variables";
@import "~bootstrap/scss/functions";
@import "~bootstrap/scss/variables";
@import "~bootstrap/scss/mixins";

/*
 * Coloured bookings
 */
@each $color, $value in $theme-colors {
  .temporary-booking-#{$color} {
    border-color: $value;
    background-color: $value !important;
    &.not-approved {
      background: repeating-linear-gradient(
          $value,
          $value 10px,
          shade($value, 20%) 10px,
          shade($value, 20%) 20px
      );
    }
    &.not-editable {
      background: repeating-linear-gradient(
          -45deg,
          $value,
          $value 10px,
          tint($value, 10%) 10px,
          tint($value, 10%) 20px
      );
    }
  }
}

.badge-font-inherit {
  font-size: inherit
}

</style>
