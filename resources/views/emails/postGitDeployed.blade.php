@component('mail::message')
# Hello {{ $teamName }},

Auto deploy has finish with the following results.

---
@foreach ($commandResults as $commandResult)
## Command: {{ $commandResult['cmd'] }}
### Return Code: {{ $commandResult['return_code'] }}
```
{{ $commandResult['output'] }}
```
---

@endforeach

Thank you,<br>
HMS
@endcomponent
