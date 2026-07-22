<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSliderController extends Controller
{
    /**
     * Batas maksimal gambar yang bisa diupload
     */
    const MAX_SLIDERS = 10;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil per_page dari request (default 5, max 10)
        $perPage = request('per_page', 5);
        $perPage = max(5, min((int)$perPage, self::MAX_SLIDERS));

        $sliders = HeroSlider::orderBy('sort_order')->paginate($perPage);
        $totalSliders = HeroSlider::count();
        $maxSliders = self::MAX_SLIDERS;

        return view('admin.hero-sliders.index', compact('sliders', 'totalSliders', 'maxSliders', 'perPage'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // VALIDASI MAKSIMAL 10 GAMBAR
        $currentCount = HeroSlider::count();
        if ($currentCount >= self::MAX_SLIDERS) {
            return redirect()->route('admin.hero-sliders.index')
                ->with('error', 'Batas maksimal ' . self::MAX_SLIDERS . ' gambar sudah tercapai. Hapus beberapa gambar terlebih dahulu untuk mengupload yang baru.');
        }

        $validated = $request->validate([
            'image' => 'required|image|max:5120', // 5MB
            'display_duration' => 'nullable|integer|min:3|max:30',
        ]);

        // Upload image - SIMPAN KE kolom image_path
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('hero-sliders', 'public');
        }

        // Auto-generate sort order (urutan terakhir)
        $maxSortOrder = HeroSlider::max('sort_order') ?? 0;

        HeroSlider::create([
            'image_path' => $imagePath,
            'display_duration' => $validated['display_duration'] ?? 5,
            'sort_order' => $maxSortOrder + 1,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.hero-sliders.index')
            ->with('success', 'Slide berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HeroSlider $heroSlider)
    {
        $validated = $request->validate([
            'image' => 'nullable|image|max:5120',
            'display_duration' => 'nullable|integer|min:3|max:30',
        ]);

        $updateData = [
            'display_duration' => $validated['display_duration'] ?? $heroSlider->display_duration,
            'is_active' => $request->has('is_active'),
        ];

        // Upload image baru jika ada
        if ($request->hasFile('image')) {
            // Hapus image lama
            if ($heroSlider->image_path && Storage::disk('public')->exists($heroSlider->image_path)) {
                Storage::disk('public')->delete($heroSlider->image_path);
            }
            $updateData['image_path'] = $request->file('image')->store('hero-sliders', 'public');
        }

        $heroSlider->update($updateData);

        return redirect()->route('admin.hero-sliders.index')
            ->with('success', 'Slide berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HeroSlider $heroSlider)
    {
        // Hapus image dari storage
        if ($heroSlider->image_path && Storage::disk('public')->exists($heroSlider->image_path)) {
            Storage::disk('public')->delete($heroSlider->image_path);
        }

        $heroSlider->delete();

        return redirect()->route('admin.hero-sliders.index')
            ->with('success', 'Slide berhasil dihapus!');
    }

    /**
     * Reorder sliders via drag & drop.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array'
        ]);

        foreach ($request->order as $index => $id) {
            HeroSlider::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
