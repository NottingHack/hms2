<template>
  <div class="container">
    <template v-for="row in locationGrid">
      <div class="row">
        <div class="col">
          <div class="card-group">
            <template v-for="location in row">
              <div class="card text-center mb-3">
                <h5 class="card-header">{{ location.name }}</h5>
                <div class="card-body" >
                  <p>{{ location.product.shortDescription }}<br><small v-if="location.product.price" class="money">({{ location.product.price }})</small></p>
                  <button type="button" class="btn btn-sm btn-primary" @click="showModal(location)"><i class="fas fa-pencil fa-sm"></i></button>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>
    </template>

    <!-- Modal -->
    <div ref="selectModal" class="modal fade" :id="$id('changeLocationModal')" tabindex="-1" :aria-labelledby="$id('changeLocationLabel')" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content vld-parent" ref="selectModalContent">
          <div class="modal-header">
            <h5 class="modal-title" :id="$id('changeLocationLabel')">Select product for location {{ editingLocation ? editingLocation.name : ''}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <select-two
              v-model="selectedProduct"
              :options="products"
              :name="null"
              :settings="mySettings"
              style="width: 100%"/>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" @click="saveLocation">Save</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
  import Loading from 'vue-loading-overlay';
  Vue.use(Loading);

  export default {
    props: {
      assignUrl: String,
      initialLocations: {
        type: Array,
        default: () => []
      },
      products: {
        type: Array,
        default: () => []
      },
    },

    data() {
      return {
        locations: [...this.initialLocations],
        editingLocation: null,
        selectedProduct: null,
      }
    },

    computed: {
      locationGrid() {
        var ls = [];
        this.locations.forEach(function (location, index) {
          if (location.name[1] == '1') {
            // push a new row
            ls.push([]);
          }

          ls[ls.length - 1].push(location);
        });
        return ls
      },

      mySettings() {
        const self = this;
        return {
          width: '100%',
        };
      },
    },

    methods: {
      showModal(location) {
        this.editingLocation = location;
        this.selectedProduct = this.editingLocation.product.id;
        $(this.$refs.selectModal).modal('show');
      },

      saveLocation() {
        let loader = this.$loading.show({
          container: this.$refs.selectModalContent,
          color: '#195905',
        });

        let product = {
          vendingLocationId: this.editingLocation.id,
          productId: parseInt(this.selectedProduct),
        };

        axios.patch(this.assignUrl, product)
          .then((response) => {
            if (response.status == '200') { // HTTP_OK
              flash('Location ' + this.editingLocation.name + ' Updated OK');
              console.log('saveLocation', 'Location ' + this.editingLocation.name + ' Updated OK');
              console.log(response.data);
              // update this.locations on success
              let index = this.locations.findIndex(location => location.id === response.data.id);
              this.$set(this.locations, index, response.data);

            } else {
              flash('Error updating location', 'danger');
              revert();
              console.log('saveLocation', response.data);
              console.log('saveLocation', response.status);
              console.log('saveLocation', response.statusText);
            }

            // dismiss the modal
            $(this.$refs.selectModal).modal('hide');
            loader.hide();
          })
          .catch((error) => {
            flash('Error updating location', 'danger');
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us

              // else if HTTP_FORBIDDEN on enough permissions
              console.log('saveLocation: Response error', error.response.data, error.response.status, error.response.headers);
            } else if (error.request) {
              // The request was made but no response was received
              // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
              // http.ClientRequest in node.js
              console.error('saveLocation: Request error', error.request);
            } else {
              // Something happened in setting up the request that triggered an Error
              console.error('saveLocation: Error', error.message);
            }

            loader.hide();
          });
      },
    },

    mounted() {
      $(this.$refs.selectModal).modal('handleUpdate');
    }
  }
</script>
