@section('content')
    <h1 class="text-2xl font-bold mb-4">All Addresses</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-8">
            {{ session('success') }}
        </div> 
    @endif
    <br><br>
    <a href="{{ route('addresses.create') }}" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-4">
        + Add New Address
    </a>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($addresses as $address)
            <div class="border p-4 rounded shadow">
                <p><strong>Street:</strong> {{ $address->street_address }}</p>
                <p><strong>City:</strong> {{ $address->city }}</p>
                <p><strong>State:</strong> {{ $address->state }}</p>
                <p><strong>Zipcode:</strong> {{ $address->zipcode }}</p>

                @php
                    $images = json_decode($address->images ?? '[]');
                    $firstImage = is_array($images) && count($images) ? $images[0] : null;
                @endphp
                @if($firstImage)
                    <img src="{{ asset('images/' . $firstImage) }}" alt="House Image" class="mt-2 w-full h-auto rounded">
                    <p class="mt-1 text-sm text-gray-600">{{ count($images) }} photo{{ count($images) > 1 ? 's' : '' }}</p>
                @else
                    <p>No images uploaded</p>
                @endif
            </div>

            <div class="mt-4 flex gap-2">
                <a href="{{ route('addresses.edit', $address->id) }}" class="text-blue-600 hover:underline">Edit</a>

                <form action="{{ route('addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                </form>
            </div>
        @endforeach
    </div>

    @if($addresses->isEmpty())
        <p class="text-gray-500">No addresses found. <a href="{{ route('addresses.create') }}" class="text-blue-600 underline">Add one?</a></p>
    @endif

@endsection
