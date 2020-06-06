@component('mail::message')
# Hello {{ $name }},

Sorry at this time your requested access to {{ $buildingName }} has been **rejected**.  

The following reason was given.
@component('mail::panel')
{{ $reason }}
@endcomponent

**Start:** {{ $start }}  
**End:** {{ $end }}  
**Area:** {{ $bookableAreaName }}  

Thank you,  
HMS
@endcomponent
