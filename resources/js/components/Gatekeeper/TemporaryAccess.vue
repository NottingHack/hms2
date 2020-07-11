<template>
  <div class="vue-remove">
    <div class="container">
      <!-- <p>To schedule temporary access for a User please select and drag on the calendar below.</p>
      <p>To re-schedule a booking, click and move the booking to a new time.</p>
      <p>To cancel a booking, click on the booking then confirm cancellation.</p> -->
      <p>Words about the current access state being <strong>{{ building.accessStateString }}</strong></p>
      <p>The maximum concurrent occupancy is currently {{ building.selfBookMaxOccupancy }}</p>
      <p>This building has the following bookable areas. Hover for area occupany limits.</p>
      <p>
        <template v-for="bookableArea in bookableAreas" >
          <span
            :class="['badge', 'badge-' + bookableArea.bookingColor, 'ta-badge-font-inherit', 'p-3']"
            data-toggle="tooltip"
            data-html="true"
            :title="'Occupancy:&nbsp;' + bookableArea.maxOccupancy">
            {{ bookableArea.name }}
          </span>&nbsp;
        </template>
      </p>
      <p v-if="settings.grant != 'ALL'">Some areas can only be booked by Trustees.</p>
      <h5>Booking  Key</h5>
      <p>
        <span
          class="badge badge-success ta-badge-font-inherit p-3 temporary-booking-success"
          data-toggle="tooltip"
          data-html="true"
          title="Automatically approved booking.<br>You can edit this booking."
          >
          Editable
        </span>&nbsp;
        <span
          class="badge badge-success ta-badge-font-inherit p-3 temporary-booking-success not-approved"
          data-toggle="tooltip"
          data-html="true"
          title="You have requested access to the space but it has not yet been approved by the Trustees.<br>You can still edit the time or cancel this booking."
          >
          Requested
        </span>&nbsp;
        <span
          class="badge badge-success ta-badge-font-inherit p-3 temporary-booking-success not-editable"
          data-toggle="tooltip"
          data-html="true"
          title="If this is your booking, it has been approved by the Trustees.<br>You may still cancel it but the time is fixed."
          >
          Not editable
        </span>
      </p>
    </div>

    <div class="container vld-parent" ref="calendar">
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
        :eventDestroy="eventDestroy"

        :selectOverlap=true
        :selectable="selectable"
        :selectMirror=true
        unselectCancel=".modal-content"
        :eventOverlap="eventOverlap"
        :defaultView="initialView"
        :defaultDate="initialDate"
        noEventsMessage="No bookings to display"
        themeSystem="bootstrap"
        :header="dayOnlyButtons"
        :footer="dayOnlyButtons"
        :buttonText="{
          today: 'Today',
          day: 'Day',
          week: 'Week',
        }"
        :aspectRatio="this.dayAspectRatio"
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
    <div ref="bookingModal" class="modal fade" :id="$id('addBookingModal')" tabindex="-1" role="dialog" :aria-labelledby="$id('addBookingLabel')" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" :id="$id('addBookingLabel')">{{ modalTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>{{ settings.bookingInfoText }}</p>
            <div :class="['form-group', buildingError ? 'is-invalid' : '']">
              <label :for="$id('building')">Building</label>
              <div class="form-control-plaintext" :id="$id('building')" >
                {{ building.name }}
                <small v-if="settings.grant == 'ALL'" class="text-muted"> ({{ building.accessStateString }})</small>
              </div>
              <div class="invalid-feedback d-block" role="alert" v-if="buildingError">{{ buildingError }}</div>
            </div>

            <div :class="['form-group', userError ? 'is-invalid' : '']" v-if="settings.grant == 'ALL'">
              <label :for="$id('user')">Select Member</label>
              <member-select-two
                :id="$id('user')"
                ref="mst"
                v-model="userId"
                :name="null"
                :current-only="true"
                />
              <div class="invalid-feedback d-block" role="alert" v-if="userError">{{ userError }}</div>
            </div>

            <div :class="['form-group', bookableAreaError ? 'is-invalid' : '']" ref="bookableAreaSelectDiv">
              <label :for="$id('userId')">Select Area</label>
              <select-two
                :id="$id('userId')"
                ref="bast"
                v-model="bookableAreaId"
                :name="null"
                placeholder="Select an area to book"
                :settings="bookableAreaSettings"
                :options="bookableAreaOptions"
                style="width: 100%"
                />
              <div class="invalid-feedback d-block" role="alert" v-if="bookableAreaError">{{ bookableAreaError }}</div>
              <small class="form-text text-muted">
                <template v-if="settings.grant != 'ALL'">Select the area of the space you will be occupying.</template>
                <template v-else>Select the area of the space this member will be occupying.</template>
                <br>
                <template v-if="selectedBookableArea">
                  Area maximum occupancy: {{ selectedBookableArea.maxOccupancy }}
                  <template v-if="selectedBookableArea.maxOccupancy == 1 && selectedBookableArea.additionalGuestOccupancy > 0">
                    Additional guest occupancy: {{ selectedBookableArea.additionalGuestOccupancy }}
                  </template>
                </template>
              </small>
            </div>

            <div class="form-group">
              <label :for="$id('guests')">Guests</label>
              <div :id="$id('guests')" class="text-center">
                <div class="btn-group btn-group-toggle">
                  <label class="btn btn-lg btn-success" :class="{ active: guests === 0 }">
                    <input
                      type="radio"
                      name="guests"
                      :value="0"
                      v-model.number="guests"
                      >No Guests
                  </label>
                  <label class="btn btn-lg btn-success" :class="{ active: guests === n }" v-for="n in settings.maxGuestsPerUser">
                    <input
                      type="radio"
                      name="guests"
                      :value="n"
                      v-model.number="guests">{{ n }}
                  </label>
                </div>
              </div>
              <div class="invalid-feedback d-block" role="alert" v-if="guestsError">{{ guestsError }}</div>
              <small :id="$id('guestsHelpBlock')" class="form-text text-muted" v-if="settings.maxGuestsPerUser">
                At this time you may bring up to {{ settings.maxGuestsPerUser }} guest{{ settings.maxGuestsPerUser >= 2 ? 's' : '' }} into the space.
              </small>
              <small :id="$id('guestsHelpBlock')" class="form-text text-muted" v-else>
                At this time you are not allowed to bring guests into the space.
              </small>
            </div>

            <div class="form-group" v-if="building.accessState == 'REQUESTED_BOOK' || settings.grant == 'ALL'">
              <label :for="$id('notes')">{{ (settings.grant == 'ALL') ? 'Notes' : 'Reason for booking' }}</label>
              <input
                type="text"
                v-model="notes"
                class="form-control"
                :id="$id('notes')"
                maxlength=250
                :aria-describedby="$id('notesHelpBlock')"
                :required="settings.grant != 'ALL'"
                >
              <div class="invalid-feedback d-block" role="alert" v-if="notesError">{{ notesError }}</div>
              <small :id="$id('notesHelpBlock')" class="form-text text-muted" v-if="settings.grant != 'ALL' && building.accessState == 'REQUESTED_BOOK'">
                Please give a short reason for your use of the space to help the Trustees review this request.
              </small>
            </div>

            <div class="form-group">
              <label :for="$id('datetimepickerstart')">Start</label>
              <div class="input-group" ref="datetimepickerstart">
                <date-picker
                  v-model="start"
                  :id="$id('datetimepickerstart')"
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
              <label :for="$id('datetimepickerend')">End</label>
              <div class="input-group" ref="datetimepickerend">
                <date-picker
                  v-model="end"
                  :id="$id('datetimepickerend')"
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

    <!-- Reason Modal -->
    <div ref="reasonModal" class="modal fade" :id="$id('reasonModal')" tabindex="-1" role="dialog" :aria-labelledby="$id('reasonLabel')" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" :id="$id('reasonLabel')">{{ reasonModalTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <dl class="row" v-if="rejectOrCancelEvent">
              <dt class="col-sm-4">Bookable Area</dt>
              <dd class="col-sm-8">
                <span :class="['badge', 'badge-' + rejectOrCancelEvent.extendedProps.bookableArea.bookingColor, 'ta-badge-font-inherit']">
                  {{ rejectOrCancelEvent.extendedProps.bookableArea.name }}
                </span>
              </dd>

              <dt class="col-sm-4">Name</dt>
              <dd class="col-sm-8">{{ rejectOrCancelEvent.title }}&nbsp;</dd>

              <dt class="col-sm-4">Reason</dt>
              <dd class="col-sm-8">{{ rejectOrCancelEvent.extendedProps.notes }}&nbsp;</dd>

              <dt class="col-sm-4">Guests</dt>
              <dd class="col-sm-8">{{ rejectOrCancelEvent.extendedProps.guests }}</dd>

              <dt class="col-sm-4">Start</dt>
              <dd class="col-sm-8">{{ rejectOrCancelEvent.start | moment('YYYY-MM-DD HH:mm') }}</dd>

              <dt class="col-sm-4">End</dt>
              <dd class="col-sm-8">{{ rejectOrCancelEvent.end | moment('YYYY-MM-DD HH:mm') }}</dd>
            </dl>

            <div class="form-group">
              <label :for="$id('reason')">Please give your reason for {{ rejectOrCancel ? 'rejecting' : 'cancelling' }} this booking.</label>
              <input
                type="text"
                v-model="reason"
                class="form-control"
                :id="$id('reason')"
                maxlength=250
                :aria-describedby="$id('reasonHelpBlock')"
                required
                >
              <div class="invalid-feedback d-block" role="alert" v-if="reasonError">{{ reasonError }}</div>
              <small :id="$id('reasonHelpBlock')" class="form-text text-muted">
                The reason you give will be include in the notification to the member.
              </small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="sumbit" class="btn btn-danger" @click="reasonSubmit">{{ reasonButtonText }}</button>
          </div>
        </div>
      </div>
    </div> <!-- /Reason Modal -->
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
  import VueMoment from 'vue-moment';
  Vue.use(Loading);
  Vue.use(VueMoment, {
    moment,
  });

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
        initialView: 'timeGridDay',
        initialDate: null,
        dayAspectRatio: 0.8,
        weekAspectRatio: 1.35, // full calender default
        dayOnlyButtons: {
          left:   'prev',
          center: 'today',
          right:  'next',
        },
        dayMonthButtons: {
          left:   'prev',
          center: 'today timeGridDay,timeGridWeek',
          right:  'next',
        },
        interval: null,
        isLoading: true,
        loader: null,
        userId: '',
        bookableAreaId: '',
        guests: 0,
        notes: '',
        start: '',
        end: '',
        buildingError: false,
        userError: false,
        bookableAreaError: false,
        guestsError: false,
        notesError: false,
        startError: false,
        endError: false,
        datapickerConfig: {
          icons: {
              time: 'fad fa-clock',
              date: 'far fa-calendar-alt',
              up: 'fas fa-arrow-up',
              down: 'fas fa-arrow-down',
              previous: 'fas fa-chevron-left',
              next: 'fas fa-chevron-right',
              today: 'fad fa-calendar-check',
              clear: 'fas fa-trash',
              close: 'fas fa-times'
          },
          locale: 'en-gb',
          format: 'YYYY-MM-DD HH:mm',
          ignoreReadonly: true,
          allowInputToggle: true,
          useCurrent: false,
          stepping: 15,
          minDate: moment().add(15, 'minutes'), // can not pick in the past
        },
        reason: '',
        reasonError: false,
        rejectOrCancel: true,
        rejectOrCancelEvent: null,
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
        // placeholder needs an empty slot, plus we filter based on grant
        if (this.settings.grant == 'ALL') {
          return [{id:'', text:''}].concat(this.bookableAreas);
        } else {
          // need to filter for only areas with selfBookable true
          return [{id:'', text:''}].concat(this.bookableAreas.filter(bookableArea => bookableArea.selfBookable));
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

      reasonModalTitle() {
        if (this.rejectOrCancel) {
          return 'Booking rejection reason?'
        }
        return 'Booking cancellation reason?'
      },

      reasonButtonText() {
        if (this.rejectOrCancel) {
          return 'Reject Booking Request'
        }
        return 'Confirm Cancellation'
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

      reason(newValue, oldValue) {
        this.reasonError = false;
      },

      guests(newValue, oldValue) {
        let start = moment(this.start);
        let end = moment(this.end);
        this.buildingError = this.checkBuildingLimits(start, end)
        if (this.selectedBookableArea){
          this.bookableAreaError = this.checkClashByBookalbeArea(start, end, this.bookableAreaId);
        }
      }
    },

    methods: {
      loading(isLoading, fullPage=false) {
        this.isLoading = isLoading;
        if (isLoading && this.loader == null) {
          this.loader = this.$loading.show({
            container: fullPage ? null : this.$refs.calendar,
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
        this.removePopoverConfirmation();
        if (this.isLoading) {

          return false;
        }

        // Don't allow selection if start is in the past
        if (moment().add(10, 'minutes').diff(selectInfo.start) > 0) {

          return false;
        }

        if (this.settings.grant == "SELF") {
          // need to check maxConcurrentPerUser against userByBuildingId.future
          if (this.settings.userByBuildingId[this.building.id].futureCount >= this.settings.maxConcurrentPerUser) {
            flash('You can only have ' + this.settings.maxConcurrentPerUser + ' concurrent Bookings', 'warning');

            return false;
          }
        }

        // need to check how long since last booking
        let latestBookingEnd = this.settings.userByBuildingId[this.building.id].latestBookingEnd;
        let nextStart = latestBookingEnd && moment(latestBookingEnd).add(this.settings.minPeriodBetweenBookings, 'minutes');

        if (this.settings.grant != 'ALL'
          && nextStart
          && nextStart.isAfter(moment(selectInfo.start))
        ) {
          flash('You may not start another booking before ' + nextStart.format('YYYY-MM-DD HH:mm'), 'warning');
          return false;
        }

        // need to check for overlap with other events for this.building.selfBookMaxOccupancy
        // can not yet check area limits as it has not been defined yet
        let start = moment(selectInfo.start);
        let end = moment(selectInfo.end);

        let buildingError = this.checkBuildingLimits(start, end);
        if (buildingError) {
          flash(buildingError, 'warning');
          if (this.settings.grant != 'ALL') {
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
          this.setupApproveRejcectCancleConfirmation(info);
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
        // console.log('eventAllow'); //, dropInfo, draggedEvent);
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

        let buildingError = this.checkBuildingLimits(start.clone(), end.clone(), draggedEvent)
        if (buildingError) {
          flash(buildingError, 'warning');
          if (this.settings.grant != 'ALL') {
            return false;
          }
        }

        // check bookable area limits
        let bookableAreaError = this.checkClashByBookalbeArea(start.clone(), end.clone(), draggedEvent.extendedProps.bookableArea.id, draggedEvent)
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

        // TODO: need to check how long since last booking

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
        // console.log('eventRender', info);
        if (info.isMirror || info.event.rendering == 'background') {
          return;
        }
        let extendedProps = info.event.extendedProps;

        if (this.settings.view == 'ALL' || extendedProps.userId == this.settings.userId) {
          let content = info.event.title;

          // if (this.settings.view == 'ALL') {
          //   if (content != '') {
          //     content += '<br>'
          //   }

          //   content += info.event.title;
          // }

          if (extendedProps.bookableArea){
            if (content != '') {
              content += '<br>'
            }

            content += 'Area: ' + extendedProps.bookableArea.name;
          }

          // if (extendedProps.guests != undefined) {
          //  if (content != '') {
          //     content += '<br>'
          //   }

          //   content += 'Guests: ' + extendedProps.guests;
          // }

          if (extendedProps.approved) {
            if (extendedProps.userId == extendedProps.approvedById) {
              if (content != '') {
                content += '<br>'
              }

              content += 'Automatically approved';
            } else {
              if (content != '') {
                content += '<br>'
              }

              if (this.settings.view == 'ALL') {
                content += 'Approved by: ' + extendedProps.approvedByName ?? ''
              } else {
                content += 'Approved'
              }
            }
          } else {
            if (content != '') {
              content += '<br>'
            }

            content += '<strong>This booking requires approval</strong>';
          }

          if (extendedProps.notes) {
            if (content != '') {
              content += '<br>'
            }

            content += 'Notes: ' + $('<span>' + extendedProps.notes + '</span>').text();
          }

          if (content != '') {
            // now render the tool tip
            // $(info.el).addClass('tooltip-' + info.event.id);

            $(info.el).tooltip({
              container: 'body',
              title: content,
              html: true,
            });
          }
        }
      },

      eventDestroy(info) {
        // console.log('eventDestroy', info)
        $(info.el).tooltip('dispose');
        $(info.el).confirmation('dispose');
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

        const request = axios.get(this.route('api.gatekeeper.temporary-access-bookings.index').url(), {
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
          guests: this.guests,
          notes: this.notes,
        };

        this.createBooking(booking);
      },

      createBooking(booking) {
        // console.log('createBooking', booking);
        this.loading(true, true);
        axios.post(this.route('api.gatekeeper.temporary-access-bookings.store').url(), booking)
          .then((response) => {
            if (response.status == '201') { // HTTP_CREATED
              const responseBooking = this.mapBookings(response.data.data);

              $(this.$refs.bookingModal).modal('hide');
              this.removeBookingConfirmation();
              this.calendarApi.unselect();
              const event = this.calendarApi.getEventById(responseBooking.id);
              if (! event) { // make sure the event has not already been added via newBookingEvent (toOthers() should have fixed this)
                // update userByBuildingId.future
                if (responseBooking.userId == this.settings.userId) {
                  this.settings.userByBuildingId[this.building.id].futureCount += 1;

                  // check if we need to update latestBooking
                  if (this.settings.userByBuildingId[this.building.id].latestBookingId === null){
                    // latestBookingId was null ??, set this as it
                    this.settings.userByBuildingId[this.building.id].latestBookingId = responseBooking.id;
                    this.settings.userByBuildingId[this.building.id].latestBookingEnd = responseBooking.end;
                  } else if (moment(responseBooking.end).isAfter(moment(this.settings.userByBuildingId[this.building.id].latestBookingEnd))) {
                    // end is after latestBookingEnd, so now this responseBooking is the latest
                    this.settings.userByBuildingId[this.building.id].latestBookingId = responseBooking.id;
                    this.settings.userByBuildingId[this.building.id].latestBookingEnd = responseBooking.end;
                  } else {
                    // should only get here if you have grant ALL
                  }
                }

                this.calendarApi.addEvent(responseBooking, 'bookings');
              }

              if (responseBooking.approved) {
                flash('Booking created', 'success');
              } else {
                //TODO: show a bigger alert to let the user know what happens next
                flash('Booking requested', 'success');
              }

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

        axios.patch(this.route('api.gatekeeper.temporary-access-bookings.update', event.id).url(), booking)
          .then((response) => {
            if (response.status == '200') { // HTTP_OK
              const responseBooking = this.mapBookings(response.data.data);
              flash('Booking updated', 'success');
              console.log('patchBooking', 'Booking Updated OK');
              // think patch does not need anything doing to confirm

              // is this our booking
              if (responseBooking.userId == this.settings.userId) {
                // check if we need to update latestBooking
                if (this.settings.userByBuildingId[this.building.id].latestBookingId){
                  // latestBookingId is set, now does it match this booking?
                  if (responseBooking.id == this.settings.userByBuildingId[this.building.id].latestBookingId) {
                    this.settings.userByBuildingId[this.building.id].latestBookingEnd = responseBooking.end;
                  }
                } else {
                  // latestBookingId was null ??
                  this.settings.userByBuildingId[this.building.id].latestBookingId = responseBooking.id;
                  this.settings.userByBuildingId[this.building.id].latestBookingEnd = responseBooking.end;
                }
              }
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

      cancelBooking(event, reason=null) {
        // console.log('cancelBooking', event, reason);

        let payload = {}
        if (reason) {
          payload.reason = reason;
          this.loading(true, true);
        } else {
          this.loading(true);
        }

        axios.delete(this.route('api.gatekeeper.temporary-access-bookings.destroy', event.id).url(), {
          data: payload,
        })
          .then((response) => {
            if (response.status == '204') { // HTTP_NO_CONTENT
              flash('Booking cancelled', 'success');
              // console.log('cancelBooking', 'Booking deleted');

              const foundEvent = this.calendarApi.getEventById(event.id);
              if (foundEvent) { // make sure the event has not already been removed via bookingCancelledEvent
                // update userByBuildingId.future
                if (event.extendedProps.userId == this.settings.userId) {
                  this.settings.userByBuildingId[this.building.id].futureCount -= 1;
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
            this.removeReasonModal();
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

      approveBooking(event) {
        let booking = {
          start: moment(event.start).toISOString(true),
          end: moment(event.end).toISOString(true),
          approve: true,
        };

        this.loading(true);

        axios.patch(this.route('api.gatekeeper.temporary-access-bookings.update', event.id).url(), booking)
          .then((response) => {
            if (response.status == '200') { // HTTP_OK
              const responseBooking = this.mapBookings(response.data.data);
              flash('Booking approved', 'success');
              console.log('approveBooking', 'Booking Updated OK');

              // replace the event to update the editable, style and props etc
              const event = this.calendarApi.getEventById(responseBooking.id);
              if (event) {
                event.remove();
              }
              this.calendarApi.addEvent(responseBooking, 'bookings');

            } else {
              flash('Error approving booking', 'danger');
              console.log('approveBooking', response.data);
              console.log('approveBooking', response.status);
              console.log('approveBooking', response.statusText);
            }

            this.loading(false);
          })
          .catch((error) => {
            flash('Error approving booking', 'danger');
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
              // else if HTTP_CONFLICT to many bookings or over lap
              // else if HTTP_FORBIDDEN on enough permissions
              console.log('approveBooking: Response error', error.response.data, error.response.status, error.response.headers);
            } else if (error.request) {
              // The request was made but no response was received
              // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
              // http.ClientRequest in node.js
              console.error('approveBooking: Request error', error.request);
            } else {
              // Something happened in setting up the request that triggered an Error
              console.error('approveBooking: Error', error.message);
            }

            this.loading(false);
          });

      },

      rejectBooking(event) {

      },

      /**
       * Display modal for a new selection.
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
        this.guests = 0;
        this.notes = '';
        this.userError = false;
        this.startError = false;
        this.endError = false;
        this.bookableAreaError = false;
        this.guestsError = false;

        this.calendarApi.unselect();
      },

      /**
       * Display bootstrap confirmation popover for event click.
       */
      setupApproveRejcectCancleConfirmation(info) {
        // console.log('setupApproveRejcectCancleConfirmation', info.el);
        const self = this;


        $(info.el).tooltip('hide');
        // info.el attach booking-selected-id class to the <a>
        $(info.el).addClass('booking-selected');
        // $(info.el).tooltip('disable');

        // basic (yes | no) cancel
        let options = {
          container: 'body',
          rootSelector: '.booking-selected',
          title: 'Would you like to cancel this booking?',
          popout: true,
          singleton: true,
          btnOkClass: 'btn btn-sm btn-danger',
          btnCancelClass: 'btn btn-sm btn-outline-dark',

          onCancel() {
            $(info.el).removeClass('booking-selected');
          },
        };

        // base dismiss button
        let buttons = [
          {
            label: '&nbsp;Decide later&nbsp;',
            class: 'btn btn-sm btn-outline-dark',
            iconClass: 'fas fa-times',
            cancel: true,
          },
        ];

        // template buttons to be added as needed
        let approveButton = {
          label: '&nbsp;Approve',
          // value: 'NORMAL',
          class: 'btn btn-sm btn-primary',
          iconClass: 'fas fa-thumbs-up',
          onClick() {
            $(info.el).removeClass('booking-selected');
            self.approveBooking(info.event);
          },
        };

        let rejectButton = {
          label: '&nbsp;Reject',
          // value: 'NORMAL',
          class: 'btn btn-sm btn-red',
          iconClass: 'fas fa-thumbs-down',
          onClick() {
            $(info.el).removeClass('booking-selected');
            self.setupRejectModal(info.event);
          },
        };

        let cancelButton = {
          label: '&nbsp;Cancel',
          // value: 'NORMAL',
          class: 'btn btn-sm btn-red',
          iconClass: 'fas fa-trash',
          onClick() {
            $(info.el).removeClass('booking-selected');
            self.cancelBooking(info.event);
          },
        };

        let cancelWithReasonButton = {
          label: '&nbsp;Cancel',
          // value: 'NORMAL',
          class: 'btn btn-sm btn-red',
          iconClass: 'fas fa-trash',
          onClick() {
            $(info.el).removeClass('booking-selected');
            self.setupCancelWithReasonModal(info.event);
          },
        };

        if (this.settings.grant == "ALL") {
          // we have grant.all
          if (info.event.extendedProps.userId == this.settings.userId) {
            // we own this booking so ([approve] | cancel | x)
            buttons.splice(0, 0, cancelButton);
            if (! info.event.extendedProps.approved) {
              // not approved so add approve button
              options.title = 'Would you like to approve or cancel this booking?';
              buttons.splice(0, 0, approveButton);
            }
          } else {
            // we don't own this booking so (approve | reject | x) or ( cancelWithReason | x)
            options.content = 'Notes: ' + info.event.extendedProps.notes;
            if (! info.event.extendedProps.approved) {
              // approve | reject | x
              options.title = 'Would you like to approve or reject this booking?';
              buttons.splice(0, 0, rejectButton);
              buttons.splice(0, 0, approveButton);
            } else {
              // cancelWithReason | x
              buttons.splice(0, 0, cancelWithReasonButton);
            }
          }
          options.buttons = buttons;
        } else {
          // we dont have grant.all and this is our booking (checked in eventClick)
          // just use the basic (yes | no) cancel
          options.onConfirm = ((type) => {
            $(info.el).removeClass('booking-selected');
            self.cancelBooking(info.event);
          });
        }

        $('.booking-selected').confirmation(options);
        $('.booking-selected').on('hidden.bs.confirmation', function () {
          $(info.el).removeClass('booking-selected');
          $(info.el).tooltip('enable');
        });

        $('.booking-selected').confirmation('show');
      },

      /**
       * Remove the previous bootstrap confirmation popover.
       */
      removePopoverConfirmation() {
        $('.popover').remove();
      },

      setupRejectModal(event) {
        console.log('setupRejectModal', event);
        this.rejectOrCancel = true;
        this.rejectOrCancelEvent = event;
        this.reason = '';

        $(this.$refs.reasonModal).modal('show');
      },

      setupCancelWithReasonModal(event) {
        console.log('setupCancelWithReasonModal', event);
        this.rejectOrCancel = false;
        this.rejectOrCancelEvent = event;
        this.reason = '';

        $(this.$refs.reasonModal).modal('show');
      },

      reasonSubmit() {
        console.log('reasonSubmit');

        if (this.reason == '') {
          this.reasonError = 'You must give a reason.'
          return;
        }

        this.cancelBooking(this.rejectOrCancelEvent, this.reason);
      },

      removeReasonModal() {
        this.rejectOrCancelEvent = null;
        this.reason = '';
        $(this.$refs.reasonModal).modal('hide');
      },

      /********************************************************/
      /**
       * Attached to 'resize' event so we can make the view responsive.
       */
      calendarWindowResize(event) {
        const windowWidth = document.documentElement.clientWidth;

        if (this.calendarApi !== null) {
          if (windowWidth < 767.98) {
            this.calendarApi.setOption('header', this.dayOnlyButtons);
            this.calendarApi.setOption('footer', this.dayOnlyButtons);
            this.calendarApi.setOption('aspectRatio', this.dayAspectRatio);
            this.calendarApi.changeView('timeGridDay');
          } else {
            this.calendarApi.setOption('header', this.dayMonthButtons);
            this.calendarApi.setOption('footer', this.dayMonthButtons);
            this.calendarApi.setOption('aspectRatio', this.weekAspectRatio);
            // this.calendarApi.changeView('timeGridWeek');
          }
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

        if (this.settings.grant != 'NONE') {
          // we have some level of grant
          if (moment().diff(booking.start) > 0
            && moment().diff(booking.end) < 0) {
            // this is a booking under now
            if (this.settings.grant == 'ALL') {
              booking.durationEditable = true;
            } else if (booking.userId == this.settings.userId) {
              // its ours
              if (booking.approved == false || (booking.approved && booking.approvedById == this.settings.userId)) {
                // and was automatically approved
                booking.durationEditable = true;
              }
            }
          } else if (moment().diff(booking.start) < 0) {
            // this booking is in the future
            if (this.settings.grant == 'ALL') {
              booking.editable = true;
            } else if (booking.userId == this.settings.userId) {
              // its ours
              if (booking.approved == false || (booking.approved && booking.approvedById == this.settings.userId)) {
                // and was automatically approved
                booking.editable = true;
              }
            }
          }
        }

        // add the class if not editable
        if (! (booking.editable || booking.durationEditable)) {
          booking.className += ' not-editable';
        }

        booking.title = 'Visitors: ' + (1 + booking.guests);

        if (booking.userName) {
          booking.title = booking.userName + " plus " + booking.guests + (booking.guests == 2 ? ' guest' : ' guests');
        }

        return booking;
      },

      /* ECHO *************************************************/
      echoInit() {
        Echo.channel('gatekeeper.temporaryAccessBookings.' + this.building.id)
          .listen('Gatekeeper.NewBooking', this.newBookingEvent)
          .listen('Gatekeeper.BookingChanged', this.bookingChangedEvent)
          .listen('Gatekeeper.BookingCancelled', this.bookingCancelledEvent)
          .listen('Gatekeeper.BookingApproved', this.bookingApprovedEvent)
          .listen('Gatekeeper.BookingRejected', this.bookingRejectedEvent);
      },

      echoDeInit() {
        Echo.leave('gatekeeper.temporaryAccessBookings.' + this.building.id);
      },

      newBookingEvent(newBooking) {
        // console.log('Echo sent newBooking event', newBooking);
        const booking = this.mapBookings(newBooking.booking);
        const event = this.calendarApi.getEventById(booking.id);
        if (event) { // make sure the event has not already been added via createBooking
          return;
        }

        // does this booking belong to us
        if (booking.userId == this.settings.userId) {
          // update userByBuildingId.future
          this.settings.userByBuildingId[this.building.id].futureCount += 1;

          // it will have been anonymized so let fill in our name
          booking.title = booking.userName + " plus " + booking.guests + (booking.guests == 2 ? ' guest' : ' guests');
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

        // does this booking belong to us
        if (booking.userId == this.settings.userId) {
          // it will have been anonymized so let fill in our name
          booking.title = booking.userName + " plus " + booking.guests + (booking.guests == 2 ? ' guest' : ' guests');
        }

        this.calendarApi.addEvent(booking, 'bookings');
      },

      bookingCancelledEvent(bookingCancelled) {
        // console.log('Echo sent bookingCancelled event', bookingCancelled);
        const event = this.calendarApi.getEventById(bookingCancelled.bookingId);
        if (event) {
          // update userByBuildingId.future
          if (event.extendedProps.userId == this.settings.userId) {
            this.settings.userByBuildingId[this.building.id].futureCount -= 1;
          }

          event.remove();
        }
      },

      bookingApprovedEvent(bookingApproved) {
        // console.log('Echo sent bookingApproved event', bookingApproved);
        const oldEvet = this.calendarApi.getEventById(bookingApproved.booking.id);
        if (oldEvet) {
          oldEvet.remove();
        }

        const booking = this.mapBookings(bookingApproved.booking);

        // does this booking belong to us
        if (booking.userId == this.settings.userId) {
          // it will have been anonymized so let fill in our name
          booking.title = booking.userName + " plus " + booking.guests + (booking.guests == 2 ? ' guest' : ' guests');
        }

        this.calendarApi.addEvent(booking, 'bookings');
      },

      bookingRejectedEvent(bookingRejected) {
        // console.log('Echo sent bookingRejected event', bookingRejected);
        const event = this.calendarApi.getEventById(bookingRejected.bookingId);
        if (event) {
          // update userByBuildingId.future
          if (event.extendedProps.userId == this.settings.userId) {
            this.settings.userByBuildingId[this.building.id].futureCount -= 1;
          }

          event.remove();
        }
      },
      /* ECHO END *********************************************/

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
      checkBuildingLimits(initialStart, initialEnd, draggedEvent = null) {
        let start = moment(initialStart);
        let end = moment(initialEnd);
        let fiftenMinutes = moment.duration(15, 'minutes');
        let draggedEventId = draggedEvent ? draggedEvent.id : null;
        let draggedGuests = draggedEvent ? draggedEvent.extendedProps.guests : 0;

        console.log('checkBuildingLimits', start.toISOString(), end.toISOString(), draggedEventId, draggedGuests);

        let events = this.calendarApi.getEvents(); // all the current events in the calender
        events = events.filter(event => event.id != draggedEventId);

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
          // check filteredEvents counts + booking guests + this.guests vs selfBookMaxOccupancy
          if (this.calculateOccupancyForBookings(filteredEvents) + this.guests + draggedGuests >= this.building.selfBookMaxOccupancy) {
            result = 'Maximum building concurrent occupancy limit is ' + this.building.selfBookMaxOccupancy + '.';
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
      checkClashByBookalbeArea(initialStart, initialEnd, bookableAreaId, draggedEvent = null) {
        let start = moment(initialStart);
        let end = moment(initialEnd);
        let fiftenMinutes = moment.duration(15, 'minutes');
        let draggedEventId = draggedEvent ? draggedEvent.id : null;
        let draggedGuests = draggedEvent ? draggedEvent.extendedProps.guests : 0;

        console.log('checkClashByBookalbeArea', start.toISOString(), end.toISOString(), bookableAreaId, draggedEventId, draggedGuests);

        // need to check for overlap with other events for this.bookableAreaId
        if (bookableAreaId == '') {
          // no area selected
          return false;
        }

        // we want to grab the bookableArea from our data array
        let selectedBookableArea = this.bookableAreas.find(bookableArea => bookableArea.id == bookableAreaId)


        let events = this.calendarApi.getEvents(); // all the current events in the calender
        // filter events by this bookable area
        events = events.filter(event => (event.extendedProps.bookableArea && event.extendedProps.bookableArea.id) == selectedBookableArea.id);
        events = events.filter(event => event.id != draggedEventId);

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


          if (selectedBookableArea.maxOccupancy == 1 && selectedBookableArea.additionalGuestOccupancy != 0) {
            if (this.calculateOccupancyForBookings(filteredEvents) != 0 || this.guests + draggedGuests > selectedBookableArea.additionalGuestOccupancy) {
              result = 'Area maximum concurrent occupancy limit is ' + selectedBookableArea.maxOccupancy + '.';
              break;
            }
            // allowed, me + number of guest is less than me + additionalGuestOccupancy
          } else if (this.calculateOccupancyForBookings(filteredEvents) + this.guests + draggedGuests >= selectedBookableArea.maxOccupancy) { // check filteredEvents counts + booking guests + this.guests vs maxOccupancy
            result = 'Area maximum concurrent occupancy limit is ' + selectedBookableArea.maxOccupancy + '.';
            break;
          }

          // adjust start for next loop
          start.add(fiftenMinutes)
        } while (start.isBefore(end));

        return result;
      },

      udpateStartMinDate() {
        // start min date
        // need to check how long since last booking
        let latestBookingEnd = this.settings.userByBuildingId[this.building.id].latestBookingEnd;
        let nextStart = latestBookingEnd && moment(latestBookingEnd).add(this.settings.minPeriodBetweenBookings, 'minutes');

        if (this.settings.grant != 'ALL'
          && nextStart
          && nextStart.isAfter(moment())
        ) {
          // console.log('minStart: ', nextStart.toISOString())
          $(this.$refs.datetimepickerstart).data('DateTimePicker').minDate(nextStart);
        } else {
          // console.log('minStart: now+15')
          $(this.$refs.datetimepickerstart).data('DateTimePicker').minDate(moment().add(15, 'minutes'));
        }
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

      /**
       * Little helper to sum the number of bookings and there guests.
       *
       * @param  {Array} bookings
       * @return {number}
       */
      calculateOccupancyForBookings(bookings) {
        // console.log('calculateOccupancyForBookings', bookings);

        let guests = bookings.reduce(function (sum, booking) {
          return sum + booking.extendedProps.guests;
        }, 0);

        return bookings.length + guests;
      },

      /* SELECT TWO AREA FORMAT *******************************/
      formatBookableArea(bookableArea) {
        if (bookableArea.id === '') {
          // adjust for custom placeholder values
          return bookableArea.text;
        }

        let markup = '<span class="badge badge-' + bookableArea.bookingColor + ' ta-badge-font-inherit">' + bookableArea.name + '</span>';

        return $(markup);
      },

      formatBookableAreaSelection(bookableArea) {
        if (bookableArea.id === '') {
          // adjust for custom placeholder values
          return bookableArea.text;
        }

        return this.formatBookableArea(bookableArea);
      },
    }, // end of methods

    created() {
      let uri = window.location.search;
      let params = new URLSearchParams(uri);
      // console.log(params.get('date'));

      if (params.get('date') && moment(params.get('date'), moment.ISO_8601).isValid()) {
        this.initialDate = params.get('date');
      }

      if (params.get('view')) {
        switch (params.get('view')) {
          case 'day':
            this.initialView = 'timeGridDay';
            break;
          case 'week':
            this.initialView = 'timeGridWeek';
            break;
        }
      }
    },

    mounted() {
      this.$nextTick(function() {
        // remove unwanted element
        $('.vue-remove').contents().unwrap();

        window.addEventListener('resize', this.calendarWindowResize);

        //Init
        this.udpateStartMinDate();
        this.calendarWindowResize();
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
        if (this.initialDate) {
          scrollTime = moment(this.initialDate, moment.ISO_8601).format('HH:mm');
        } else if (moment().isAfter(moment('06:00', 'HH:mm'), 'hour')) {
          scrollTime = moment().format('HH') + ':00:00'
        }

        this.calendarApi.scrollToTime(scrollTime);
      }, 100);

      // Call refetchEventsevery 15 minutes, so past events are shaded
      this.interval = setInterval(function () {
        // TODO: once we have Echo running only really need to call this if there is an event under now
        this.calendarApi.refetchEvents();
        this.removePopoverConfirmation();

        this.udpateStartMinDate();

      }.bind(this), 900000);

      this.echoInit();

      $(this.$refs.bookingModal).modal('handleUpdate');
      // need to setup close event to unselected
      $(this.$refs.bookingModal).on('hidden.bs.modal', this.removeBookingConfirmation);
      $(this.$refs.reasonModal).modal('handleUpdate');
      $(this.$refs.reasonModal).on('hidden.bs.modal', this.removeReasonModal);
    },

    beforeDestroy() {
      clearInterval(this.interval);
      window.removeEventListener('resize', this.calendarWindowResize);
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

.ta-badge-font-inherit {
  font-size: inherit
}

.fc-mirror {
  background-color: rgba(55, 136, 216, 0.60)
}
</style>
