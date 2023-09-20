@component('mail::message')
# Hello,

{{ $userFullName }} has requested access to the {{ ucfirst(config('branding.space_type')) }} and this requires Trustee authorisation. Details are below. Please click the Review Booking button to authorise/decline. Always give reasons if declining.  

**Building:** {{ $buildingName }}  
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
