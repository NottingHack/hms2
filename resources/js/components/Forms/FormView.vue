<template>
  <Survey :survey="survey" />
</template>

<script>
  import { Survey, Model, StylesManager } from "survey-vue";
  import "survey-vue/modern.css";
  StylesManager.applyTheme("modern");

  export default {
    props: {
      formId: {
        type: String
      }
    },

    components: {
      Survey
    },

    data () {
      return {
        survey: null
      }
    },

    mounted() {
      this.loadModel();
    },

    methods: {
      async loadModel() {
        axios.get(this.route('api.forms.model', {'form': this.formId}))
         .then(response => {
           this.survey = new Model(response.data);
           this.survey.onComplete.add(this.submit)
         });
      },

      async submit(sender, options) {
        options.showSaveInProgress();

        axios.put(this.route('api.forms.respond', {'form': this.formId}), sender.data)
         .then(() => {
           options.showSaveSuccess();
         })
         .catch(error => {
           if (error.response.data?.errors[0]?.detail) {
             options.showSaveError(error.response.data.errors[0].detail);
           } else {
             options.showSaveError('An unknown error has occured.');
           }
         })
      }
    },
  };
</script>
