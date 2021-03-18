@component('mail::message')
# Hello,

Thank you for registering your interest in Nottingham Hackspace.

If you'd like to become a Nottingham Hackspace member, the first step is is to:-
@component('mail::button', ['url' => route('register', $token)])
Create an HMS account
@endcomponent

Also, please add "{{ $membershipEmail }}" and "{{ $trusteesEmail }}" to your address book to reduce the chances of hackspace email going into your spam.

After setting up login details, you'll be asked to fill in some more information about yourself, namely your address and a contact phone number. Don't worry, we won't share this information with anyone, unless legally obliged to do so.

Once you've submitted the information we need, one of our member admins will be notified. They'll give your information a quick check, and if all is well they'll move your membership on to the next stage. This is the part where you get the Nottingham Hackspace bank details, as well as a unique payment reference code for your account. It is very important you include this reference on your payments. 

Use these details to set up a bank standing order for your membership fee. Membership is pay-what-it's-worth-to-you on a monthly basis, and you can always change the amount you're paying if you find yourself using the space more or less than you first thought.

When your standing order is set up and your first payment is made, even if the money leaves your account, payments are not instant between all banks and records don't update immediately, so it may take 3 to 4 days before it's visible in the hackspace account. Our automated system checks our account at midnight on weekdays. When your payment does show, you'll receive an email confirming membership, you'll get the door codes and an invitation to collect your RFID card at a Wednesday Open Hack Night. Once you've collected your card, you are free to visit at any time, twenty four hours a day.

A few important details: Nottingham Hackspace is incorporated as a non-profit company, registration number {{ config('branding.company_number', '07766826') }}. Everyone who works on stuff for the hackspace is a volunteer; the hackspace has no staff, just members. So far, it has also been entirely funded and is self-sustaining through members contributions rather than grants.

Please do consider volunteering. We are always looking for help.

Here's the URL for the public Google Group:<br>
{{ $groupLink }}

Here are the hackspace rules:<br>
{{ $rulesLink }}

If you have any questions, just email.

Thanks,<br>
Nottinghack Membership Team
@endcomponent
