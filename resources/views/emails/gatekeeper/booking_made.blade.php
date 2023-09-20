@component('mail::message')
# Hello {{ $name }},

Your access booking request has been created by a Trustee and authorised.  
@content('emails.gatekeeper.booking_made', 'additional')  

**Building:** {{ $buildingName }}  
**Start:** {{ $start }}  
**End:** {{ $end }}  
**Area:** {{ $bookableAreaName }}  
**Guests:** {{ $guests }}  

Please keep up to date with the status of the {{ ucfirst(config('branding.space_type')) }} with regards to Membership entry [here]({{ Meta::get('temporary_access_email_link', Meta::get('wiki_html')) }}).  

Thank you,  
HMS
@endcomponent
