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


/**
 * AddressController
 * 
 * Handles all CRUD operations for addresses including image upload, processing, and management.
 * Features image resizing, validation, and proper error logging.
 */
class AddressController extends Controller
{
    /**
     * Show a single address
     * 
     * @param int $id - The address ID
     * @return \Illuminate\View\View - Returns the address detail view
     */
    public function show($id)
    {
        $address = Address::findOrFail($id);
        return view('addresses.show', compact('address'));
    }

    /**
     * Show the form to create a new address
     * 
     * @return \Illuminate\View\View - Returns the address creation form view
     */
    public function create()
    {
        return view('addresses.create');
    }

    
    /**
     * Store a new address with images
     * 
     * Validates address data and processes uploaded images:
     * - Resizes images to max 900x600px while maintaining aspect ratio
     * - Saves images as PNG files in public/images directory
     * - Stores image filenames as JSON array in database
     * - Logs processing errors for debugging
     * 
     * @param \Illuminate\Http\Request $request - Contains address data and image files
     * @return \Illuminate\Http\RedirectResponse - Redirects to address list with success message
     */
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

            // Array to store processed image filenames
            $newImages = [];

            if ($request->hasFile('images')) {

                // Create public/images directory if it doesn't exist
                $destination = public_path('images');
                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }

                Log::info('Processing ' . count($request->file('images')) . ' uploaded files.');

                // Process each uploaded image
                foreach ($request->file('images') as $index => $image) {
                    Log::info('Processing image ' . $index . ': ' . $image->getClientOriginalName() . ', size: ' . $image->getSize() . ', mime: ' . $image->getMimeType());
                    if ($image->isValid()) {
                        // Generate unique filename with UUID and PNG extension
                        $filename = Str::uuid() . '.png';
                        $fullPath = $destination . '/' . $filename;

                        try {
                            // Use Intervention Image library to process and optimize the image
                            $manager = new ImageManager(new Driver());
                            $processedImage = $manager->read($image)
                                // Resize to max 900x600px while preserving aspect ratio
                                ->resize(900, 600, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                })
                                // Encode as PNG with compression
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
                
                // Log completion of image processing
                Log::info('Image processing complete');
            } else {
                Log::info('No files uploaded.');
            }
                
            // Store image filenames as JSON string in database
            $validated['images'] = json_encode($newImages);
            Address::create($validated);

            // Redirect to address list with success message
            return redirect()->route('addresses.index')
                ->with('success', 'Address and images added successfully!');
    }

    /**
     * Show the form to edit an existing address
     * 
     * @param int $id - The address ID
     * @return \Illuminate\View\View - Returns the address edit form view
     */
    public function edit($id)
    {
        $address = Address::findOrFail($id);
        return view('addresses.edit', compact('address'));
    }

    /**
     * Update an existing address with new data and images
     * 
     * Handles complex image management scenarios:
     * - Deletes images marked for removal from filesystem
     * - Processes and saves new image uploads
     * - Maintains existing images that weren't deleted
     * - Respects user-defined image order
     * - Updates address fields
     * 
     * @param \Illuminate\Http\Request $request - Contains updated address data and image operations
     * @param int $id - The address ID to update
     * @return \Illuminate\Http\RedirectResponse - Redirects to address list
     */
    public function update(Request $request, $id)
    {
        $address = Address::findOrFail($id);

        // Step 1: Decode incoming JSON data from frontend
        // delete_images: array of filenames to delete
        // replace_map: mapping of old image names to replacement names
        // image_order: final desired order of images
        $deleteImages = json_decode($request->delete_images ?? '[]', true);
        $replaceMap   = json_decode($request->replace_map ?? '{}', true);
        $imageOrder   = json_decode($request->image_order ?? '[]', true);

        $finalImages = [];

        // Step 2: Delete images marked for removal from the filesystem
        foreach ($deleteImages as $filename) {
            $path = public_path('images/' . $filename);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        // Step 3: Process newly uploaded images
        $uploadedFiles = [];
        if ($request->hasFile('new_images')) {
            $manager = new ImageManager(new Driver());
            foreach ($request->file('new_images') as $file) {
                // Generate unique filename using uniqid
                $newName = uniqid() . '.' . $file->getClientOriginalExtension();

                // Save using Intervention Image library
                $image = $manager->read($file->getRealPath());
                $image->save(public_path('images/' . $newName));

                // Map original filename to new unique filename
                $uploadedFiles[$file->getClientOriginalName()] = $newName;
            }
        }

        // Step 4: Build final image list based on user's desired order
        // Handles three cases: existing images, new images, and replacements
        foreach ($imageOrder as $item) {

            // CASE A: Existing image that was NOT deleted
            // Keep the original filename if it wasn't marked for deletion
            if ($address->images && in_array($item, json_decode($address->images))) {
                if (!in_array($item, $deleteImages)) {
                    $finalImages[] = $item;
                }
                continue;
            }

            // CASE B: New image (freshly uploaded)
            // Use the new filename from the upload
            if (isset($uploadedFiles[$item])) {
                $finalImages[] = $uploadedFiles[$item];
                continue;
            }

            // CASE C: Replacement mapping (old image replaced with new one)
            // Use the replacement filename from the map
            if (isset($replaceMap[$item])) {
                $newOriginal  = $replaceMap[$item];

                if (isset($uploadedFiles[$newOriginal])) {
                    $finalImages[] = $uploadedFiles[$newOriginal];
                }
                continue;
            }
        }

        // Step 5: Update address fields with user input
        $address->street_address   = $request->street_address;
        $address->street_address_2 = $request->street_address_2;
        $address->city             = $request->city;
        $address->state            = $request->state;
        $address->zipcode          = $request->zipcode;

        // Step 6: Save the final image list as JSON and persist changes
        $address->images = json_encode($finalImages);
        $address->save();

        // Step 7: Redirect back to address list
        return redirect()->route('addresses.index');

    }

    /**
     * Lookup and display an address by ID
     * 
     * Validates that the ID is numeric before querying the database.
     * Redirects to address list with error if ID is invalid or address not found.
     * 
     * @param \Illuminate\Http\Request $request - Contains 'id' parameter
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse - Address detail view or redirect with error
     */
    public function lookup(Request $request)
    {
        $id = $request->input('id');
                
        // Validate that ID is numeric to prevent invalid lookups
        if (!is_numeric($id)) {
            return redirect()->route('addresses.index')->with('error', 'Invalid house ID.');
        }

        $address = Address::find($id);

        // Check if address exists in database
        if (!$address) {
            return redirect()->route('addresses.index')->with('error', 'Address not found for ID: ' . $id);
        }
        
        return view('addresses.show', compact('address'));
    }

    /**
     * Display all addresses
     * 
     * Retrieves all addresses sorted by most recently created first.
     * 
     * @return \Illuminate\View\View - Returns the address list view
     */
    public function index()
    {
        // Fetch all addresses, sorted by creation date (newest first)
        $addresses = Address::latest()->get();
        return view('addresses.index', compact('addresses'));
    }
  
    /**
     * Delete an address and all associated images
     * 
     * Removes image files from public/images directory and deletes the address record.
     * 
     * @param Address $address - The address to delete (model binding)
     * @return \Illuminate\Http\RedirectResponse - Redirects to address list with success message
     */
    public function destroy(Address $address)
    {
        // Delete all image files associated with this address
        if ($address->images) {
            $images = json_decode($address->images, true);
            if (is_array($images)) {
                foreach ($images as $filename) {
                    Storage::delete('public/images/' . $filename);
                }
            }
        }

        // Delete the address record from database
        $address->delete();
        
        // Redirect to address list with success message
        return redirect()->route('addresses.index')
            ->with('success', 'Address and images have been deleted successfully!');
    }

}