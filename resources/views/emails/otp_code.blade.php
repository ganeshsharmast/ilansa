<x-mail::message>
Hello {{$customer->name}},

Your Order No.{{$order->id}} has been placed successfully and is now being processed

You can view your order details using the link below.

<x-mail::button :url="$order_url">
View Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>