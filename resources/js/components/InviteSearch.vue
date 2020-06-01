<template>
  <form :id="$id('invite-search')" ref="search" role="form" method="POST" :action="actionUrl">
    <input type="hidden" name="_token" :value="csrf">
    <div class="form-group" ref="selectDiv">
      <label :for="$id('invite')">Search for an Invite to resend</label>
      <select-two
        :id="$id('invite')"
        v-model="value"
        :name="null"
        :placeholder="placeholder"
        :settings="mySettings"
        style="width: 100%"
        :disabled="disabled"
        />
    </div>
    <div class="form-group text-center">
      <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane" aria-hidden="true"></i> Resend Invite</button>
    </div>
  </form>
</template>

<script>
  export default {
    props: {
      placeholder: {
        type: String,
        default: 'Search for an invite...'
      },
      disabled: Boolean,
    },

    model: {
      event: 'change',
      prop: 'value'
    },

    data() {
      return {
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        value: '',
      }
    },

    computed: {
      actionUrl() {
        return this.route('membership.invites.resend', this.value);
      },

      mySettings() {
        const self = this;
        return {
          width: '100%',
          placeholder: this.placeholder,
          dropdownParent: this.$refs.selectDiv,
          minimumInputLength: 1,
          ajax: this.ajax,
        };
      },

      ajax() {
        const self = this
        return {
          url: '/api/search/invites',
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term, // search term
              page: params.page
            };
          },
          processResults: function (data, params) {
            // need to need to have a .text value for display
            data.data = $.map(data.data, function (obj) {
                    obj.text = obj.email;
                    return obj;
            });
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
  }
</script>
