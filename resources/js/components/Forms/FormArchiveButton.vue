<template>
  <button type="button" class="btn btn-primary btn-sm" @click="archive">Archive</button>
</template>

<script>
  export default {
    props: {
      formResponseId: {
        type: String
      }
    },

    methods: {
      archive(sender, options) {
        const row = this.$el.closest('tr');

        axios.put(this.route('api.forms.hide', {'formResponse': this.formResponseId}))
          .then(() => {
            if (row) {
              row.remove();
            }
          });
      }
    },
  };
</script>
