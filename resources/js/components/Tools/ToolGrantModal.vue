<template>
  <div class="vue-remove">
    <button type="button" class="btn btn-primary" :class="{ 'btn-sm mb-1': small, 'btn-block': block}" @click="showModal"><i class="fas fa-plus" :class="{ 'fa-lg': !small }" aria-hidden="true"></i> Appoint {{ grantTypeString }}</button>

    <!-- Modal -->
    <div ref="selectModal" class="modal fade" :id="$id('toolGrantModal')" tabindex="false" role="dialog" :aria-labelledby="$id('toolGrantLabel')" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" method="POST" :action="actionUrl">
          <input type="hidden" name="_token" :value="csrf">
          <input type="hidden" name="_method" value="PATCH">
          <input type="hidden" name="grantType" :value="grantType">
          <div class="modal-header">
            <h5 class="modal-title" :id="$id('toolGrantLabel')">Appoint {{ grantTypeString }} for {{ toolName }}</h5>
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
            <button type="sumbit" class="btn btn-primary">Appoint {{ grantTypeString }}</button>
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
      grantType: String,
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
        return "/tools/" + this.toolId + "/grant";
      },
      grantTypeString() {
        return this.grantType.charAt(0).toUpperCase() +
           this.grantType.slice(1).toLowerCase();
      },
    }, // end of computed

    methods: {
      showModal() {
        console.log('show');
        // console.log($(this.el).find('#addUserToTeamModal').modal('toggle'));
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
