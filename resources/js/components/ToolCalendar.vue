<template>
  <div class="container">
    <div ref="calendar" id="calendar"></div>
  </div>
</template>

<script>
  import { Calendar } from 'fullcalendar';
  import 'fullcalendar/dist/plugins/moment-timezone';
  import moment from 'moment';
  require('bootstrap-confirmation2');

  export default {
    props: [
      'toolId',
      'bookingLengthMax',
      'bookingsMax',
      'bookingsUrl',
      // 'initialBookings',
      'userCanBook',
    ],

    data() {
      return {
        bookings: [],
        axiosCancle: null,
        calendar: null,
        defaultView: 'agendaDay',
      };
    },

    computed: {
      defaultConfig() {
        const self = this
        return {
          locale: 'en-gb',
          timeZone: 'Europe/London',
          timeZoneImpl: 'moment-timezone',
          firstDay: 1,
          eventSources: [
            {
              events: self.fetchEvents,
              id: 'bookings',
            },
          ],

          loading: self.loading,

          eventClick(info) {
            console.log('eventClick', info);
          },

          select(selectionInfo) {
            self.setupBookingConfirmation(selectionInfo);
          },

          unselect( jsEvent, view ) {
            self.removeConfirmation();
          },

          selectAllow(selectInfo) {
            // Don't allow selection if start is in the past
            if (moment().diff(selectInfo.start) > 0) {
              return false;
            }

            // TODO: check max allowed bookings and userCanBook

            // Check length against tools max booking length
            var duration = moment.duration(moment(selectInfo.end).diff(selectInfo.start));
            if (duration.asMinutes() > self.bookingLengthMax) {
              // TODO: flash message "Max booking length is HH:mm"
              return false;
            }

            return true;
          },

          eventDrop(eventDropInfo) {
            // check it has not been dropped into the past
            if (moment().diff(eventDropInfo.event.start) > 0) {
              // TODO: flash "Bookings can not be moved into the past"
              eventDropInfo.revert();
              return;
            }
            // patch the bookings start and end time
            self.patchBooking(eventDropInfo.event, eventDropInfo.revert)
          },

          eventResize(eventResizeInfo) {
            // check new duration except on Maintenance
            var duration = moment.duration(moment(eventResizeInfo.event.end).diff(eventResizeInfo.event.start));
            if (duration.asMinutes() > self.bookingLengthMax && eventResizeInfo.event.extendedProps.type != 'MAINTENANCE') {
              // TODO: flash message "Max booking length is HH:mm"
              eventResizeInfo.revert();
              return;
            }

            // patch the bookings end time
            self.patchBooking(eventResizeInfo.event, eventResizeInfo.revert)
          },

          selectable: true,
          selectOverlap: false,
          selectMirror: true,
          unselectCancel: '.popover',
          eventOverlap: false,
          defaultView: this.defaultView,
          themeSystem: 'bootstrap4',
          header: {
            left:   'prev',
            center: 'today',
            right:  'next',
          },
          footer: {
            left:   'prev',
            center: 'today',
            right:  'next',
          },
          views: {
            agenda: {
              // options apply to agendaWeek and agendaDay views
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
          },
        };
      },
    }, // end of computed

    methods: {
      /**
       * Attached to 'resize' event so we can make the view responsive.
       */
      getWindowResize(event) {
        const windowWidth = document.documentElement.clientWidth;

        if (windowWidth < 767.98) {
          this.defaultView = 'agendaDay';
        } else {
          this.defaultView = 'agendaWeek';
        }

        if (this.calendar !== null) {
          this.calendar.changeView(this.defaultView);
        }
      },

      mapBookings(booking) {
          booking.className = 'tool-' + booking.type.toLowerCase();

          if (booking.userId != self.userCanBook.userId) {
            booking.className += ' not-ours';
          }

          if (booking.userId == self.userCanBook.userId && moment().diff(booking.start) < 0) {
            booking.editable = true;
          }

          return booking;
      },

      loading(isLoading) {
        // TODO: loading spinner/grey out
        console.log('loading', isLoading)
      },

      /**
       * FullCalendar will call this function whenever it needs new event data.
       * This is triggered when the user clicks prev/next or switches views.
       */
      fetchEvents(fetchInfo, successCallback, failureCallback) {
        self = this
        const CancelToken = axios.CancelToken;
        if (this.axiosCancle !== null) {
          this.axiosCancle("New events range requested");
        }

        const request = axios.get(this.bookingsUrl, {
          params: {
            start: fetchInfo.startStr,
            end: fetchInfo.endStr,
          },
          cancelToken: new CancelToken(function executor(c) {
            self.axiosCancle = c;
          }),
        });

        request.then(({ data }) => {
          // need to map over our api response first to prep them for fullcalender
          self.bookings = data.map(self.mapBookings);

          // pass bookings over to fullcalenders callback
          successCallback(self.bookings);
        })
        .catch((thrown) => {
          if (axios.isCancel(thrown)) {
            // console.log('fetchEvents: Request cancelled', thrown.message);
          } else {
            // handle error
            console.log('fetchEvents: Request error', thrown);
            failureCallback(thrown);
          }
        });

        return request;
      },

      /**
       * Confirmation onConfirm handler.
       * @param  {string} type
       * @param  {object} selectionInfo
       * @return
       */
      bookOnConfirm(type, selectionInfo) {
        switch (type) {
          case "NORMAL":
            this.bookNormal(selectionInfo.startStr, selectionInfo.endStr);
            break;
          case "INDUCTION":
            this.bookIndcution(selectionInfo.startStr, selectionInfo.endStr);
            break;
          case "MAINTENANCE":
            this.bookMaintenance(selectionInfo.startStr, selectionInfo.endStr);
            break;
        }
      },

      bookNormal(start, end) {
        // make a normal booking
        // we can just submit this to the end point, don't need any other user interaction

        let booking = new FormData();
        booking.append('start', start);
        booking.append('end', end);
        booking.append('type', 'NORMAL');

        this.createBooking(booking);
      },

      bookIndcution(start, end) {
        // make a induction booking
        // for now we don't need other interaction on inductions
        // TODO: in future we might ask who is to be inducted (or tie this in with induction requests)

        let booking = new FormData();
        booking.append('start', start);
        booking.append('end', end);
        booking.append('type', 'INDUCTION');

        this.createBooking(booking);
      },

      bookMaintenance(start, end) {
        // make a maintenance slot
        // TODO: ask for e reason? ask if they want a longer slot (past normal limits), ask if we should disable the tool and let members know

        let booking = new FormData();
        booking.append('start', start);
        booking.append('end', end);
        booking.append('type', 'MAINTENANCE');

        this.createBooking(booking);
      },

      createBooking(booking) {
        this.loading(true);
        axios.post(this.bookingsUrl, booking)
          .then((response) => {
            // TODO: deal with the response
            if (response.status == '201') { // HTTP_CREATED
              const booking = this.mapBookings(response.data);

              if (booking.type === 'NORMAL') {
                this.userCanBook.normalCurrentCount += 1;
              }

              this.calendar.unselect();
              // this.calendar.addEvent(booking, 'bookings'); // this is broken until the next release
              this.calendar.refetchEvents(); // using this until the above is fixed
              // this.loading(false);
            } else {
              console.log('createBooking', response.data);
              console.log('createBooking', response.status);
              console.log('createBooking', response.statusText);
            }
          })
          .catch((error) => {
            // TODO: flash error
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
              // else if HTTP_CONFLICT to many bookings or over lap
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
        }

        this.loading(true);

        axios.patch(this.bookingsUrl + '/' + event.id, booking)
          .then((response) => {
            // TODO: deal with the response
            if (response.status == '200') { // HTTP_CREATED
              // flash "Booking updated"
              console.log('patchBooking', 'Booking Updated OK');

              // const booking = this.mapBookings(response.data);

              // if (booking.type === 'NORMAL') {
              //   this.userCanBook.normalCurrentCount--;
              // }

              // this.calendar.unselect();
              // // this.calendar.addEvent(booking, 'bookings'); // this is broken until the next release
              // this.calendar.refetchEvents(); // using this until the above is fixed

              this.loading(false);
            } else {
              console.log('patchBooking', response.data);
              console.log('patchBooking', response.status);
              console.log('patchBooking', response.statusText);
            }
          })
          .catch((error) => {
            // TODO: flash error
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
              // else if HTTP_CONFLICT to many bookings or over lap
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

      /**
       * Display bootstrap confirmation popover for a new selection.
       */
      setupBookingConfirmation(selectionInfo) {
        const self = this;

        let options = {
          container: 'body',
          rootSelector: '.fc-mirror',
          selector: '.fc-mirror',
          title: "Add booking?",
          onConfirm(type) {
            self.bookOnConfirm(type, selectionInfo);
          },
          onCancel() {
            self.calendar.unselect();
          },
          buttons: [
            {
              label: this.defaultView == 'agendaWeek' ? '&nbsp;' : '',
              class: 'btn-outline-dark',
              iconClass: 'fas fa-times',
              cancel: true,
            },
          ],
        };

        if (this.userCanBook.maintenance) {
          options.buttons.splice(0, 0,
            {
              label: this.defaultView == 'agendaWeek' ? '&nbsp;Maintenance' : '',
              value: 'MAINTENANCE',
              class: 'btn-booking-maintenance',
              iconClass: 'fas fa-wrench',
            }
          );
        }

        if (this.userCanBook.induction) {
          options.buttons.splice(0, 0,
            {
              label: this.defaultView == 'agendaWeek' ? '&nbsp;Induction' : '',
              value: 'INDUCTION',
              class: 'btn-booking-induction',
              iconClass: 'fas fa-chalkboard-teacher',
            }
          );
        }

        if (this.userCanBook.normal) {
          if (this.userCanBook.normalCurrentCount < this.bookingsMax) {
            options.buttons.splice(0, 0,
              {
                label: this.defaultView == 'agendaWeek' ? '&nbsp;Normal' : '',
                value: 'NORMAL',
                class: 'btn-booking-normal',
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
       * Remove the previous bootstrap confirmation popover.
       */
      removeConfirmation() {
        $('.popover').remove();
      },

    }, // end of methods

    mounted() {
      const cal = this.$refs.calendar
      self = this

      this.$nextTick(function() {
        window.addEventListener('resize', this.getWindowResize);

        //Init
        this.getWindowResize();
      });

      this.calendar = new Calendar(cal, self.defaultConfig);
      this.calendar.render();
    },

    beforeDestroy() {
      window.removeEventListener('resize', this.getWindowResize);
    },
  }
</script>

<style lang="scss">
@import '~sass/_variables.scss';
@import '~fullcalendar/dist/fullcalendar.css';
@import '~sass/color-helpers';

// override the bootstrap 4 theme today highlight
.fc-today {
  background-color:inherit !important;
}

.fc-past {
  background: #d7d7d7;
}

.fc-slats table tbody tr:nth-of-type(odd) {
  background-color: rgba(0, 0, 0, 0.05);
}

.popover{
    max-width: 100%;
}

/*
 * Tool related bits
 */
.tool-normal {
  border-color: $tool-booking-normal;
  background-color: $tool-booking-normal !important;
  &.not-ours {
    background: repeating-linear-gradient(
        -45deg,
        $tool-booking-normal,
        $tool-booking-normal 10px,
        tint($tool-booking-normal, 10%) 10px,
        tint($tool-booking-normal, 10%) 20px
    );
  }
}

.tool-induction {
  border-color: $tool-booking-induction;
  background-color: $tool-booking-induction !important;
  &.not-ours {
    background: repeating-linear-gradient(
        -45deg,
        $tool-booking-induction,
        $tool-booking-induction 10px,
        tint($tool-booking-induction, 10%) 10px,
        tint($tool-booking-induction, 10%) 20px
    );
  }
}

.tool-maintenance {
  border-color: $tool-booking-maintenance;
  background-color: $tool-booking-maintenance !important;
  &.not-ours {
    background: repeating-linear-gradient(
        -45deg,
        $tool-booking-maintenance,
        $tool-booking-maintenance 10px,
        tint($tool-booking-maintenance, 10%) 10px,
        tint($tool-booking-maintenance, 10%) 20px
    );
  }
}

</style>
