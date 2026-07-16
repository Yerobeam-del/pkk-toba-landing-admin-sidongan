<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->title }} - SIDONGAN</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('sidongan') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-signature text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">SIDONGAN</h1>
                        <p class="text-xs text-gray-500">PKK Kabupaten Toba</p>
                    </div>
                </a>
                <div class="flex items-center gap-4">
                    <a href="{{ route('sidongan') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    <a href="{{ route('sidongan.login') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Document Detail -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-green-600 text-white p-8">
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 backdrop-blur-sm">
                        @if(str_contains($document->file_type, 'pdf'))
                            <i class="fas fa-file-pdf text-white text-3xl"></i>
                        @elseif(str_contains($document->file_type, 'word'))
                            <i class="fas fa-file-word text-white text-3xl"></i>
                        @else
                            <i class="fas fa-file-alt text-white text-3xl"></i>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h1 class="text-2xl md:text-3xl font-bold mb-2">{{ $document->title }}</h1>
                        @if($document->category)
                        <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-sm font-medium backdrop-blur-sm">
                            {{ $document->category->name }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">
                <!-- Document Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-500 mb-1">Nomor Dokumen</div>
                        <div class="font-semibold text-gray-800">{{ $document->document_number ?? '-' }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-500 mb-1">Tanggal Dokumen</div>
                        <div class="font-semibold text-gray-800">{{ $document->document_date?->format('d M Y') ?? '-' }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-500 mb-1">Ukuran File</div>
                        <div class="font-semibold text-gray-800">{{ $document->formatted_size ?? '-' }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-500 mb-1">Tanggal Upload</div>
                        <div class="font-semibold text-gray-800">{{ $document->created_at->format('d M Y') }}</div>
                    </div>
                </div>

                <!-- Description -->
                @if($document->description)
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-3">Deskripsi</h2>
                    <div class="bg-gray-50 rounded-lg p-4 text-gray-700">
                        {{ $document->description }}
                    </div>
                </div>
                @endif

                <!-- Tags -->
                @if($document->tags && $document->tags->count() > 0)
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-3">Tags</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($document->tags as $tag)
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                            #{{ $tag->name }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Download Button -->
                <div class="flex items-center justify-center gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ $document->file_url }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2">
                        <i class="fas fa-download"></i>
                        Download Dokumen
                    </a>
                    <a href="{{ route('sidongan') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-8 py-3 rounded-lg font-semibold transition-colors">
                        <i class="fas fa-list mr-2"></i> Lihat Semua Dokumen
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400 text-sm">
                &copy; {{ date('Y') }} SIDONGAN - Kabupaten Toba. Seluruh hak cipta dilindungi.
            </p>
        </div>
    </footer>
</body>
</html>