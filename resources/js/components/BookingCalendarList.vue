<template>
  <div ref="calendar">
    <full-calendar
      ref="fullCalendar"

      :plugins="calendarPlugins"
      locale="en-gb"
      timeZone="Europe/London"
      :firstDay=1
      :eventSources="eventSources"

      @loading="loading"
      @unselect="unselect"
      @eventClick="eventClick"
      :datesDestroy="removeConfirmation"
      :viewSkeletonRender="viewSkeletonRender"

      :selectable=true
      :selectOverlap=false
      :selectMirror=true
      unselectCancel=".popover"
      :eventOverlap=false
      defaultView="bookingList"
      themeSystem="bootstrap"
      noEventsMessage="No bookings to display"
      :header="false"
      :footer="false"
      :visibleRange="visibleRange"
      :views="{
        bookingList: {
          type: 'list',
          duration: { days: 7 },
          eventTimeFormat: {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
          },
        },
      }"
      />
  </div>
</template>

<script>
  import FullCalendar from '@fullcalendar/vue';
  import listPlugin from '@fullcalendar/list';
  import interactionPlugin from '@fullcalendar/interaction';
  import momentPlugin from '@fullcalendar/moment';
  import momentTimezonePlugin from '@fullcalendar/moment-timezone';
  import bootstrapPlugin from '@fullcalendar/bootstrap';
  import moment from 'moment';
  import humanizeDuration from 'humanize-duration';
  import Loading from 'vue-loading-overlay';
  Vue.use(Loading);

  export default {
    components: {
      FullCalendar, // make the <FullCalendar> tag available
    },

    props: {
      bookingsUrl: String,
      userId: Number,
      initialBookings: Array,
      toolIds: Array,
      removeCardClass: Boolean,
    },

    data() {
      return {
        axiosCancle: null,
        bookings: null,
        calendarApi: null,
        calendarPlugins: [
          listPlugin,
          interactionPlugin,
          momentPlugin,
          momentTimezonePlugin,
          bootstrapPlugin,
        ],
        interval: null,
        isLoading: true,
        loader: null,
      };
    },

    computed: {
      eventSources() {
        return [
          {
            events: this.initialBookings.map(this.mapBookings),
            id: 'bookings',
          },
        ];
      },
      visibleRange() {
        return {
          start: moment().startOf('day').toDate(),
          end: moment().add(7, 'days')
        };
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

      unselect( jsEvent, view ) {
        this.removeConfirmation();
      },

      eventClick(info) {
        console.log(info)
        if (this.isLoading) {
          return false;
        }

        // is it ours and is does it end in the future
        if (moment().diff(info.event.end) < 0) {
          this.setupCancleConfirmation(info);
        }
      },

      cancelBooking(event) {
        console.log('cancelBooking', event);

        this.loading(true);

        axios.delete(this.bookingsUrl.replace("_ID_", event.extendedProps.toolId) + '/' + event.id)
          .then((response) => {
            if (response.status == '204') { // HTTP_NO_CONTENT
              flash('Booking cancelled');
              console.log('cancelBooking', 'Booking deleted');

              event.remove();
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
      setupCancleConfirmation(info) {
        console.log('setupCancleConfirmation')
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
      removeConfirmation() {
        $('.popover').remove();
      },

      checkBookings() {
        let events = this.calendarApi.getEvents();

        events.forEach(function (event) {
          if (moment().diff(event.end) > 0) {
            console.log('checkBookings removing', event);
            event.remove()
          }
        });
      },

      mapBookings(booking) {
        // booking.backgroundColor = "rgba(0,0,0,0);
        booking.className = 'tool-list-' + booking.type.toLowerCase();

        if (moment().diff(booking.start) > 0
          && moment().diff(booking.end) < 0) {
            // this is our booking under now
          booking.durationEditable = true;
        } else if (moment().diff(booking.start) < 0) {
          booking.editable = true;
        } else {
          booking.className += ' not-editable';
        }

        return booking;
      },

      viewSkeletonRender(info) {
        // FullCalendar's bootstrap theme uses card on there fc-list-view class
        // this causes a card inside a card
        if (this.removeCardClass) {
          $(info.el).removeClass(['card', 'fc-list-view'])
        }
      },

      echoInit() {
        this.toolIds.forEach(function (toolId) {
          Echo.channel('tools.' + toolId + '.bookings')
            .listen('Tools.NewBooking', this.newBookingEvent)
            .listen('Tools.BookingChanged', this.bookingChangedEvent)
            .listen('Tools.BookingCancelled', this.bookingCancelledEvent);
        }, this);
      },

      echoDeInit() {
        this.toolIds.forEach(function (toolId) {
          Echo.leave('tools.' + toolId + '.bookings');
        });
      },

      newBookingEvent(newBooking) {
        // console.log('Echo sent newBooking event', newBooking);
        if (newBooking.booking.userId != this.userId) {
          return;
        }
        const event = this.calendarApi.getEventById(newBooking.booking.id);
        if (event) {
          return;
        }
        const booking = this.mapBookings(newBooking.booking);
        this.calendarApi.addEvent(booking, 'bookings');
      },

      bookingChangedEvent(bookingChanged) {
        // console.log('Echo sent bookingChanged event', bookingChanged);
        if (bookingChanged.booking.userId != this.userId) {
          return;
        }
        const oldEvet = this.calendarApi.getEventById(bookingChanged.booking.id);
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
    }, // end of methods

    mounted() {
      this.calendarApi = this.$refs.fullCalendar.getApi();

      // workaround for wierd height issue ($nextTick is to soon)
      // migt be related https://github.com/fullcalendar/fullcalendar/issues/4650
      setTimeout(() => {
        this.calendarApi.updateSize();
      }, 100);

      // Call checkBookings minute, so past events are shaded
      this.interval = setInterval(function () {
        // TODO: once we have Echo running only really need to call this if there is an event under now ±15
        this.checkBookings()
      }.bind(this), 60000); // 900000

      this.echoInit();

      this.isLoading = false;
    },

    beforeDestroy() {
      clearInterval(this.interval);
      this.echoDeInit();
    },
  }
</script>
