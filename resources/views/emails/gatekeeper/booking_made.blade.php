@component('mail::message')
# Hello {{ $name }},

Your have been granted access to {{ $buildingName }} by {{ $approvedBy }}.  
You may now visit during the times below.

**Start:** {{ $start }}  
**End:** {{ $end }}  
**Area:** {{ $bookableAreaName }}  

Please remember to keep your main use during this time within the area of the space you indicated.

Thank you,  
HMS
@endcomponent
