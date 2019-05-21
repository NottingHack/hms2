<template>
  <form id='member-search' ref="search" role="form" method="get" :action="actionUrl">
    <member-select-two v-model="myValue" :name="null"></member-select-two>
  </form>
</template>

<script>
 export default {
    props: {
      action: String,
    },

    data() {
      return {
        myValue: '',
      }
    },

    computed: {
      actionUrl() {
        return this.action.replace("_ID_", this.myValue);
      },
    },

    watch: {
      actionUrl() {
        // use val to replace _ID_ in the action url and submit form
        // console.log(this.actionUrl);
        this.$nextTick(() => {
          this.$refs.search.submit();
        });
      },
    }
 }
</script>
