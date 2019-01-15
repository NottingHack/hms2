<template>
  <div>
    <button type="button" class="btn btn-primary" @click="showModal"><i class="fas fa-plus fa-lg" aria-hidden="true"></i> Add User To Team</button>

    <!-- Modal -->
    <div ref="selectModal" class="modal fade" id="addUserToTeamModal" tabindex="false" role="dialog" aria-labelledby="addUserToTeamlLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" method="POST" :action="actionUrl">
          <input type="hidden" name="_token" :value="csrf">
          <input type="hidden" name="_method" value="PATCH">
          <input type="hidden" name="user_id" :value="myValue">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add User to {{ roleName }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <select-two v-model="myValue" :placeholder="placeholder" :settings="mySettings" @change="myChangeEvent($event)" @select="mySelectEvent($event)" style="width: 100%"></select-two>
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
        placeholder: "Search for a member...",
        myValue: '',
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      }
    },

    computed: {
      actionUrl() {
        return "team/" + this.roleId + "/users";
      },

      mySettings() {
        const self = this;
        return {
          width: '100%',
          placeholder: this.placeholder,
          dropdownParent: this.$refs.selectModal,
          minimumInputLength: 1,
          ajax: this.ajax,
          templateResult: this.formatUser,
          templateSelection: this.formatUserSelection,
        };
      },

      ajax() {
        const self = this
        return {
          url: '/api/search/users',
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              withAccount: true,
              page: params.page
            };
          },
          processResults: function (data, params) {
            // need to need to have a .text value for display
            // and use the account id as the select value
            data.data = $.map(data.data, function (obj) {
                    obj.text = obj.fullname;
                    return obj;
            });

            // indicate that infinite scrolling can be used
            params.page = params.page || 1;

            return {
              results: data.data,
              pagination: {
                more: (params.page * data._per_page) < data.total
              }
            };
          },
          cache: true
        };
      },
    }, // end of computed

    methods: {
      showModal() {
        console.log("show");
        // console.log($(this.el).find('#addUserToTeamModal').modal('toggle'));
        $(this.$refs.selectModal).modal('toggle');
      },

      myChangeEvent(val) {
        console.log(val);
      },

      mySelectEvent({id, text}) {
        console.log({id, text})
      },

      formatUser (user) {
        if (user.loading) return user.text;
        var markup = $('<div class="membersearch">' +
          '<div class="name"><span class="fullname">' + user.fullname + '</span> <span class="username">' + user.username + '</span></div>' +
          '<div class="email">' + user.email + '</div>' +
          '<div class="address">' + user.address1 + ', ' + user.addressPostcode + '</div>' +
          '<div class="paymentref">' + user.paymentRef + '</div>' +
          '</div>');
        return markup;
      },

      formatUserSelection (user) {
        return user.text;
      },

    },

    mounted() {
      $(this.$refs.selectModal).modal('handleUpdate');
    }
  }
</script>

<style lang="scss">

</style>
