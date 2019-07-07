@component('mail::message')
# Hello {{ $fullname }}

Thanks for reinstating your membership of Nottingham Hackspace. Here is a reminder of some details you might need.

GateKeeper is our RFID entry system for which you need a suitable card set up, which we provide. If you aren't sure if your former RFID card will work, or if you need a new one, please visit on the next open hack night and ask people to point you to one of the membership team, or please contact membership@nottinghack.org.uk if you are unable to attend one. It will be £1 for a new RFID card.

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
