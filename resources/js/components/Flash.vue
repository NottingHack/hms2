<template>
  <div class="alert-wrap" v-if="notifications.length > 0">
    <transition-group :name="transition" tag="div">
      <div :class="item.typeObject" role="alert" :key="item.id" v-for="item in notifications">
        <span v-if="displayIcons" :class="item.iconObject"></span> <span v-html="item.message"></span>
      </div>
    </transition-group>
  </div>
</template>

<script>
  /**
   * New imporved multi flash - LWK 04/06/2020
   * Thanks to laracasts and https://www.npmjs.com/package/vue-flash
   * Could not use the npm package as the scoped styles messed up mix extractVueStyles
   */
  export default {
    props: {
      timeout: {
        type: Number,
        default: 3000
      },
      transition: {
        type: String,
        default: 'slide-fade'
      },
      types: {
        type: Object,
        default: () => ({
          base:    'alert',
          success: 'alert-success',
          danger:  'alert-danger',
          warning: 'alert-warning',
          info:    'alert-info'
        })
      },
      displayIcons: {
        type: Boolean,
        default: false
      },
      icons: {
        type: Object,
        default: () => ({
          base:    'fa',
          danger:  'fa-exclamation-circle',
          success: 'fa-check-circle',
          info:    'fa-info-circle',
          warning: 'fa-exclamation-circle',
        })
      },
    },

    data() {
      return {
        notifications: [],
        lastMessage: null,
      };
    },

    /**
     * On creation Flash a message if a message exists otherwise listen for
     * flash event from global event bus
     */
    created() {
      window.events.$on(
        'flash', (message, type) => this.flash(message, type)
        );
    },

    methods: {
      /**
       * Flash our alert to the screen for the user to see
       * and begin the process to hide it
       *
       * @param message
       * @param type
       */
      flash(message, type = 'success') {
        if (this.lastMessage == message) {
          return;
        }
        this.lastMessage = message;
        this.notifications.push({
          id: Math.random().toString(36).substr(2, 9),
          message: message,
          type: type,
          typeObject: this.classes(this.types, type),
          iconObject: this.classes(this.icons, type)
        });
        setTimeout(this.hide, this.timeout);
      },

      /**
       * Sets and returns the values needed
       *
       * @param type
       */
      classes(propObject, type) {
        let classes = {};

        if (propObject.hasOwnProperty('base')) {
          classes[propObject.base] = true;
        }
        if (propObject.hasOwnProperty(type)) {
          classes[propObject[type]] = true;
        }

        return classes;
      },

      /**
       * Hide Our Alert
       *
       * @param item
       */
      hide(item = this.notifications[0]) {
        let key = this.notifications.indexOf(item);
        this.notifications.splice(key, 1);
        if (this.notifications.length == 0) {
          this.lastMessage = null;
        }
      }
    },
  }
</script>

<style lang="scss">
/*
 * Flash.vue
 */

.alert-wrap {
  position: fixed;
  right: 25px;
  bottom: 25px;
  z-index:9999;
}

/**
 * Fade transition styles
 */
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
  opacity: 0
}

/**
 * Bounce transition styles
 */
.bounce-enter-active {
  animation: bounce-in .5s;
}
.bounce-leave-active {
  animation: bounce-in .5s reverse;
}
@keyframes bounce-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.5);
  }
  100% {
    transform: scale(1);
  }
}

/**
 * Slide transition styles
 */
.slide-fade-enter-active {
  transition: all .3s ease;
}
.slide-fade-leave-active {
  transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-enter, .slide-fade-leave-to
  /* .slide-fade-leave-active for <2.1.8 */ {
  transform: translateX(10px);
  opacity: 0;
}
</style>
