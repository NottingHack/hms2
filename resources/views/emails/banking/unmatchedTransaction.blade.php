@component('mail::message')
# Hello {{ $teamName }},

@if (count($unmatchedTransaction))
I was unable to match the following transactions to any payment reference or member account.

@component('mail::table')
| Date       | Description                                                      | Amount  | Bank          |
| ---------- | ---------------------------------------------------------------- | ------- | ------------- |
@foreach ($unmatchedTransaction as $bankTransaction)
| {{ $bankTransaction->getTransactionDate()->toDateString() }} | {{ $bankTransaction->getDescription() }} | @money($bankTransaction->getAmount(), 'GBP') | {{ $bankTransaction->getBank()->getName() }} |
@endforeach
@endcomponent
@endif

@if (count($unmatchedBank))
I was unable to match a Bank account for the following transactions.

@component('mail::table')
| Sort Code | Account Number | Date       | Description                                                      | Amount  |
| --------- | -------------- | ---------- | ---------------------------------------------------------------- | ------- |
@foreach ($unmatchedBank as $transaction)
| {{ $transaction['sortCode'] }} | {{ $transaction['accountNumber'] }} | {{ $transaction['date'] }} | {{ $transaction['description'] }} | @money($transaction['amount'], 'GBP') |
@endforeach
@endcomponent
@endif


Thank you,<br>
HMS
@endcomponent
