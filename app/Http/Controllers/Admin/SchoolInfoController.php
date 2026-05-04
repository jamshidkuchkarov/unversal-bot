<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolInfo;
use App\Support\AdminSchoolContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolInfoController extends Controller
{
    public function __construct(private readonly AdminSchoolContext $schoolContext) {}

    /**
     * Show the form for editing school info
     */
    public function edit(): View
    {
        $school = $this->schoolContext->current(auth()->user());
        $info = $school->info ?? new SchoolInfo(['school_id' => $school->id]);

        return view('admin.school-info.edit', compact('school', 'info'));
    }

    /**
     * Update school info
     */
    public function update(Request $request): RedirectResponse
    {
        $school = $this->schoolContext->current(auth()->user());

        $validated = $request->validate([
            'about_text_uz' => 'nullable|string',
            'about_text_ru' => 'nullable|string',
            'history_text_uz' => 'nullable|string',
            'history_text_ru' => 'nullable|string',
            'mission_text_uz' => 'nullable|string',
            'mission_text_ru' => 'nullable|string',
            'vision_text_uz' => 'nullable|string',
            'vision_text_ru' => 'nullable|string',
            'director_name' => 'nullable|string|max:255',
            'director_photo' => 'nullable|image|max:5120',
            'director_message_uz' => 'nullable|string',
            'director_message_ru' => 'nullable|string',
            'video_url' => 'nullable|url|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:100',
            'address_uz' => 'nullable|string',
            'address_ru' => 'nullable|string',
            'map_latitude' => 'nullable|numeric|between:-90,90',
            'map_longitude' => 'nullable|numeric|between:-180,180',
            'gallery_images.*' => 'nullable|image|max:5120',
        ]);

        // Handle director photo upload
        if ($request->hasFile('director_photo')) {
            $path = $request->file('director_photo')->store('school-info/directors', 'public');
            $validated['director_photo'] = $path;

            // Delete old photo
            if ($school->info?->director_photo) {
                Storage::disk('public')->delete($school->info->director_photo);
            }
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryPaths[] = $image->store('school-info/gallery', 'public');
            }

            $existingGallery = $school->info?->gallery_images ?? [];
            $validated['gallery_images'] = array_merge($existingGallery, $galleryPaths);
        }

        $school->info()->updateOrCreate(
            ['school_id' => $school->id],
            $validated
        );

        return redirect()->back()->with('success', 'Maktab ma\'lumotlari saqlandi');
    }

    /**
     * Delete gallery image
     */
    public function deleteGalleryImage(Request $request): RedirectResponse
    {
        $school = $this->schoolContext->current(auth()->user());
        $info = $school->info;

        if (!$info) {
            return redirect()->back()->with('error', 'Ma\'lumot topilmadi');
        }

        $imagePath = $request->input('image_path');
        $gallery = $info->gallery_images ?? [];

        if (($key = array_search($imagePath, $gallery)) !== false) {
            unset($gallery[$key]);
            Storage::disk('public')->delete($imagePath);

            $info->update(['gallery_images' => array_values($gallery)]);

            return redirect()->back()->with('success', 'Rasm o\'chirildi');
        }

        return redirect()->back()->with('error', 'Rasm topilmadi');
    }
}
