<template>
  <div class="container" ref="calendar">
    <full-calendar
      ref="fullCalendar"

      :plugins="calendarPlugins"
      locale="en-gb"
      timeZone="Europe/London"
      :firstDay=1
      :eventSources="eventSources"

      @loading="loading"
      @select="select"
      @unselect="unselect"
      :selectAllow="selectAllow"
      @eventClick="eventClick"
      :eventAllow="eventAllow"
      @eventDragStart="removeConfirmation"
      @eventDrop="eventDrop"
      @eventResizeStart="removeConfirmation"
      @eventResize="eventResize"
      @datesDestroy="removeConfirmation"

      :selectable=true
      :selectOverlap=false
      :selectMirror=true
      unselectCancel=".popover"
      :eventOverlap=false
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
  Vue.use(Loading);

  export default {
    components: {
      FullCalendar, // make the <FullCalendar> tag available
    },

    props: {
      bookingLengthMax: Number,
      bookingsMax: Number,
      bookingsUrl: String,
      // initialBookings: Object,
      toolId: Number,
      toolRestricted: Boolean,
      userCanBook: {
        type: Object,
        default: () => ({
          userId: null,
          normal: true,
          normalCurrentCount: 0,
          induction: 0,
          maintenance: 0,
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

      unselect( jsEvent, view ) {
        this.removeConfirmation();
      },

      selectAllow(selectInfo) {
        if (this.isLoading) {

          return false;
        }

        if (this.toolRestricted && ! (this.userCanBook.normal || this.userCanBook.induction || this.userCanBook.maintenance)) {
          // we dont have permissino to even make a booking
          flash('This tool requires an induction for use', 'warning');

          return false;
        }


        // Don't allow selection if start is in the past
        if (moment().diff(selectInfo.start) > 0) {

          return false;
        }

        // TODO: check max allowed bookings and userCanBook

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

        if (this.userCanBook.userId == null) {
          console.error('eventClick: userCanBook.userId not set');

          return false;
        }
        // is it ours and is does it end in the future
        if (info.event.extendedProps.userId == this.userCanBook.userId && moment().diff(info.event.end) < 0) {
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

        const request = axios.get(this.bookingsUrl, {
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
          successCallback(data.map(this.mapBookings));
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
       * Confirmation onConfirm handler.
       * @param  {string} type
       * @param  {object} selectionInfo
       * @return
       */
      bookOnConfirm(type, selectionInfo) {
        switch (type) {
          case 'NORMAL':
            this.bookNormal(moment(selectionInfo.start), moment(selectionInfo.end));
            break;
          case 'INDUCTION':
            this.bookIndcution(moment(selectionInfo.start), moment(selectionInfo.end));
            break;
          case 'MAINTENANCE':
            this.bookMaintenance(moment(selectionInfo.start), moment(selectionInfo.end));
            break;
        }
      },

      bookNormal(start, end) {
        // make a normal booking
        // we can just submit this to the end point, don't need any other user interaction

        let booking = new FormData();
        booking.append('start', start.toISOString(true));
        booking.append('end', end.toISOString(true));
        booking.append('type', 'NORMAL');

        this.createBooking(booking);
      },

      bookIndcution(start, end) {
        // make a induction booking
        // for now we don't need other interaction on inductions
        // TODO: in future we might ask who is to be inducted (or tie this in with induction requests)

        let booking = new FormData();
        booking.append('start', start.toISOString(true));
        booking.append('end', end.toISOString(true));
        booking.append('type', 'INDUCTION');

        this.createBooking(booking);
      },

      bookMaintenance(start, end) {
        // make a maintenance slot
        // TODO: ask for e reason? ask if they want a longer slot (past normal limits), ask if we should disable the tool and let members know

        let booking = new FormData();
        booking.append('start', start.toISOString(true));
        booking.append('end', end.toISOString(true));
        booking.append('type', 'MAINTENANCE');

        this.createBooking(booking);
      },

      createBooking(booking) {
        this.loading(true);
        axios.post(this.bookingsUrl, booking)
          .then((response) => {
            if (response.status == '201') { // HTTP_CREATED
              const booking = this.mapBookings(response.data);

              if (booking.type === 'NORMAL') {
                this.userCanBook.normalCurrentCount += 1;
              }

              this.removeConfirmation();
              this.calendarApi.unselect();
              this.calendarApi.addEvent(booking, 'bookings');
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

        axios.patch(this.bookingsUrl + '/' + event.id, booking)
          .then((response) => {
            if (response.status == '200') { // HTTP_OK
              flash('Booking updated');
              console.log('patchBooking', 'Booking Updated OK');

              // const booking = this.mapBookings(response.data);

              // if (booking.type === 'NORMAL') {
              //   this.userCanBook.normalCurrentCount--;
              // }

              // this.calendarApi.unselect();
              // // this.calendarApi.addEvent(booking, 'bookings'); // this is broken until the next release
              // this.calendarApi.refetchEvents(); // using this until the above is fixed
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

        axios.delete(this.bookingsUrl + '/' + event.id)
          .then((response) => {
            if (response.status == '204') { // HTTP_NO_CONTENT
              flash('Booking cancelled');
              console.log('cancelBooking', 'Booking deleted');

              if (event.extendedProps.type == "NORMAL") {
                this.userCanBook.normalCurrentCount -= 1;
              }

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
      setupBookingConfirmation(selectionInfo) {
        const self = this;

        let options = {
          container: 'body',
          rootSelector: '.fc-mirror',
          selector: '.fc-mirror',
          title: 'Add booking?',
          popout: true,
          singleton: true,
          onConfirm(type) {
            self.bookOnConfirm(type, selectionInfo);
          },
          onCancel() {
            self.calendarApi.unselect();
          },
          buttons: [
            {
              label: this.defaultView == 'timeGridWeek' ? '&nbsp;' : '',
              class: 'btn btn-sm btn-outline-dark',
              iconClass: 'fas fa-times',
              cancel: true,
            },
          ],
        };

        if (this.userCanBook.maintenance) {
          options.buttons.splice(0, 0,
            {
              label: this.defaultView == 'timeGridWeek' ? '&nbsp;Maintenance' : '',
              value: 'MAINTENANCE',
              class: 'btn btn-sm btn-booking-maintenance',
              iconClass: 'fas fa-wrench',
            }
          );
        }

        if (this.userCanBook.induction) {
          options.buttons.splice(0, 0,
            {
              label: this.defaultView == 'timeGridWeek' ? '&nbsp;Induction' : '',
              value: 'INDUCTION',
              class: 'btn btn-sm btn-booking-induction',
              iconClass: 'fas fa-chalkboard-teacher',
            }
          );
        }

        if (this.userCanBook.normal || ! this.toolRestricted) {
          var normalLabel = '&nbsp;Normal';
          if (! (this.userCanBook.maintenance || this.userCanBook.induction)) {
            // Don't need to include the text if this is the only type of booking you can make
            normalLabel = '&nbsp;';
          }

          if (this.userCanBook.normalCurrentCount < this.bookingsMax) {
            options.buttons.splice(0, 0,
              {
                label: this.defaultView == 'timeGridWeek' ? normalLabel : '',
                value: 'NORMAL',
                class: 'btn btn-sm btn-booking-normal',
                iconClass: 'fas fa-check',
              }
            );
          } else {
            const txt = this.bookingsMax > 1 ? 'bookings' : 'booking';
            options.content = 'You can only have ' + this.bookingsMax + ' normal ' + txt + ' for this tool';
          }
        } else {

        }

        $('.fc-mirror').confirmation(options);

        $('.fc-mirror').confirmation('show');
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
      removeConfirmation() {
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
          this.aspectRatio = this.weeAspecctRatio;
        }

        if (this.calendarApi !== null) {
          this.calendarApi.changeView(this.defaultView);
          this.removeConfirmation();
        }
      },

      mapBookings(booking) {
        booking.className = 'tool-' + booking.type.toLowerCase();

        if (booking.userId == this.userCanBook.userId
          && moment().diff(booking.start) > 0
          && moment().diff(booking.end) < 0) {
            // this is our booking under now
          booking.durationEditable = true;
        } else if (booking.userId == this.userCanBook.userId && moment().diff(booking.start) < 0) {
          booking.editable = true;
        } else {
          booking.className += ' not-editable';
        }

        return booking;
      },

    }, // end of methods

    mounted() {
      this.$nextTick(function() {
        window.addEventListener('resize', this.getWindowResize);

        //Init
        this.getWindowResize();
      });

      this.calendarApi = this.$refs.fullCalendar.getApi();

      // Call refetchEventsevery 15 minutes, so past events are shaded
      this.interval = setInterval(function () {
        // TODO: once we have Echo running only really need to call this if there is an event under now ±15
        this.calendarApi.refetchEvents();
      }.bind(this), 900000);
    },

    beforeDestroy() {
      clearInterval(this.interval);
      window.removeEventListener('resize', this.getWindowResize);
    },
  }
</script>
