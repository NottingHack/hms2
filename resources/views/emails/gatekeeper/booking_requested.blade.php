@component('mail::message')
# Hello,

{{ $userFullName }} has requested access to {{ $buildingName }}.

**Start:** {{ $start }}  
**End:** {{ $end }}  
**Area:** {{ $bookableAreaName }}  
**Reason:** {{ $reason }}  

@component('mail::button', ['url' => route('gatekeeper.temporary-access') . '#' . Str::slug($buildingName)])
Review Booking
@endcomponent

Thank you,  
HMS
@endcomponent
