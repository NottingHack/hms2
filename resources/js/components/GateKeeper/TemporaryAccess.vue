<template>
  <div class="vue-remove">
    <div class="container">
      <p>To schedule temporary access for a User please select and drag on the calendar below.</p>
      <p>To re-schedule a booking, click and move the booking to a new time.</p>
      <p>To cancel a booking, click on the booking then confirm cancellation.</p>
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

        :selectable=true
        :selectOverlap=true
        :selectMirror=true
        unselectCancel=".modal-content"
        :eventOverlap=true
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

    <!-- Modal -->
    <div ref="selectModal" class="modal fade" id="addBookingModal" tabindex="-1" role="dialog" aria-labelledby="addBookingLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addBookingLabel">Schedule booking</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div :class="['form-group', userError ? 'is-invalid' : '']">
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
            <div class="form-group">
              <div class="btn-group btn-group-toggle w-100">
                <label
                  v-for="c in colors"
                  :class="['btn', 'btn-' + c]"
                >
                  <input type="radio" v-model="color" :value="c"><i class="fas fa-check" v-show="color === c"></i>
                </label>
              </div>
            </div>
            <div class="form-group">
              <label for="notes">Notes</label>
              <input type="text" v-model="notes" class="form-control" id="notes" maxlength=250>
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
                  <div class="input-group-text datepickerbutton"><i class="far fa-calendar-alt"></i></div>
                </div>
              </div>
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
                  <div class="input-group-text datepickerbutton"><i class="far fa-calendar-alt"></i></div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="sumbit" class="btn btn-primary" @click="book">Add Booking</button>
          </div>
        </div>
      </div>
    </div> <!-- /Modal -->
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
      bookingLengthMax: Number,
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
        colors: [
          'primary',
          'red',
          'indigo',
          'yellow',
          'pink',
          'orange',
          'cyan',
        ],
        color: 'primary',
        notes: '',
        start: '',
        end: '',
        userError: '',
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
          minDate: moment().add(15, 'mintues'), // can not pick in the past
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
    },

    watch: {
      userId(newValue, oldValue) {
        // clear error on any change
        this.userError = '';
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

        // Check length against tools max booking length
        var duration = moment.duration(moment(selectInfo.end).diff(selectInfo.start));
        if (duration.asMinutes() > this.bookingLengthMax) {
          flash('Max booking length is '+ humanizeDuration(this.bookingLengthMax * 60000), 'warning');

          return false;
        }

        return true;
      },

      eventClick(info) {
        if (this.isLoading) {

          return false;
        }

        // does it end in the future?
        if (moment().diff(info.event.end) < 0) {
          this.setupCancleConfirmation(info);
        }
      },

      eventAllow(dropInfo, draggedEvent) {
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

        // check new duration except on Maintenance
        var duration = moment.duration(moment(dropInfo.end).diff(dropInfo.start));
        if (duration.asMinutes() > this.bookingLengthMax && draggedEvent.extendedProps.type != 'MAINTENANCE') {
          flash('Max booking length is '+ humanizeDuration(this.bookingLengthMax * 60000), 'warning');

          return false;
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
        let start = moment(this.start);
        let end = moment(this.end);

        // Validation checks

        // is the start date in the future?
        // is the end date after the start date
        // is the duration less than bookingLengthMax
        // all of the above should already be taken care of by check in startChange and endChange

        // Has a user been selected?
        if (this.userId == '') {
          this.userError = 'You must select a member.';
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
            this.userError = 'Booking clash for this member';
          } else {
            this.userError = '';
          }
        }

        // if there was any error get out now
        if (this.userError != '' || this.startError || this.endError) {
          return;
        }

        let booking = new FormData();
        booking.append('start', start.toISOString(true));
        booking.append('end', end.toISOString(true));
        booking.append('user_id', this.userId);
        booking.append('color', this.color);
        booking.append('notes', this.notes);

        this.createBooking(booking);
      },

      createBooking(booking) {
        this.loading(true);
        axios.post(this.route('api.gatekeeper.temporary-access-bookings.store'), booking)
          .then((response) => {
            if (response.status == '201') { // HTTP_CREATED
              const booking = this.mapBookings(response.data.data);

              $(this.$refs.selectModal).modal('hide');
              this.removeBookingConfirmation();
              this.calendarApi.unselect();
              const event = this.calendarApi.getEventById(booking.id);
              if (! event) { // make sure the event has not already been added via newBookingEvent
                if (booking.type === 'NORMAL') {
                  this.userCanBook.normalCurrentCount += 1;
                }

                this.calendarApi.addEvent(booking, 'bookings');
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
                if (event.extendedProps.type == "NORMAL") {
                  this.userCanBook.normalCurrentCount -= 1;
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
        $(this.$refs.selectModal).modal('show');
      },

      removeBookingConfirmation() {
        // this.start = '';
        // this.end = '';
        this.$refs.mst.clear();
        this.color = 'primary';
        this.notes = '';
        this.userError = '';
        this.startError = false;
        this.endError = false;

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
        if (booking.color) {
          booking.className = 'temporary-booking-' + booking.color.toLowerCase();
        } else {
          booking.className = 'temporary-booking-primary';
        }

        if (moment().diff(booking.start) > 0
          && moment().diff(booking.end) < 0) {
            // this is a booking under now
          booking.durationEditable = true;
        } else if (moment().diff(booking.start) < 0) {
          booking.editable = true;
        } else {
          booking.className += ' not-editable';
        }

        return booking;
      },

      echoInit() {
        Echo.channel('gatekeeper.temporaryAccessBookings')
          .listen('GateKeeper.NewBooking', this.newBookingEvent)
          .listen('GateKeeper.BookingChanged', this.bookingChangedEvent)
          .listen('GateKeeper.BookingCancelled', this.bookingCancelledEvent);
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
          event.remove();
        }
      },

      /**
       * Watch for changes of the start date picker.
       * TODO: might this be better as a watch on this.start?
       * Nope this is better as event gives us new and old dates as moment's
       */
      startChange(event) {
        // console.log('start dp.change', event);
        // limit end based on bookingLengthMax and new start
        let minEndDate = moment(event.date).add(15, 'minutes');
        let maxEndDate = moment(event.date).add(this.bookingLengthMax, 'minutes');

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
            this.end = newEnd;
          } else {
            this.end = maxEndDate;
          }
        }

        // check if end is now before start
        if (this.end && moment(this.end).isBefore(event.date)) {
          // console.log('end is before start now');
          // update end to same period after start?
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
        // check the end is later than the start
        if (event.date.isBefore(moment(this.start))) {

        }

        // only update is end is after start?
        if (moment(this.end).isAfter(moment(this.start))) {
          this.$nextTick(function () {
            // update the selection
            // console.log('endChange: updating selection to ', this.start, this.end);
            this.calendarApi.select(this.start, this.end);
          });
        }
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

      // workaround for wierd height issue ($nextTick is to soon)
      // migt be related https://github.com/fullcalendar/fullcalendar/issues/4650
      setTimeout(() => {
        this.calendarApi.updateSize();
        this.calendarApi.scrollToTime("06:00");
      }, 100);

      // Call refetchEventsevery 15 minutes, so past events are shaded
      this.interval = setInterval(function () {
        // TODO: once we have Echo running only really need to call this if there is an event under now ±15
        this.calendarApi.refetchEvents();
        this.removePopoverConfirmation();
        $(this.$refs.datetimepickerstart).data('DateTimePicker').minDate(moment().add(15, 'minutes'));
      }.bind(this), 900000);

      this.echoInit();

      $(this.$refs.selectModal).modal('handleUpdate');
      // need to setup close event to unselected
      $(this.$refs.selectModal).on('hidden.bs.modal', this.removeBookingConfirmation);
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
