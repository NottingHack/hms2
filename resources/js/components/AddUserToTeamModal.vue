<template>
  <div>
    <button type="button" class="btn btn-primary" @click="showModal"><i class="fas fa-plus fa-lg" aria-hidden="true"></i> Add User To Team</button>

    <!-- Modal -->
    <div ref="selectModal" class="modal fade" id="addUserToTeamModal" tabindex="false" role="dialog" aria-labelledby="addUserToTeamLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" method="POST" :action="actionUrl">
          <input type="hidden" name="_token" :value="csrf">
          <input type="hidden" name="_method" value="PATCH">
          <div class="modal-header">
            <h5 class="modal-title" id="addUserToTeamLabel">Add User to {{ roleName }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <member-select-two :with-account="true"></member-select-two>
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
    },

    data() {
      return {
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      }
    },

    computed: {
      actionUrl() {
        return "team/" + this.roleId + "/users";
      },
    }, // end of computed

    methods: {
      showModal() {
        console.log("show");
        // console.log($(this.el).find('#addUserToTeamModal').modal('toggle'));
        $(this.$refs.selectModal).modal('toggle');
      },
    },

    mounted() {
      $(this.$refs.selectModal).modal('handleUpdate');
    }
  }
</script>
