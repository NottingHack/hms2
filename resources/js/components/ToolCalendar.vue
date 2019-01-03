<template>
  <div class="container">
    <div ref="calendar" id="calendar"></div>
  </div>
</template>

<script>
  import { Calendar } from 'fullcalendar';
  import moment from 'moment';

  export default {
    props: [
      'toolId',
      'bookingsUrl',
      // 'initialBookings',
      'userCanBook',
    ],

    data() {
      return {
        bookings: [],
        axiosCancle: null,
        calendar: null,

        config: {
          timeZone: 'Europe/London',
          events: this.events,
          dateClick: function(arg) {
            console.log(arg.date.toString()); // use *local* methods on the native Date Object
            // will output something like 'Sat Sep 01 2018 00:00:00 GMT-XX:XX (Eastern Daylight Time)'
          },
          selectable: true,
          selectAllow: function(selectInfo) {
            return moment().diff(selectInfo.start) <= 0;
          },
          // businessHours: this.businessHours(),
          datesRender: function (info) {
            // console.log(info);
          },
          defaultView: 'agendaDay',
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
        },
      };
    },

    methods: {
      getWindowResize(event) {
        const windowWidth = document.documentElement.clientWidth;

        if (windowWidth < 767.98) {
          this.config.defaultView = 'agendaDay';
        } else {
          this.config.defaultView = 'agendaWeek';
        }

        if (this.calendar !== null) {
          this.calendar.changeView(this.config.defaultView);
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
          this.axiosCancle("New events request");
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
    },

    mounted() {
      const cal = this.$refs.calendar
      self = this

      this.$nextTick(function() {
        window.addEventListener('resize', this.getWindowResize);

        //Init
        this.getWindowResize();
      });

      this.calendar = new Calendar(cal, this.config);
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

</style>
