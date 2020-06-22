@component('mail::message')
# Hello {{ $name }},

Your access booking request which was previously accepted has been **cancelled**. The given reason is:  
@component('mail::panel')
{{ $reason }}
@endcomponent

**Building:** {{ $buildingName }} 
**Start:** {{ $start }}  
**End:** {{ $end }}  
**Area:** {{ $bookableAreaName }}  
**Guests:** {{ $guests }}  

Please keep up to date with the status of Hackspace with regards to Membership entry [here]({{ Meta::get('temporary_access_email_link', Meta::get('wiki_html')) }}).  

Thank you,  
HMS
@endcomponent
