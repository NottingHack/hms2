@component('mail::message')
# Hello {{ $fullname }}

@content('emails.membership.membershipExUnderPaid', 'main')

Thank you for your payment supporting {{ config('branding.space_name') }}. Supporter payments are below **@money($minimumAmount, 'GBP')** and help us keep the space open.

For membership, payments need to be at least @money($minimumAmount, 'GBP'). We generally recommend @money($recommendedAmount, 'GBP') for users of the space.

Thanks again for your donation. If you have any questions, please email {{ $membershipTeamEmail }}

Thanks,<br>
{{ config('branding.community_name') }} Membership Team
@endcomponent
