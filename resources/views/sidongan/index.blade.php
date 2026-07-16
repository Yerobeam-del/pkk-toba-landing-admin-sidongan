<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIDONGAN - Sistem Informasi Dokumen Organisasi Agenda dan Naskah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .hero-gradient { background: linear-gradient(135deg, #065f46 0%, #059669 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-signature text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">SIDONGAN</h1>
                        <p class="text-xs text-gray-500">PKK Kabupaten Toba</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="#dokumen" class="text-gray-600 hover:text-gray-900 font-medium">Dokumen</a>
                    <a href="#tentang" class="text-gray-600 hover:text-gray-900 font-medium">Tentang</a>
                    <a href="{{ route('sidongan.login') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-6 backdrop-blur-sm">
                <i class="fas fa-file-signature text-4xl text-white"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">SIDONGAN</h1>
            <p class="text-xl md:text-2xl mb-2 font-medium">Sistem Informasi Dokumen Organisasi Agenda dan Naskah</p>
            <p class="text-lg text-blue-100 mb-8">Tim Penggerak PKK Kabupaten Toba</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-3xl mx-auto mt-12">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6">
                    <div class="text-3xl font-bold mb-2">{{ $totalDocuments ?? 0 }}</div>
                    <div class="text-blue-100">Dokumen Tersedia</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6">
                    <div class="text-3xl font-bold mb-2">{{ $totalCategories ?? 0 }}</div>
                    <div class="text-blue-100">Kategori</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6">
                    <div class="text-3xl font-bold mb-2">24/7</div>
                    <div class="text-blue-100">Akses Online</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="py-12 -mt-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-search text-blue-600"></i>
                    Cari Dokumen
                </h2>
                <form method="GET" class="flex gap-3">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" placeholder="Cari berdasarkan judul, nomor dokumen, atau kata kunci..." class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">Cari</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Documents Section -->
    <section id="dokumen" class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 flex items-center gap-3">
                <i class="fas fa-file-alt text-blue-600"></i>
                Dokumen Terbaru
            </h2>

            @if(isset($documents) && $documents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($documents as $doc)
                <a href="{{ route('sidongan.show', $doc->slug) }}" class="block bg-white border border-gray-200 rounded-xl p-6 card-hover">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-alt text-white text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800 text-lg mb-1 line-clamp-2">{{ $doc->title }}</h3>
                            @if($doc->category)
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">{{ $doc->category->name }}</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($doc->description ?? '', 100) }}</p>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100 text-sm text-gray-500">
                        <span><i class="far fa-calendar mr-1"></i>{{ $doc->document_date?->format('d M Y') ?? $doc->created_at->format('d M Y') }}</span>
                        <span><i class="fas fa-download mr-1"></i>{{ $doc->formatted_size ?? '-' }}</span>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-3xl text-gray-400"></i>
                </div>
                <p class="text-gray-600 font-medium">Belum ada dokumen yang tersedia</p>
            </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} SIDONGAN - Kabupaten Toba. Seluruh hak cipta dilindungi.</p>
        </div>
    </footer>
</body>
</html>