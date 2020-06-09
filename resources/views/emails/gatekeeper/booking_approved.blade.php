@component('mail::message')
# Hello {{ $name }},

Your requested access to {{ $buildingName }} has been **approved**.  
You may now visit during the times below.

**Start:** {{ $start }}  
**End:** {{ $end }}  
**Area:** {{ $bookableAreaName }}  
**Guests:** {{ $guests }}  

Please remember to keep your main use during this time within the area of the space you indicated.

Thank you,  
HMS
@endcomponent