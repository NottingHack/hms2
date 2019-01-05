<template>
  <div class="container">
    <div ref="calendar" id="calendar"></div>
  </div>
</template>

<script>
  import { Calendar } from 'fullcalendar';
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
          timeZone: 'Europe/London',
          events: self.events,
          eventClick(info) {
            console.log(info);
          },

          selectable: true,
          selectOverlap: false,
          selectMirror: true,
          // unselectCancel: '', https://fullcalendar.io/docs/v4/unselectCancel

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

          // datesRender(info) {
          //   // console.log(info);
          // },
          eventRender: function(event, element) {
          //   element.find('.fc-title').append("<br/>" + event.description);
          //  TODO: if this is one of our own bookings and in the future we should be able to cancel it or edit it?
          },

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
    },

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

      /**
       * FullCalendar will call this function whenever it needs new event data.
       * This is triggered when the user clicks prev/next or switches views.
       */
      events(fetchInfo, successCallback, failureCallback) {
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
          // console.log(data);
          self.bookings = data;
          successCallback(data);
        })
        .catch((thrown) => {
          if (axios.isCancel(thrown)) {
            // console.log('Request cancelled', thrown.message);
          } else {
            // handle error
            console.log('Request error', thrown);
            failureCallback(thrown);
          }
        });

        return request;
      },

      /**
       * Confirmation onConfirm handler.
       * @param  {string} type
       * @param  {object} selectInfo
       * @return
       */
      book(type, selectInfo) {
        // console.log(type, selectInfo);
        switch (type) {
          case "NORMAL":
            this.bookNormal(selectInfo);
            break;
          case "INDUCTION":
            this.bookIndcution(selectInfo);
            break;
          case "MAINTENANCE":
            this.bookMaintenance(selectInfo);
            break;
        }
      },

      bookNormal(selectInfo) {
        // try to make a normal booking
        // we can just submit this to the end point, don't need any other user interaction

        let booking = new FormData();

        booking.append('type', 'NORMAL');
        booking.append('start', selectInfo.startStr);
        booking.append('end', selectInfo.endStr)

        axios.post(this.bookingsUrl, booking)
          .then((response) => {
            // TODO: deal with the response
            // if HTTP_CREATED we have a new booking
            // else if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
            // else if HTTP_CONFLICT to many bookings or over lap
            // else if HTTP_FORBIDDEN on enough permissions
            console.log(response.data);
          });
      },

      bookIndcution(selectInfo) {

        let booking = new FormData();

        booking.append('type', 'INDUCTION');
        booking.append('start', selectInfo.startStr);
        booking.append('end', selectInfo.endStr)

        axios.post(this.bookingsUrl, booking)
          .then((response) => {
            // TODO: deal with the response
            // if HTTP_CREATED we have a new booking
            // else if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
            // else if HTTP_CONFLICT to many bookings or over lap
            // else if HTTP_FORBIDDEN on enough permissions
            console.log(response.data);
          });
      },

      bookMaintenance(selectInfo) {

        let booking = new FormData();

        booking.append('type', 'MAINTENANCE');
        booking.append('start', selectInfo.startStr);
        booking.append('end', selectInfo.endStr)

        axios.post(this.bookingsUrl, booking)
          .then((response) => {
            // TODO: deal with the response
            // if HTTP_CREATED we have a new booking
            // else if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
            // else if HTTP_CONFLICT to many bookings or over lap
            // else if HTTP_FORBIDDEN on enough permissions
            console.log(response.data);
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
            self.book(type, selectionInfo);
          },
          onCancel() {
            self.calendar.unselect();
          },
          buttons: [
            {
              label: '&nbsp;',
              class: 'btn-outline-dark',
              iconClass: 'fas fa-times',
              cancel: true,
            },
          ],
        };

        if (this.userCanBook['maintenance']) {
          options.buttons.splice(0, 0,
            {
              label: this.defaultView == 'agendaWeek' ? '&nbsp;Maintenance' : '',
              value: 'MAINTENANCE',
              class: 'btn-booking-maintenance',
              iconClass: 'fas fa-wrench',
            }
          );
        }

        if (this.userCanBook['induction']) {
          options.buttons.splice(0, 0,
            {
              label: this.defaultView == 'agendaWeek' ? '&nbsp;Induction' : '',
              value: 'INDUCTION',
              class: 'btn-booking-induction',
              iconClass: 'fas fa-chalkboard-teacher',
            }
          );
        }

        if (this.userCanBook['normal']) {
          if (this.userCanBook['normalCurrentCount'] < this.bookingsMax) {
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

    },

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
@import "~sass/_variables.scss";
@import '~fullcalendar/dist/fullcalendar.css';

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
</style>
