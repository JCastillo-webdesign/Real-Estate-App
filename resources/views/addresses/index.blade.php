{{-- Debug: show count --}}
<p>Total addresses: {{ count($addresses ?? []) }}</p>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Addresses</title>
</head>
    <style>
        body {
            background-image: url('/images/house pic_3.png');
            background-size: cover;          /* Fill the screen without distortion */
            background-position: center;     /* Center the image */
            background-repeat: no-repeat;    /* Prevent tiling */
            background-attachment: fixed;    /* Optional: makes it stay fixed on scroll */
        }

        .header-container {
            color: #add8e6;
            font-weight: bold;
            padding: 10px;
        }

        .table-container {
        background-color: white;
        padding: 20px;
        max-width: 90%;
        margin: 20px auto;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
     table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ccc;
    }

    th {
        background-color: #f4f4f4;
    }

    .message-style {
        color: green;
        font-weight: bold;
        margin: 50px;
    }

    .alert-box {
        background-color: #d4edda;         /* light green */
        color: #155724;                    /* dark green text */
        border: 1px solid #c3e6cb;         /* border */
        padding: 15px;
        margin: 20px auto;
        width: 80%;
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    </style>
<body>
    <h1 class="header-container">List of Addresses:</h1>  
 
    <div class="table-container">
    
        @if(session('success'))
            <div class="alert-box">{{ session('success') }}</div>
        @endif

    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div><br>
    @endif

    <br>
    <a href="{{ route('addresses.create') }}">Add a New Address</a>
    <br><br><br> 
    
    <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Street Address</th>
                    <th>Street Address 2</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Zipcode</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($addresses as $address)
                    <tr>
                        <td>{{ $address->id }}</td>
                        <td>{{ $address->street_address }}</td>
                        <td>{{ $address->street_address_2 ?? 'N/A' }}</td>
                        <td>{{ $address->city }}</td>
                        <td>{{ $address->state }}</td>
                        <td>{{ $address->zipcode }}</td>

                        <td>

                            @php
                                $raw = $address->images;

                                // First decode
                                $images = json_decode($raw, true);

                                // If still not an array, decode again (handles double-encoded JSON)
                                if (!is_array($images)) {
                                    $images = json_decode($images ?? '[]', true);
                                }

                                $firstImage = $images[0] ?? null;
                            @endphp

                            @if($firstImage)
                                <a href="{{ route('addresses.show', $address->id) }}">
                                    <img src="{{ asset('images/' . $firstImage) }}"
                                        alt="Preview"
                                        style="width: 120px; height: 120px; object-fit: cover; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.2);">
                                </a>
                                <div style="margin-top: 6px;
                                    font-size: 12px;
                                    background: #f2f2f2;
                                    padding: 4px 8px;
                                    border-radius: 4px;
                                    display: inline-block;
                                    color: #555;
                                ">
                                  {{ count($images) }} photo{{ count($images) > 1 ? 's' : '' }}
                                </div>
                            @else
                                <span style="color: gray;">No images uploaded</span>
                            @endif
                        </td>

                        <td>
                            <form action="{{ route('addresses.edit', $address->id) }}" method="GET" style="display: inline;">
                                <button type="submit">Edit</button>
                            </form>

                            <form action="{{ route('addresses.destroy', $address->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this address?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">No addresses available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</body>
</html>
