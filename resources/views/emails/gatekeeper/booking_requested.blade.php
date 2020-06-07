@component('mail::message')
# Hello,

{{ $userFullName }} has requested access to {{ $buildingName }}.

**Start:** {{ $start }}  
**End:** {{ $end }}  
**Area:** {{ $bookableAreaName }}  
**Guests:** {{ $guests }}  
**Reason:** {{ $reason }}  

@component('mail::button', ['url' => $actionUrl])
Review Booking
@endcomponent

Thank you,  
HMS
@endcomponent
