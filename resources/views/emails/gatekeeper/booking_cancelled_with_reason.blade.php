@component('mail::message')
# Hello {{ $name }},

Sorry at this time your access to {{ $buildingName }} which had been previously approved has now been **cancelled**.  

{{ $rejectedBy }} gave the following reason.
@component('mail::panel')
{{ $reason }}
@endcomponent

**Start:** {{ $start }}  
**End:** {{ $end }}  
**Area:** {{ $bookableAreaName }}  

Thank you,  
HMS
@endcomponent
