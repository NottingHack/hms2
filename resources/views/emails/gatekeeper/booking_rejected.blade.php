@component('mail::message')
# Hello {{ $name }},

Your access booking request has been **rejected**. The given reason is:  
@component('mail::panel')
{{ $reason }}
@endcomponent
Your booking request has been removed from the calendar, should you wish to add further details, the request must be made through the calendar again.  

@content('emails.gatekeeper.booking_rejected', 'additional')  

**Building:** {{ $buildingName }}  
**Start:** {{ $start }}  
**End:** {{ $end }}  
**Area:** {{ $bookableAreaName }}  
**Guests:** {{ $guests }}  

Please keep up to date with the status of the {{ ucfirst(config('branding.space_type')) }} with regards to Membership entry [here]({{ Meta::get('temporary_access_email_link', Meta::get('wiki_html')) }}).  

Thank you,  
HMS
@endcomponent
