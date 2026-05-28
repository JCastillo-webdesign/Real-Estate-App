<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Address Details</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
        }
        .lookup-form {
            background-color: #ffffffaa;
            padding: 15px;
            margin: 20px auto;
            width: 300px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .lookup-form input[type="number"] {
            padding: 8px;
            width: 80%;
            margin-bottom: 10px;
        }

        .lookup-form button {
            padding: 8px 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .address-box {
            background-color: white;
            padding: 20px;
            margin: 20px auto;
            width: 90%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .field {
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
            color: #333;
            display: inline-block;
            width: 150px;
        }

        .value {
            color: #555;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">

        <h1>Address Details</h1>
    <!-- Address Details -->
     <div class="address-box">
    <div class="field">
            <span class="label">Street Address:</span>
            <span class="value">{{ $address->street_address }}</span>
        </div>

        <div class="field">
            <span class="label">Street Address 2:</span>
            <span class="value">{{ $address->street_address_2 ?? 'N/A' }}</span>
        </div>

        <div class="field">
            <span class="label">City:</span>
            <span class="value">{{ $address->city }}</span>
        </div>

        <div class="field">
            <span class="label">State:</span>
            <span class="value">{{ $address->state }}</span>
        </div>

        <div class="field">
            <span class="label">Zipcode:</span>
            <span class="value">{{ $address->zipcode }}</span>
        </div>
        @php
            $images = json_decode($address->images ?? '[]');
        @endphp

        @if(count($images))
            <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center; margin-top: 20px;">
                @foreach($images as $image)
                    <img src="{{ asset('images/' . $image) }}"
                        alt="House image"
                        style="max-width: 200px; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.2); transition: transform 0.2s ease;"
                        onmouseover="this.style.transform='scale(1.05)'"
                        onmouseout="this.style.transform='scale(1)'">
                @endforeach
            </div>
        @else
            <p style="text-align:center; color:gray;">No images available for this address.</p>
        @endif

        <a href="{{ route('addresses.index') }}" class="back-link">← Back to Address List</a>
    </div>
</body>
</html>