<template>
  <div class="vue-remove">
    <button type="button" class="btn btn-primary" :class="{ 'btn-sm mb-1': small }" @click="showModal"><i class="fas fa-plus" :class="{ 'fa-lg': !small }" aria-hidden="true"></i> Add User{{small ? '' : ' To Team' }}</button>

    <!-- Modal -->
    <div ref="selectModal" class="modal fade" :id="$id('addUserToTeamModal')" tabindex="-1" :aria-labelledby="$id('addUserToTeamLabel')" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" method="POST" :action="actionUrl">
          <input type="hidden" name="_token" :value="csrf">
          <input type="hidden" name="_method" value="PATCH">
          <div class="modal-header">
            <h5 class="modal-title" :id="$id('addUserToTeamLabel')">Add User to {{ roleName }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <member-select-two :current-only="true"></member-select-two>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="sumbit" class="btn btn-primary">Add User</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
  export default {

    props: {
      roleId: Number,
      roleName: String,
      small: false,
    },

    data() {
      return {
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      }
    },

    computed: {
      actionUrl() {
        return "/teams/" + this.roleId + "/users";
      },
    }, // end of computed

    methods: {
      showModal() {
        console.log('show');
        $(this.$refs.selectModal).modal('toggle');
      },
    },

    mounted() {
      this.$nextTick(function () {
        // remove unwanted element all other is work jQuery required
        $('.vue-remove').contents().unwrap();
      });

      $(this.$refs.selectModal).modal('handleUpdate');

    }
  }
</script>
