<div class="shadow rounded-lg overflow-hidden">
    <div class="bg-primary text-white">
        <p class="text-lg">{{$user->id}}-{{$user->name}}</p>
    </div>

    <div class="text-center my-2">
        <div class="flex justify-center space-x-2">
            <p class="font-bold">Phone</p>
            <p>{{$user->phone_number}}</p>
        </div>
        <div class="flex justify-center space-x-2">
            <p class="font-bold">Orders Created</p>
            <p>{{$user->ordersQuantity}}</p>
        </div>
        <div class="flex justify-center space-x-2">
            <p class="font-bold">Total Spent</p>
            @forelse(array_keys($user->totalSpent) as $currency )
                <p>  {{$user->totalSpent[ $currency]}} {{$currency}} </p>
            @empty
                <p>0</p>
            @endforelse
        </div>
        <div class="flex justify-center space-x-2">
            <p class="font-bold">Phone Number</p>
            <p>{{$user->phone_number}}</p>
        </div>
    </div>
</div>
