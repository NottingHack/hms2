@component('mail::message')
# Hello {{ $fullname }}

@content('emails.membership.membershipExUnderPaid', 'main')

Thank you for your membership payment to {{ config('branding.space_name') }}, however your payment was below our required minimum amount of **@money($minimumAmount, 'GBP')** for membership. In order to reinstate your membership your payments must be at least the minimum membership amount.

Any amount below the minimum is considered a donation.

If you have any questions, feel free to email: {{ $membershipTeamEmail }}

Thanks,<br>
{{ config('branding.community_name') }} Membership Team
@endcomponent
