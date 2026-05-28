<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add a New Address</title>
</head>
<style>
        body {
            background-image: url('/images/house pic.png');
            background-size: cover;          /* Fill the screen without distortion */
            background-position: center;     /* Center the image */
            background-repeat: no-repeat;    /* Prevent tiling */
            background-attachment: fixed;    /* Optional: makes it stay fixed on scroll */
        }

        .add-container {
            background-color: white;
            padding: 35px;
            max-width: 30%;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .header-container {
            color: rgba(212, 217, 219, 1);
            font-weight: bold;
            text-align: center;
        }

        .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="file"],
    textarea {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
        border: 1px solid #ccc; /* This adds the visible border */
        border-radius: 6px;
        background-color: #fff;
        font-size: 16px;
    }

    input:focus,
    textarea:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }

    form {
        max-width: 500px;
        margin: auto;
        background-color: white;
        padding: 20px;
        border-radius: 10px;
    }

    .upload-zone {
    border: 2px dashed #ccc;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    background: #f9f9f9;
}

.preview-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.preview-wrapper {
    position: relative;
}

.preview-img {
    max-width: 100px;
    border-radius: 4px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

.remove-btn {
    position: absolute;
    top: 2px;
    right: 2px;
    background: #ff4d4d;
    color: white;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    cursor: pointer;
}

button[type="submit"] {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

button[type="button"] {
    background-color: #6c757d;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
}

button[type="button"]:hover {
    background-color: #484949;
}    
</style>
<body>
@extends('layouts.app')   

@section('title', 'Add a New Address') 

@section('content')
<div class="add-container">

 @if($errors->any())
    <div class="alert-box" style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
 @endif

    <form id="address-form" action="{{ route('addresses.store') }}" method="POST" autocomplete="off" 
        enctype="multipart/form-data">
    @csrf
    <h3>Enter the New Address</h3>
    <div class="form-group">
    <br>    
        <label for="street_address">Street Address:</label>
        <input type="text" name="street_address" id="street_address" required><br>
    </div>

    <div class="form-group">
        <label for="street_address_2">Street Address 2:</label>
        <input type="text" name="street_address_2" id="street_address_2"><br>
    </div>

    <div class="form-group">    
        <label for="city">City:</label>
        <input type="text" name="city" id="city" required><br>
    </div>

    <div class="form-group">    
        <label for="state">State:</label>
        <input type="text" name="state" id="state" required><br>
    </div>

    <div class="form-group">
        <label for="zipcode">Zipcode:</label>
        <input type="text" name="zipcode" id="zipcode" required><br>
    </div>

    <!-- Drop zone and image input -->
    <div id="uploadTrigger" class="upload-zone">
        <p>Click to upload images (max 15)</p>
        <div id="previewContainer" class="preview-grid"></div>
    </div>
    <br>

    <input type="file" name="images[]" id="imageDrop" multiple accept="image/*" style="display: none;">

        <button type="submit" id="submitBtn">Add Address</button>
        <br><br>

        <button type="button" onclick="window.location.href='{{ route('addresses.index') }}'">Back to Home</button> 
    </form>
</div>
    @endsection    

    @section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

const uploadTrigger = document.getElementById('uploadTrigger');
const imageInput = document.getElementById('imageDrop');
const previewContainer = document.getElementById('previewContainer');
let selectedFiles = [];

uploadTrigger.addEventListener('click', () => imageInput.click());

imageInput.addEventListener('change', (e) => {
    const files = Array.from(e.target.files);
    if (selectedFiles.length + files.length > 15) {
        alert('You can only upload up to 15 images.');
        return;
    }
    handleFiles(files);
});

function handleFiles(files) {
    files.forEach(file => {
        if (!file.type.startsWith('image/')) return;
            selectedFiles.push(file);

            const reader = new FileReader();
            reader.onload = function(e) {
                const wrapper = document.createElement('div');
                wrapper.className = 'preview-wrapper';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-img';


                const removeBtn = document.createElement('button');
                removeBtn.textContent = '×';
                removeBtn.className = 'remove-btn';

                removeBtn.addEventListener('click', () => {
                    const index = selectedFiles.indexOf(file);
                    if (index > -1) {
                        selectedFiles.splice(index, 1);
                        wrapper.remove();
                        updateFileInput();                        
                    }
                });

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                previewContainer.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
    });

    updateFileInput();
}  
    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        imageInput.files = dt.files;
    }

    console.log('Upload script loaded');
});
</script>

@endsection
</body>
</html>