<template>
  <div class="vue-remove">
    <div class="card">
      <div class="card-header">Make a Donation</div>
      <div class="card-body">
        <p>
          The hackspace is entirely funded by its members and donations.<br>
          If you would like to make a one off donation please enter an amount below and click the Donate button to pay by card.
        </p>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">£</span>
          </div>
          <input type="number" min="5" class="form-control" v-model="amount">
        </div>
      </div>
      <div class="card-footer">
        <button type="button" class="btn btn-primary mb-1" @click="showModal">Donate</button>
      </div>
    </div>

    <!-- Card Modal -->
    <div ref="cardModal" class="modal fade" :id="$id('addMoneyToSnackspaceModal')" tabindex="false" role="dialog" :aria-labelledby="$id('addMoneyToSnackspaceLabel')" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" :id="$id('addMoneyToSnackspaceLabel')">Donate to the hackspace</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div ref="modalBody" class="modal-body">
            <p>Your have entered <strong>£{{ intentAmount / 100 }}</strong> as your donation amount.</p>
            <button type="button" :class="['btn btn-primary btn-block mb-3', changingAmount ? 'd-none' : '']" @click="changeAmount">Change Amount</button>
            <div :class="['form-group', changingAmount ? '' : 'd-none']">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">£</span>
                </div>
                <input type="number" min="5" class="form-control" v-model="amount">
                <div class="input-group-append">
                  <button type="button" class="btn btn-primary" @click="updateAmount">Set</button>
                </div>
              </div>

            </div>


            <div ref="paymentRequestFG" class="form-group">
              <div ref="paymentRequestButton">
                <!-- A Stripe Element will be inserted here. -->
              </div>
            </div>

            <div class="form-group">
              <input v-model="cardholderName" :id="$id('cardholder-name')" :class="['form-control', nameError ? 'is-invalid' : '']" type="text" placeholder="Card holder name" autocomplete="cc-name" :disabled="cardButtonDisable" @input="updateName">
              <div class="invalid-feedback" role="alert" v-if="nameError">{{ nameError }}</div>
            </div>

            <div class="form-group" v-if="guest">
              <input v-model="cardholderEmail" :id="$id('cardholder-email')" :class="['form-control', emailError ? 'is-invalid' : '']" type="text" placeholder="Card holder email" autocomplete="email" :disabled="cardButtonDisable" @input="updateEmail">
              <div class="invalid-feedback" role="alert" v-if="emailError">{{ emailError }}</div>
            </div>

            <div class="form-group">
              <div ref="cardDiv" :class="['form-control', cardError ? 'is-invalid' : '']">
                <!-- A Stripe Element will be inserted here. -->
              </div>
              <div class="invalid-feedback" role="alert" v-if="cardError">{{ cardError }}</div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-block" :data-secret="clientSecret" :disabled="cardButtonDisable" @click="submitPayment">
              <i class="fal fa-credit-card" aria-hidden="true"></i> Submit Payment
            </button>
          </div>
        </div>
      </div>
    </div> <!-- Card Modal end -->

    <!-- Success Modal -->
    <div ref="successModal" class="modal fade" :id="$id('successModal')" tabindex="false" role="dialog" :aria-labelledby="$id('addMoneyToSnackspaceLabel')" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" :id="$id('addMoneyToSnackspaceLabel')">Donate to the hackspace</h5>
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
      guest: false,
      // userId: Number,
    },

    data() {
      return {
        amount: 10.00,
        intentAmount: 0,
        cardholderName: '',
        cardholderEmail: '',
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
        emailError: null,
        successMessage: 'Donation successful, thank you very much.',
        reloadTime: 3,
        changingAmount: false,
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
        } else if (this.intentAmount != this.amount * 100) {
          this.updateAmount();
        }
      },

      modalHidden(event) {
        this.cardholderName = '';
        this.cardholderEmail = '';
        this.cardElement.clear();
        this.loading(false);
      },

      changeAmount() {
        this.changingAmount = true;
        this.disableCard();
        this.cardButtonDisable = true;
        this.prButtonDisable = false;
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
          amount: this.amount * 100,
          type: 'DONATION',
        };

        this.loading(true);
        let route = this.guest ? this.route('api.stripe.make-intent.anon') : this.route('api.stripe.make-intent');

        axios.post(route, intent)
          .then((response) => {
            if (response.status == '201') { // HTTP_CREATED
              // pull out pi and client secret
              //
              this.intentId = response.data.intentId;
              this.clientSecret = response.data.clientSecret;
              this.intentAmount = response.data.amount;
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
          amount: this.amount * 100,
          type: 'DONATION',
        };

        this.loading(true);

        let route = this.guest ? this.route('api.stripe.make-intent.anon') : this.route('api.stripe.make-intent');

        axios.post(route, intent)
          .then((response) => {
            if (response.status == '200') {
              this.intentAmount = response.data.amount;

              // check amount is correct
              if (response.data.amount != this.amount * 100) {
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
        this.changingAmount = false;

        this.cardButtonDisable = true;

        this.updateIntent();

        // update the paymentRequest as with the new amount
        this.paymentRequest.update({
          total: {
            label: 'Snackspace',
            amount: this.amount * 100, // < bind amount here
          },
        });
      },

      updateName() {
        this.nameError = null;
      },

      updateEmail() {
        this.emailError = null;
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

        if (this.guest && !this.cardholderEmail) {
          this.emailError = 'Card holder email is required.';
          return;
        }

        this.loading(true);
        this.cardButtonDisable = true;
        this.cardError = null;
        this.disableCard();
        this.prButtonDisable = true;

        let paymentData = {
          payment_method_data: {
            billing_details: {name: this.cardholderName}
          }
        };

        if (this.guest) {
          paymentData.receipt_email = this.cardholderEmail;
        }

        stripe.handleCardPayment(
          this.clientSecret, this.cardElement, paymentData
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
          amount: this.amount * 100,
          type: 'DONATION',
        };

        this.loading(true);

        let route = this.guest ? this.route('api.stripe.make-intent.anon') : this.route('api.stripe.make-intent');

        axios.post(route, intent)
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
          amount: this.amount * 100,
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
