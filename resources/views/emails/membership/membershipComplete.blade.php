@component('mail::message')
# Hello {{ $fullname }}

@content('emails.membership.membershipComplete', 'main')  

The {{ config('branding.space_type') }} members guide can be found at {{ $membersGuideHTML }} and it is a recommended read for all members. <br>
A PDF version is also available [here.]({{ $membersGuidePDF }})  

@feature('members_enroll_pin')
A guide to setting up your RFID door card can be found at {{ $gatekeeperSetupGuide }} - for this you will also need to know your unique, single-use PIN, {{ $membershipPin }}.  
@endfeature

In terms of access, the street door code is **{{ $outerDoorCode }}**, and all other doors, including the doors in the stairwell and studio, are **{{ $innerDoorCode }}**. Obviously, please do not share these with non-members.  

Wifi access:  
SSID: {{ $wifiSsid }}  
Pass: {{ $wifiPass }}  

Our Google Group is where a lot of online discussion takes place:  
{{ $groupLink }}  

@feature('discord')
Discord is used for members to chat online. You can join the hackspace Discord at:  
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

