<?php

namespace App\Http\Controllers;
use Intervention\Image\Encoders\PngEncoder;
use Illuminate\Support\Str;
use App\Models\Address; // Import the Address model
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class AddressController extends Controller
{
    public function show($id)
    {
        $address = Address::findOrFail($id);
        return view('addresses.show', compact('address'));
    }

    public function create()
    {
        return view('addresses.create');
    }

    
    public function store(Request $request) {
            // Validate all input including the image
            $validated = $request->validate([
                'street_address'     => 'required|string|max:255',
                'street_address_2'   => 'nullable|string|max:255',
                'city'               => 'required|string|max:255',
                'state'              => 'required|string|max:255',
                'zipcode'            => 'required|string|max:20',
                'images.*'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
                'images'             => 'nullable|array|max:15'
            ]);

            $newImages = [];

            if ($request->hasFile('images')) {

                // Ensure the directory exists once before the loop
                $destination = public_path('images');
                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }

                Log::info('Processing ' . count($request->file('images')) . ' uploaded files.');

                foreach ($request->file('images') as $index => $image) {
                    Log::info('Processing image ' . $index . ': ' . $image->getClientOriginalName() . ', size: ' . $image->getSize() . ', mime: ' . $image->getMimeType());
                    if ($image->isValid()) {
                        $filename = Str::uuid() . '.png';
                        $fullPath = $destination . '/' . $filename;

                        try {
                            $manager = new ImageManager(new Driver());
                            $processedImage = $manager->read($image)
                                ->resize(900, 600, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                })
                                ->encode(new PngEncoder(false, 80, true))
                                ->save($fullPath);

                            $newImages[] = $filename;
                            Log::info('Saved image: ' . $fullPath);
                        } catch (\Exception $e) {
                            Log::error('Failed to process image ' . $index . ': ' . $e->getMessage());
                        }
                    } else {
                        Log::warning('Image ' . $index . ' is not valid. Error: ' . $image->getError());
                    }
                }
            } else {
                Log::info('No files uploaded.');
            }
                
            $validated['images'] = json_encode($newImages);
            Address::create($validated);

            return redirect()->route('addresses.index')
                ->with('success', 'Address and images added successfully!');
    }

    public function edit($id)
    {
        $address = Address::findOrFail($id);
        return view('addresses.edit', compact('address'));
    }

    public function update(Request $request, $id)
    {
        $address = Address::findOrFail($id);

        // 1.Decode incoming JSON data
        $deleteImages = json_decode($request->delete_images ?? '[]', true);
        $replaceMap   = json_decode($request->replace_map ?? '{}', true);
        $imageOrder   = json_decode($request->image_order ?? '[]', true);

        $finalImages = [];

        // 2.DELETE IMAGES
        foreach ($deleteImages as $filename) {
            $path = public_path('images/' . $filename);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        // 3. PROCESS NEW UPLOADS
        $uploadedFiles = [];
        if ($request->hasFile('new_images')) {
            $manager = new ImageManager(new Driver());
            foreach ($request->file('new_images') as $file) {
                $newName = uniqid() . '.' . $file->getClientOriginalExtension();

                // Save using Intervention Image v3
                $image = $manager->read($file->getRealPath());
                $image->save(public_path('images/' . $newName));

                // Map originalName → newUniqueName
                $uploadedFiles[$file->getClientOriginalName()] = $newName;
            }
        }

        // 4. BUILD FINAL IMAGE LIST BASED ON ORDER
        foreach ($imageOrder as $item) {

            // CASE A: Existing image that was NOT deleted
            if ($address->images && in_array($item, json_decode($address->images))) {
                if (!in_array($item, $deleteImages)) {
                    $finalImages[] = $item;
                }
                continue;
            }

            // CASE B: New image (added or replacement)
            if (isset($uploadedFiles[$item])) {
                $finalImages[] = $uploadedFiles[$item];
                continue;
            }

            // CASE C: Replacement mapping (old → new)
            if (isset($replaceMap[$item])) {
                $newOriginal  = $replaceMap[$item];

                if (isset($uploadedFiles[$newOriginal])) {
                    $finalImages[] = $uploadedFiles[$newOriginal];
                }
                continue;
            }
        }

        // 5. SAVE ADDRESS FIELDS
        $address->street_address   = $request->street_address;
        $address->street_address_2 = $request->street_address_2;
        $address->city             = $request->city;
        $address->state            = $request->state;
        $address->zipcode          = $request->zipcode;

        // 6. SAVE FINAL IMAGE ARRAY
        $address->images = json_encode($finalImages);
        $address->save();

        // 7. REDIRECT TO HOME PAGE
        return redirect()->route('addresses.index');

    }

    public function lookup(Request $request)
    {
        $id = $request->input('id');
                
        if (!is_numeric($id)) {
            return redirect()->route('addresses.index')->with('error', 'Invalid house ID.');
        }

        $address = Address::find($id);

        // Optionally validate the input
        if (!$address) {
            return redirect()->route('addresses.index')->with('error', 'Address not found for ID: ' . $id);
        }
        
        return view('addresses.show', compact('address'));
    }

    public function index()
    {
        $addresses = Address::latest()->get(); // newest first
        return view('addresses.index', compact('addresses'));
    }
  
    public function destroy(Address $address)
{
    if ($address->images) {
        $images = json_decode($address->images, true);
        if (is_array($images)) {
            foreach ($images as $filename) {
                Storage::delete('public/images/' . $filename);
            }
        }
    }

    $address->delete();
    
    return redirect() ->route('addresses.index')
    ->with('success', 'Address and images have been deleted successfully!');
}

}