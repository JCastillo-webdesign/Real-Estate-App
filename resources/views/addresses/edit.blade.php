<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Address</title>

    <style>
        .layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .left-panel {
            width: 30%;
            padding: 20px;
            background-color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow-y: auto;
        }

        .right-panel {
            width: 70%;
            background-image: url('/images/house pic_4.png');
            background-size: cover;
            background-position: center right;
            background-repeat: no-repeat;
            position: fixed;
            right: 0;
            top: 0;
            height: 100vh;
        }

        .header-container {
            color: rgba(33, 34, 34, 1);
            font-weight: bold;
            text-align: center;
            width: 100%;
            margin-bottom: 20px;
        }

        form {
            width: 100%;
            max-width: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-group {
            width: 100%;
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .image-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
        }

        .img-box {
            width: 100px;
            height: 100px;
            position: relative;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.15);
        }

        .img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .img-box .delete-btn,
        .img-box .replace-btn {
            position: absolute;
            background: rgba(0,0,0,0.6);
            color: white;
            padding: 3px 6px;
            font-size: 11px;
            border-radius: 4px;
            opacity: 0;
            transition: 0.2s;
            cursor: pointer;
        }

        .img-box:hover .delete-btn,
        .img-box:hover .replace-btn {
            opacity: 1;
        }

        .delete-btn {
            top: 4px;
            right: 4px;
        }

        .replace-btn {
            bottom: 4px;
            right: 4px;
        }
    </style>
</head>

<body>
<div class="layout">
    <div class="left-panel">
        <h1 class="header-container">Edit an Address</h1>

        <form id="editForm" action="{{ route('addresses.update', $address->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="street_address">Street Address:</label>
                <input type="text" name="street_address" id="street_address" value="{{ $address->street_address }}" required>
            </div>

            <div class="form-group">
                <label for="street_address_2">Street Address 2:</label>
                <input type="text" name="street_address_2" id="street_address_2" value="{{ $address->street_address_2 }}">
            </div>

            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" name="city" id="city" value="{{ $address->city }}" required>
            </div>

            <div class="form-group">
                <label for="state">State:</label>
                <input type="text" name="state" id="state" value="{{ $address->state }}" required>
            </div>

            <div class="form-group">
                <label for="zipcode">Zipcode:</label>
                <input type="text" name="zipcode" id="zipcode" value="{{ $address->zipcode }}" required>
            </div>

            <h3>Images</h3>

            <input type="hidden" name="delete_images" id="delete_images">
            <input type="hidden" name="replace_map" id="replace_map">
            <input type="hidden" name="image_order" id="image_order">

            <div class="form-group" style="text-align:center;">
                <label for="addImagesInput">Add New Images:</label>
                <input type="file" id="addImagesInput" name="new_images[]" multiple accept="image/*">
            </div>

            <div id="imageManager" class="image-grid"></div>

            <button type="submit" class="btn btn-success" style="margin-top: 20px;">
                Save Changes
            </button>
        </form>
    </div>

    <div class="right-panel"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    let images = [];
    let deleteList = [];
    let replaceMap = {};
    let newFiles = [];

    @if ($address->images)
        @php
            $raw = $address->images;
            $decoded = json_decode($raw, true);

            if (!is_array($decoded)) {
                $decoded = json_decode($decoded ?? '[]', true);
            }
        @endphp

        @foreach ($decoded as $img)
            images.push({ type: "existing", filename: "{{ $img }}", file: null });
        @endforeach
    @endif

    const grid = document.getElementById('imageManager');
    const addInput = document.getElementById('addImagesInput');

    function renderGrid() {
        grid.innerHTML = "";

        images.forEach((imgObj, index) => {
            const box = document.createElement('div');
            box.className = "img-box";
            box.dataset.index = index;

            const img = document.createElement('img');
            img.src = imgObj.type === "existing"
                ? "{{ asset('images') }}/" + imgObj.filename
                : URL.createObjectURL(imgObj.file);

            const del = document.createElement('div');
            del.className = "delete-btn";
            del.textContent = "✕";
            del.onclick = () => {
                if (imgObj.type === "existing") {
                    deleteList.push(imgObj.filename);
                }
                images.splice(index, 1);
                renderGrid();
            };

            const rep = document.createElement('div');
            rep.className = "replace-btn";
            rep.textContent = "Replace";
            rep.onclick = () => {
                const input = document.createElement('input');
                input.type = "file";
                input.accept = "image/*";
                input.onchange = e => {
                    const file = e.target.files[0];
                    if (!file) return;

                    if (imgObj.type === "existing") {
                        deleteList.push(imgObj.filename);
                        replaceMap[imgObj.filename] = file.name;
                    }

                    imgObj.type = "new";
                    imgObj.file = file;
                    newFiles.push(file);

                    renderGrid();
                };
                input.click();
            };

            box.appendChild(img);
            box.appendChild(del);
            box.appendChild(rep);
            grid.appendChild(box);
        });
    }

    // Render existing images on page load
    renderGrid();

    addInput.addEventListener('change', e => {
        Array.from(e.target.files).forEach(file => {
            images.push({ type: "new", filename: file.name, file });
            newFiles.push(file);
        });
        renderGrid();
    });

    new Sortable(grid, {
        animation: 150,
        onEnd: () => {
            const reordered = [];
            const boxes = Array.from(grid.children);

            boxes.forEach(box => {
                const oldIndex = parseInt(box.dataset.index);
                reordered.push(images[oldIndex]);
            });

            images = reordered;
             // Reassign correct indexes
            images.forEach((img, i) => {
                grid.children[i].dataset.index = i;
            });
        }
    });

    document.getElementById('editForm').addEventListener('submit', () => {
    document.getElementById('delete_images').value = JSON.stringify(deleteList);
    document.getElementById('replace_map').value = JSON.stringify(replaceMap);

    const order = images.map(img => img.type === "existing" ? img.filename : img.file.name);
    document.getElementById('image_order').value = JSON.stringify(order);
    
    // rebuild the file input
    const dt = new DataTransfer();
    newFiles.forEach(file => dt.items.add(file));
    document.getElementById('addImagesInput').files = dt.files;
});
</script>
</body>
</html>
