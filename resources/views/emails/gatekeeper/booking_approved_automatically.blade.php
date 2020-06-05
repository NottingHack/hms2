@component('mail::message')
# Hello {{ $name }},

Your access to {{ $buildingName }} has been **automatically approved**.  
You may now visit during the times below.

**Start:** {{ $start }}  
**End:** {{ $end }}  
**Area:** {{ $bookableAreaName }}  

Please remember to keep your main use during this time within the area of the space you indicated.

Thank you,  
HMS
@endcomponent
