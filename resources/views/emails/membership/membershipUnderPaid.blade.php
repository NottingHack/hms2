@component('mail::message')
# Hello {{ $fullname }}

@content('emails.membership.membershipUnderPaid', 'main')


Thank you for your membership payment to {{ config('branding.space_name') }}. Your payment must be at least **@money($minimumAmount, 'GBP')** for membership. In order to complete your membership, please make a payment of @money($minimumAmount, 'GBP') or more, using your payment reference. Our recommended amount for users of the space is @money($recommendedAmount, 'GBP').

Any amount below the minimum is considered a donation.

If you have any questions, feel free to email: {{ $membershipTeamEmail }}

Thanks,<br>
{{ config('branding.community_name') }} Membership Team
@endcomponent
