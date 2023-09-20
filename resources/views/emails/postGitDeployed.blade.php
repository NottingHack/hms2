@component('mail::message')
# Hello {{ $teamName }},

@if ($success)
Auto deploy has **successfully** finish with the following results.  
@else
Auto deploy has **failed** finish with the following results.  
@endif

Start Time: {{ $startTime->toDateTimeString() }}  
End Time: {{ $stopTime->toDateTimeString() }}  
Run Time: {{ $runTime }} Minutes  

---
@foreach ($commandResults as $commandResult)
## Command: {{ $commandResult['cmd'] }}
### Return Code: {{ $commandResult['return_code'] }}
```
{{ $commandResult['output'] }}
```
---

@endforeach

Thank you,  
HMS
@endcomponent
