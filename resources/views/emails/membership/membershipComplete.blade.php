@component('mail::message')
# Hello {{ $fullname }}

@content('emails.membership.membershipComplete', 'main')

The hackspace members guide can be found at {{ $membersGuideHTML }} and it is a recommended read for all members. <br>
A PDF version is also available [here.]({{ $membersGuidePDF }})

In terms of access, the street door code is **{{ $outerDoorCode }}**, and all other doors, including the doors in the stairwell and studio, are **{{ $innerDoorCode }}**. Obviously, please do not share these with non-members.

Wifi access:<br>
SSID: {{ $wifiSsid }}<br>
Pass: {{ $wifiPass }}

Our Google Group is where a lot of online discussion takes place:<br>
{{ $groupLink }}

The hackspace rules:<br>
{{ $rulesHTML }}

We also have a wiki, with lots of information about the tools in the Hackspace:<br>
{{ $wikiLink }}

If you have any questions, feel free to email: {{ $membershipTeamEmail }}

Thanks,<br>
Nottinghack Membership Team
@endcomponent

