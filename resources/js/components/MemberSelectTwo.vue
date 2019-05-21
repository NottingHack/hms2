<template>
  <div ref="selectDiv">
    <select-two
      v-model="value"
      :name="name"
      :placeholder="placeholder"
      :settings="mySettings"
      @change="myChangeEvent"
      @select="mySelectEvent"
      style="width: 100%"
      />
  </div>
</template>

<script>
  export default {
    props: {
      // set to null if you don't need us return a form param
      name: {
       type: String,
       default: 'user_id'
      },
      placeholder: {
        type: String,
        default: 'Search for a member...'
      },
      // Search for only users with an account
      withAccount: Boolean,
      // Return Account ID instead of User ID
      returnAccountId: Boolean,
    },

    model: {
      event: 'change',
      prop: 'value'
    },

    data() {
      return {
        value: '',
      }
    },

    computed: {
      mySettings() {
        const self = this;
        return {
          width: '100%',
          placeholder: this.placeholder,
          dropdownParent: this.$refs.selectDiv,
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
              withAccount: self.withAccount,
              page: params.page
            };
          },
          processResults: function (data, params) {
            // need to need to have a .text value for display
            if (self.returnAccountId) {
              // and use the account id as the select value
              data.data = $.map(data.data, function (obj) {
                      obj.id = obj.accountId;
                      obj.text = obj.fullname;
                      return obj;
              });
            } else {
              data.data = $.map(data.data, function (obj) {
                      obj.text = obj.fullname;
                      return obj;
              });
            }
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
      myChangeEvent(val) {
        // console.log(val);
        this.$emit('change', val);
      },

      mySelectEvent({id, text}) {
        // console.log({id, text})
        this.$emit('select', {id, text});
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
  }
</script>
