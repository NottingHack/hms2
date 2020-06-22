@component('mail::message')
# Hello {{ $name }},

Your access booking request has been created by a Trustee and authorised.  
You must leave and swipe out of the Hackspace before the end time of your booking. You must swipe your RFID card on an exit door (this is any door, except the door at the top of the spiral staircase. This door is only an “in” door.  
Please ensure that you wipe all surfaces you have been in contact with before you leave.  

**Building:** {{ $buildingName }}  
**Start:** {{ $start }}  
**End:** {{ $end }}  
**Area:** {{ $bookableAreaName }}  
**Guests:** {{ $guests }}  

Please keep up to date with the status of Hackspace with regards to Membership entry [here]({{ Meta::get('temporary_access_email_link', Meta::get('wiki_html')) }}).  

Thank you,  
HMS
@endcomponent
