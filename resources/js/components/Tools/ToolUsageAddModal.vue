<template>
  <div class="vue-remove">
    <div class="btn-group float-right" role="group">
      <button type="button" class="btn btn-primary" :class="{ 'btn-sm mb-1': small, 'btn-block': block}" @click="showModal"><i class="fas fa-plus" :class="{ 'fa-lg': !small }" aria-hidden="true"></i></button>
    </div>
    <!-- Modal -->
    <div ref="addModal" class="modal fade" tabindex="false" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" method="POST" :action="actionUrl">
          <input type="hidden" name="_token" :value="csrf">
          <input type="hidden" name="user_id" :value="userId">
          <div class="modal-header">
            <h5 class="modal-title">Add time on the '{{ toolName }}' for {{ userName }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label :for="$id('hours')">Hours</label>
              <input type="number" class="form-control" name="hours">
            </div>
            <div class="form-group">
              <label :for="$id('minutes')">Minutes</label>
              <input type="number" class="form-control" name="minutes">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="sumbit" class="btn btn-primary">Add free time</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
  export default {

    props: {
      toolId: Number,
      toolName: String,
      userId: String,
      userName: String,
      small: false,
      block: false,
    },

    data() {
      return {
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      }
    },

    computed: {
      actionUrl() {
        return this.route('tools.add-free-time', this.toolId);
      },
    }, // end of computed

    methods: {
      showModal() {
        $(this.$refs.addModal).modal('toggle');
      },
    },

    mounted() {
      this.$nextTick(function () {
        // remove unwanted element all other is work jQuery required
        $('.vue-remove').contents().unwrap();
      });

      $(this.$refs.addModal).modal('handleUpdate');

    }
  }
</script>
