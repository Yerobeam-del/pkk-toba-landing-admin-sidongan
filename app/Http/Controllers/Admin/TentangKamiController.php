<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TentangKami;
use Illuminate\Http\Request;

class TentangKamiController extends Controller
{
    public function index()
    {
        $tentang = TentangKami::getFirst();
        return view('admin.tentang.index', compact('tentang'));
    }
    
    public function update(Request $request)
    {
        $tentang = TentangKami::getFirst();
        
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'subjudul' => 'required|string|max:255',
            'heading' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'programs' => 'required|array|min:1',
            'programs.*' => 'required|string|max:255',
            'maps_embed_code' => 'required|string',
            'maps_link' => 'nullable|url'
        ]);
        
        $tentang->update([
            'judul' => $validated['judul'],
            'subjudul' => $validated['subjudul'],
            'heading' => $validated['heading'],
            'deskripsi' => $validated['deskripsi'],
            'program_list' => $validated['programs'],
            'maps_embed_code' => $validated['maps_embed_code'],
            'maps_link' => $validated['maps_link']
        ]);
        
        return redirect()->route('admin.tentang.index')
            ->with('success', 'Konten Tentang Kami berhasil diperbarui');
    }
}