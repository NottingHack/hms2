@component('mail::message')
# Dear {{ $fullname }},

@content('emails.project.tort', 'main')


The project details are:

* Project Number: {{ $projectNumber }}
* Project Name: {{ $projectName }}
* Removal Reason: {{ $removalReason }}

@content('emails.project.tort', 'additional')


{{ config('branding.community_name') }} Trustees
@endcomponent
