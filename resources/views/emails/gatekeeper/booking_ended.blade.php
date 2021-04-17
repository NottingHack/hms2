@component('mail::message')
# Hello {{ $name }},

Your access to {{ $buildingName }} ended at {{ $end }} but Gatekeeper still things you are still in the building.  
Gatekeeper is not perfect, it may be that you have already left the space but did not swipe out.

If you have left just click the button below to confirm.  

However if you are still in the space please leave as soon as possible, by over staying you booking you are in of the rules and putting yourselves and others at risk. 

@component('mail::button', ['url' => $actionUrl])
Yes I Have Left
@endcomponent

@content('emails.gatekeeper.booking_ended', 'additional')

**Start:** {{ $start }}  
**End:** {{ $end }}  
**Area:** {{ $bookableAreaName }}  
**Guests:** {{ $guests }}  

Please keep up to date with the status of the {{ ucfirst(config('branding.space_type')) }} with regards to Membership entry [here]({{ Meta::get('temporary_access_email_link', Meta::get('wiki_html')) }}).  

Thank you,  
HMS
@endcomponent
