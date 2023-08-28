<template>
  <div>
    <select-two
      class="text-body"
      v-model="value"
      :name="name"
      :placeholder="placeholder"
      :settings="mySettings"
      @change="myChangeEvent"
      @select="mySelectEvent"
      style="width: 100%"
      :disabled="disabled"
      :invalid="invalid"
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
      // Search for only current members
      currentOnly: Boolean,
      disabled: Boolean,
      invalid: {
        type: Boolean,
        default: false
      },
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
              withAccount: self.withAccount ? 1 : 0,
              currentOnly: self.currentOnly ? 1 : 0,
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
                more: (params.page * data.per_page) < data.total
              }
            };
          },
          cache: true
        };
      },
    }, // end of computed

    methods: {
      clear() {
        this.value = ''
      },

      myChangeEvent(val) {
        // console.log('mst:myChangeEvent', val);
        this.$emit('change', val);
      },

      mySelectEvent(data) {
        // console.log('mst:mySelectEvent', data)
        this.$emit('select', data);
      },

      formatUser (user) {
        if (user.loading) return user.text;
        var markup = '<div class="membersearch">' +
          '<div class="name"><span class="fullname">' + user.fullname + '</span> <span class="username">' + user.username + '</span></div>' +
          '<div class="email">' + user.email + '</div>';

        if(user.address1) {
          markup += '<div class="address">' + user.address1 + ', ' + user.addressPostcode + '</div>';
        }

        if(user.discordUsername) {
          markup += '<div class="discordUsername">' + user.discordUsername + ' <i>on discord</i></div>';
        }

        markup += '<div class="paymentref">' + user.paymentRef + '</div>' +
          '</div>';
        return $(markup);
      },

      formatUserSelection (user) {
        return user.text;
      },

    },
  }
</script>
