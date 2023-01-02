@component('mail::message')
# Hello {{ $fullname }}

@content('emails.membership.membershipUnderPaid', 'main')


Thank you for your membership payment to {{ config('branding.space_name') }}, however your payment was below our require minimum amount of **@money($minimumAmount, 'GBP')** for membership. In our to complete you membership your payments must be equal or above the minimum amount. 

Any payments under the minimum amount are consider as purely donations.  

If you have any questions, feel free to email: {{ $membershipTeamEmail }}

Thanks,<br>
{{ config('branding.community_name') }} Membership Team
@endcomponent
