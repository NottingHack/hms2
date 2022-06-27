@component('mail::message')
# Hello {{ $fullname }}

@content('emails.membership.membershipComplete', 'main')

The {{ config('branding.space_type') }} members guide can be found at {{ $membersGuideHTML }} and it is a recommended read for all members. <br>
A PDF version is also available [here.]({{ $membersGuidePDF }})

In terms of access, the street door code is **{{ $outerDoorCode }}**, and all other doors, including the doors in the stairwell and studio, are **{{ $innerDoorCode }}**. Obviously, please do not share these with non-members.

Wifi access:<br>
SSID: {{ $wifiSsid }}<br>
Pass: {{ $wifiPass }}

Our Google Group is where a lot of online discussion takes place:<br>
{{ $groupLink }}

Slack is also used for team discussions. You can join NH teams slack at:<br>
{{ $slackHTML }}

The {{ config('branding.space_type') }} rules:<br>
{{ $rulesHTML }}

We also have a wiki, with lots of information about the tools in the {{ ucfirst(config('branding.space_type')) }}:<br>
{{ $wikiLink }}

If you have any questions, feel free to email: {{ $membershipTeamEmail }}

Thanks,<br>
{{ config('branding.community_name') }} Membership Team
@endcomponent

