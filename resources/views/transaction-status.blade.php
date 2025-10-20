@php
    use App\Enums\TransactionStatus;
@endphp

@switch($status)
    @case(TransactionStatus::PENDING_PAYMENT->value)
        <span class="text-xs font-medium px-3 py-1 rounded-full bg-orange-100 text-orange-800">{{ $status }}</span>
    @break

    @case(TransactionStatus::WAITING_CONFIRMATION->value)
        <span class="text-xs font-medium px-3 py-1 rounded-full bg-yellow-100 text-yellow-800">{{ $status }}</span>
    @break

    @case(TransactionStatus::CONFIRMED->value)
        <span class="text-xs font-medium px-3 py-1 rounded-full bg-blue-100 text-blue-800">{{ $status }}</span>
    @break

    @case(TransactionStatus::DELIVERED->value)
        <span class="text-xs font-medium px-3 py-1 rounded-full bg-green-100 text-green-800">{{ $status }}</span>
    @break

    @default
        <span class="text-xs font-medium px-3 py-1 rounded-full bg-red-100 text-red-800">{{ $status }}</span>
@endswitch
