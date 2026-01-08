<x-mail::message>
    # Workload Remarks Submitted

    **Lecturer:** {{ $lecturer->name }} ({{ $lecturer->email }})
    **Department:** {{ $lecturer->department->name ?? 'N/A' }}

    ## Remarks

    {{ $remarks }}

    <x-mail::button :url="route('login')">
        Login to TFMS
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>