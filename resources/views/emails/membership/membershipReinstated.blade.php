@component('mail::message')
# Hello {{ $fullname }}

@content('emails.membership.membershipReinstated', 'main')  

The {{ config('branding.space_type') }} members guide can be found at {{ $membersGuideHTML }} and it is a recommended read for all members.  
A PDF version is also available [here.]({{ $membersGuidePDF }})  

In terms of access, the street door code is **{{ $outerDoorCode }}**, and all other doors, including the doors in the stairwell and studio, are **{{ $innerDoorCode }}**. Obviously, please do not share these with non-members.  

Wifi access:  
SSID: {{ $wifiSsid }}  
Pass: {{ $wifiPass }}  

@feature('discord')
Discord is used for members to chat online. You can join the NH Discord at:  
{{ $discordHTML }}  
@endfeature

The {{ config('branding.space_type') }} rules:  
{{ $rulesHTML }}  

We also have a wiki, with lots of information about the tools in the {{ ucfirst(config('branding.space_type')) }}:  
{{ $wikiLink }}  

If you have any questions, feel free to email: {{ $membershipTeamEmail }}  

Thanks,  
{{ config('branding.community_name') }} Membership Team
@endcomponent
