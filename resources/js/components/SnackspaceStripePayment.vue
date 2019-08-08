<template>
  <div class="vue-remove">
    <button type="button" class="btn btn-primary mb-1" @click="showModal"><i class="fas fa-plus" aria-hidden="true"></i> Add Money To Snackspace</button>

    <!-- Card Modal -->
    <div ref="cardModal" class="modal fade" id="addMoneyToSnackspaceModal" tabindex="false" role="dialog" aria-labelledby="addMoneyToSnackspaceLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addMoneyToSnackspaceLabel">Add Money To Snackspace</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div ref="modalBody" class="modal-body">
            <p>To add money to your snack space account please select an amount, then enter your card details.</p>
            <div class="form-group text-center">
              <div class="btn-group btn-group-toggle">
                <label :class="amount === 1000 ? 'btn btn-lg btn-success active': 'btn btn-lg btn-success'">
                  <input type="radio" v-model="amount" :value="1000" @change="updateAmount" :disabled="cardButtonDisable"> £10
                </label>
                <label :class="amount === 1500 ? 'btn btn-lg btn-success active': 'btn btn-lg btn-success'">
                  <input type="radio" v-model="amount" :value="1500" @change="updateAmount" :disabled="cardButtonDisable"> £15
                </label>
                <label :class="amount === 2000 ? 'btn btn-lg btn-success active': 'btn btn-lg btn-success'">
                  <input type="radio" v-model="amount" :value="2000" @change="updateAmount" :disabled="cardButtonDisable"> £20
                </label>
              </div>
            </div>

            <div ref="paymentRequestFG" class="form-group">
              <div ref="paymentRequestButton">
                <!-- A Stripe Element will be inserted here. -->
              </div>
            </div>

            <div class="form-group">
              <input v-model="cardholderName" id="cardholder-name" :class="['form-control', nameError ? 'is-invalid' : '']" type="text" placeholder="Card holder name" autocomplete="cc-name" :disabled="cardButtonDisable" @input="updateName">
              <div class="invalid-feedback" role="alert" v-if="nameError">{{ nameError }}</div>
            </div>

            <div class="form-group">
              <div ref="cardDiv" :class="['form-control', cardError ? 'is-invalid' : '']">
                <!-- A Stripe Element will be inserted here. -->
              </div>
              <div class="invalid-feedback" role="alert" v-if="cardError">{{ cardError }}</div>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-primary btn-block" :data-secret="clientSecret" :disabled="cardButtonDisable" @click="submitPayment">
              <i class="fal fa-credit-card" aria-hidden="true"></i> Submit Payment
            </button>
          </div>
        </div>
      </div>
    </div> <!-- Card Modal end -->

    <!-- Success Modal -->
    <div ref="successModal" class="modal fade" id="addMoneyToSnackspaceModal" tabindex="false" role="dialog" aria-labelledby="addMoneyToSnackspaceLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addMoneyToSnackspaceLabel">Add Money To Snackspace</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>{{ successMessage }}</p>
          </div>
          <div class="modal-footer">
            <p>Page will reload in {{ reloadTime }}</p>
          </div>
        </div>
      </div>
    </div>  <!-- Success Modal end -->

  </div>
</template>

<script>
  let stripe = Stripe(process.env.MIX_STRIPE_KEY),
    elements = stripe.elements();
  import Loading from 'vue-loading-overlay';
  Vue.use(Loading);

  export default{
    props: {
      // userId: Number,
    },

    data() {
      return {
        amount: 1000,
        cardholderName: '',
        cardElement: Object,
        paymentRequest: Object,
        prButton: Object,
        cardButtonDisable: true,
        prButtonDisable: true,
        intentId: null,
        clientSecret: null,
        isLoading: true,
        loader: null,
        cardError: null,
        nameError: null,
        successMessage: 'Payment successful, it may take a few minutes to show on your Snackspace balance.',
        reloadTime: 3,
      };
    },

    methods: {
      loading(isLoading) {
        this.isLoading = isLoading;
        if (isLoading && this.loader == null) {
          this.loader = this.$loading.show({
            container: this.$refs.modalBody,
            color: '#195905',
          });
        } else if (!isLoading && this.loader !== null) {
          this.loader.hide();
          this.loader = null;
        }
      },

      showModal() {
        $(this.$refs.cardModal).modal('toggle');

        if (this.clientSecret === null) {
          this.createIntent();
        }
      },

      modalHidden(event) {
        this.cardholderName = '';
        this.cardElement.clear();
        this.loading(false);
      },

      cardElementChange(event) {
        if (event.error) {
          this.cardError = event.error.message;
        } else {
          this.cardError = null;
        }
      },

      createIntent() {
        let intent = {
          amount: this.amount,
          type: 'SNACKSPACE',
        };

        this.loading(true);

        axios.post(this.route('api.stripe.make-intent'), intent)
          .then((response) => {
            if (response.status == '201') { // HTTP_CREATED
              // pull out pi and client secret
              //
              this.intentId = response.data.intentId;
              this.clientSecret = response.data.clientSecret;
              // enable buttons
              this.enableCard();
              this.cardButtonDisable = false;
              this.prButtonDisable = false;
            } else {
              flash('Error setting up payment', 'danger');
              console.log('createIntent', response.data);
              console.log('createIntent', response.status);
              console.log('createIntent', response.statusText);
            }
            this.loading(false);
          })
          .catch((error) => {
            flash('Error setting up payment', 'danger');
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
              // else if HTTP_CONFLICT to many bookings or over lap
              // else if HTTP_FORBIDDEN on enough permissions
              console.log('createIntent: Response error', error.response.data, error.response.status, error.response.headers);
            } else if (error.request) {
              // The request was made but no response was received
              // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
              // http.ClientRequest in node.js
              console.error('createIntent: Request error', error.request);
            } else {
              // Something happened in setting up the request that triggered an Error
              console.error('createIntent: Error', error.message);
            }

            // this.loading(false);
          });
      },

      updateIntent() {
        let intent = {
          intentId: this.intentId,
          amount: this.amount,
          type: 'SNACKSPACE',
        };

        this.loading(true);

        axios.post(this.route('api.stripe.update-intent'), intent)
          .then((response) => {
            if (response.status == '200') {
              // check amount is correct
              if (response.data.amount != this.amount) {
                // TODO: flag some error
              }
              // enable buttons
              this.enableCard();
              this.cardButtonDisable = false;
              this.prButtonDisable = false;
            } else {
              flash('Error setting up payment', 'danger');
              console.log('createIntent', response.data);
              console.log('createIntent', response.status);
              console.log('createIntent', response.statusText);
            }
            this.loading(false);
          })
          .catch((error) => {
            flash('Error setting up payment', 'danger');
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
              // else if HTTP_CONFLICT to many bookings or over lap
              // else if HTTP_FORBIDDEN on enough permissions
              console.log('createIntent: Response error', error.response.data, error.response.status, error.response.headers);
            } else if (error.request) {
              // The request was made but no response was received
              // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
              // http.ClientRequest in node.js
              console.error('createIntent: Request error', error.request);
            } else {
              // Something happened in setting up the request that triggered an Error
              console.error('createIntent: Error', error.message);
            }

            // this.loading(false);
          });
      },

      updateAmount() {
        // user selected a new amount update the payment intent
        // console.log('updateAmount: ' + this.amount);

        this.cardButtonDisable = true;

        this.updateIntent();

        // update the paymentRequest as with the new amount
        this.paymentRequest.update({
          total: {
            label: 'Snackspace',
            amount: this.amount, // < bind amount here
          },
        });
      },

      updateName() {
        this.nameError = null;
      },

      enableCard() {
        this.cardElement.update({
          disabled: false,
        });
      },

      disableCard() {
        this.cardElement.update({
          disabled: true,
        });
      },

      submitPayment() {
        if (!this.cardholderName) {
          this.nameError = 'Card holder name is required.';
          return;
        }

        this.loading(true);
        this.cardButtonDisable = true;
        this.cardError = null;
        this.disableCard();
        this.prButtonDisable = true;

        stripe.handleCardPayment(
          this.clientSecret, this.cardElement, {
            payment_method_data: {
              billing_details: {name: this.cardholderName}
            }
          }
        ).then((result) => {
          if (result.error) {
            // Display error.message in your UI.
            console.log('submitPayment: error');
            console.log(result.error);
            flash('Payment error: ' + result.error.message, 'warning');
            this.cardError = result.error.message;
            this.enableCard();
            this.cardElement.focus();
            this.prButtonDisable = false;
            this.cardButtonDisable = false;
            this.loading(false);
          } else {
            // The payment has succeeded. Display a success message.
            this.paymentSuccess(result);
          }
        });
      },

      submitPaymentRequest(event) {
        this.cardButtonDisable = true;
        this.cardError = null;
        this.disableCard();

        stripe.confirmPaymentIntent(this.clientSecret, {
          payment_method: event.paymentMethod.id,
        }).then((confirmResult) => {
          if (confirmResult.error) {
            // Report to the browser that the payment failed, prompting it to
            // re-show the payment interface, or show an error message and close
            // the payment interface.
            event.complete('fail');
          } else {
            // Report to the browser that the confirmation was successful, prompting
            // it to close the browser payment method collection interface.
            event.complete('success');
            // Let Stripe.js handle the rest of the payment flow.
            stripe.handleCardPayment(this.clientSecret).then((result) => {
              if (result.error) {
                // The payment failed -- ask your customer for a new payment method.
                flash('Payment error: ' + result.error.message, 'warning');

                this.cardError = result.error.message;
                this.enableCard();
                this.cardElement.focus();
                this.cardButtonDisable = false;
              } else {
                // The payment has succeeded.
                this.paymentSuccess(result);
              }
            });
          }
        });
      },

      paymentSuccess(result) {
        console.log('paymentSuccess: ', result);

        // let backend know?
        let intent = {
          intentId: this.intentId,
          amount: this.amount,
          type: 'SNACKSPACE',
        };

        this.loading(true);

        axios.post(this.route('api.stripe.intent-success'), intent)
          .then((response) => {
            if (response.status == '204') {
              console.log('paymentSuccess: confirmed');
            } else {
              flash('Error reporting payment success', 'danger');
              console.log('paymentSuccess', response.data);
              console.log('paymentSuccess', response.status);
              console.log('paymentSuccess', response.statusText);
            }

            this.paymentSuccessConfirmed();
          })
          .catch((error) => {
            flash('Error reporting payment success', 'danger');
            if (error.response) {
              // if HTTP_UNPROCESSABLE_ENTITY some validation error laravel or us
              // else if HTTP_CONFLICT to many bookings or over lap
              // else if HTTP_FORBIDDEN on enough permissions
              console.log('paymentSuccess: Response error', error.response.data, error.response.status, error.response.headers);
            } else if (error.request) {
              // The request was made but no response was received
              // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
              // http.ClientRequest in node.js
              console.error('paymentSuccess: Request error', error.request);
            } else {
              // Something happened in setting up the request that triggered an Error
              console.error('paymentSuccess: Error', error.message);
            }

            this.paymentSuccessConfirmed();
          });
      },

      paymentSuccessConfirmed() {
        // swap modal
        $(this.$refs.cardModal).modal('hide');
        $(this.$refs.successModal).modal('show');

        // refresh page
        this.reloadPageTimer();
      },

      reloadPageTimer() {
          if (this.reloadTime <= 0) {
            window.location.reload(true);
          } else {
            setTimeout(() => {
              this.reloadTime -= 1;
              this.reloadPageTimer();
            }, 1000);
          }
      },
    },

    mounted() {
      this.$nextTick(function () {
        $('.vue-remove').contents().unwrap();
      });

      $(this.$refs.cardModal).modal('handleUpdate');
      $(this.$refs.cardModal).on('hidden.bs.modal', this.modalHidden);
      $(this.$refs.successModal).modal('handleUpdate');

      this.cardElement = elements.create('card', {
        disabled: true,
      });
      this.cardElement.mount(this.$refs.cardDiv)
      this.cardElement.on('change', this.cardElementChange);

      this.paymentRequest = stripe.paymentRequest({
        country: 'GB',
        currency: 'gbp',
        total: {
          label: 'Snackspace',
          amount: this.amount,
        },
        requestPayerName: true,
        requestPayerEmail: true,
      });

      this.prButton = elements.create('paymentRequestButton', {
        paymentRequest: this.paymentRequest,
      });
      this.prButton.on('click', (event) => {
        if (this.prButtonDisable) {
          event.preventDefault();
          return;
        }
      });

      // Check the availability of the Payment Request API first.
      this.paymentRequest.canMakePayment().then((result) => {
        if (result) {
          this.prButton.mount(this.$refs.paymentRequestButton);
        } else {
          this.$refs.paymentRequestFG.style.display = 'none';
        }
      });

      this.paymentRequest.on('paymentmethod', this.submitPaymentRequest);
    },
  };
</script>
