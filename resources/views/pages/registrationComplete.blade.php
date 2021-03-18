@extends('layouts.app')

@section('content')
<div class="container">
  <h4>
    Thank you for registering with Nottingham Hackspace. Here are the next steps.
  </h4>
  <p>
    You will have just received an email with a link to verify you email address. Please check your in-box and give the link a quick click.
  </p>
  <p>
    Now you have filled in your details, our membership admins will be notified. They'll give your information a quick check, and if all is well they'll move your membership on to the next stage. If there is an issue, they will send you an email with details of what needs correcting.
  </p>
  <p>
    Once it's all checked and accounted for, you will get the Nottingham Hackspace bank details, as well as a unique payment reference for your account.<br>
    Use these details to set up a standing order for your membership fee.<br>
    The reference must be used exactly as is when setting up your standing order for our automated systems to recognise your membership payments.
  </p>
  <p>
    Membership is pay-what-it's-worth-to-you on a monthly basis, and you can always change the amount you're paying if you find yourself using the space more or less than you first thought.
  </p>
  <p>
    When your standing order is set up and your first payment is made, even if the money leaves your account, payments are not instant between all banks and records don't update immediately, so it may take 3 to 4 days before it's visible in the hackspace account. Our automated system checks our account at midnight on weekdays.<br>
    When your payment does show, you'll receive an email confirming membership, you'll get the door codes and an invitation to collect your RFID card at a Wednesday Open Hack Night.<br>
    Once you've collected your card, you are free to visit at any time, twenty four hours a day.
  </p>
  <hr>
  <h4>A few important details:</h4>
  <p>
    Nottingham Hackspace is incorporated as a non-profit company, registration number {{ config('branding.company_number', '07766826') }}.<br>
    Everyone who works on stuff for the hackspace is a volunteer; the hackspace has no staff, just members.<br>
    So far, it has also been entirely funded and is self-sustaining through members contributions rather than grants.
  </p>
</div>
@endsection
