<template>
  <div :class="[inputGroup, invalidStyle]">
    <slot name="prepend"></slot>
    <select
      ref="select"
      class="form-control"
      :id="id"
      :name="name"
      :disabled="disabled"
      :required="required"
      >
      <slot></slot>
    </select>
    <slot name="append"></slot>
    <div v-if="invalid" class="d-none form-control is-invalid"></div>
  </div>
</template>

<script>
  // Component originally from https://github.com/godbasin/vue-select2/
  import select2 from 'select2/dist/js/select2.full';

  export default {
    // name: 'SelectTwo',
    data() {
      return {
        select2: null
      };
    },

    model: {
      event: 'change',
      prop: 'value'
    },

    props: {
      id: {
        type: String,
        default: ''
      },

      name: {
        type: String,
        default: ''
      },

      placeholder: {
        type: String,
        default: ''
      },

      options: {
        type: Array,
        default: () => []
      },

      disabled: {
        type: Boolean,
        default: false
      },

      required: {
        type: Boolean,
        default: false
      },

      settings: {
        type: Object,
        default: () => {}
      },

      value: null,

      invalid: {
        type: Boolean,
        default: false
      },
    },

    computed: {
      inputGroup() {
          return !! this.$slots.prepend || !! this.$slots.append ? 'input-group' : '';
      },

      invalidStyle() {
        return this.invalid ? "is-invalid" : "";
      },
    },

    watch: {
      options(val) {
        this.setOption(val);
      },
      value(val) {
        // console.log('select2:value', val);
        this.setValue(val);
      }
    },

    methods: {
      setOption(val = []) {
        // console.log('select2:setOption', val);
        this.select2.empty();
        this.select2.select2({
          theme: 'bootstrap',
          containerCssClass: ':all:',
          placeholder: this.placeholder,
          ...this.settings,
          data: val
        });
        this.setValue(this.value);
      },
      setValue(val) {
        // console.log('select2:setValue', val);
        if (val instanceof Array) {
          this.select2.val([...val]);
        } else {
          this.select2.val([val]);
        }
        this.select2.trigger('change');
      }
    },

    mounted() {
      this.select2 = $(this.$el)
        .find('select')
        .select2({
          theme: 'bootstrap',
          containerCssClass: ':all:',
          placeholder: this.placeholder,
          ...this.settings,
          data: this.options
        })
        .on('select2:select select2:unselect', ev => {
          this.$emit('change', this.select2.val());
          this.$emit('select', ev['params']['data']);
        });
      this.setValue(this.value);
    },
    beforeDestroy() {
      this.select2.select2('destroy');
    }
  };
</script>
