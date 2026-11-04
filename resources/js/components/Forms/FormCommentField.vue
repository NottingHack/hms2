<template>
  <input class="form-control" :value="comment" @focusout="updateComment" />
</template>

<script>
  export default {
    props: {
      formResponseId: {
        type: String
      },
      comment: {
        type: String
      }
    },

    methods: {
      updateComment(sender, options) {
        let body = {
          comment: sender.target.value
        };

        axios.put(this.route('api.forms.comment', {'formResponse': this.formResponseId}), body)
             .then(() => {
               sender.target.classList.add('is-valid');
               setTimeout(() => {
                 sender.target.classList.remove('is-valid');
               }, 1000);
             });
      },
    },
  };
</script>
