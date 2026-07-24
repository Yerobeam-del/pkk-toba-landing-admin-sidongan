<section class="news-home-section" style="padding: 4rem 2rem;">
    <div style="text-align: center; margin-bottom: 3rem;">
        <div style="display: inline-block; padding: 0.5rem 1rem; background: rgba(39,103,73,0.1); border-radius: 50px; color: #276749; font-size: 0.75rem; font-weight: 700; margin-bottom: 1rem;">BERITA TERKINI</div>
        <h2 style="font-size: 2.5rem; font-weight: 800; color: #0f766e; margin: 0 0 0.5rem 0;">Kabar Terbaru PKK</h2>
        <p style="color: #64748b; font-size: 1.05rem; margin: 0;">Ikuti perkembangan terbaru dari kegiatan dan program PKK Kabupaten Toba.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; max-width: 1200px; margin: 0 auto;">
        @php
            try {
                $recentNews = \App\Models\News::published()->latest('published_at')->limit(3)->get();
            } catch (\Exception $e) {
                $recentNews = collect();
            }
        @endphp

        @forelse($recentNews as $news)
        <a href="{{ route('news.show', $news->slug ?? $news->id) }}" style="text-decoration: none; color: inherit; display: block; min-height: 100%; touch-action: manipulation;">
            <article style="background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); transition: transform 0.3s; min-height: 100%;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <img src="{{ $news->image_path ? asset('storage/' . $news->image_path) : 'https://via.placeholder.com/400x200?text=No+Image' }}"
                    alt="{{ $news->title }}"
                    style="width: 100%; height: 200px; object-fit: cover;">
                <div style="padding: 1.5rem;">
                    <div style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 0.75rem;">
                        {{ $news->published_at?->format('d M Y') ?? '-' }}
                    </div>
                    <span style="display: inline-block; padding: 0.25rem 0.75rem; background: rgba(39,103,73,0.1); color: #276749; border-radius: 50px; font-size: 0.75rem; font-weight: 600; margin-bottom: 0.75rem;">{{ $news->category ?? 'Umum' }}</span>
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0;">{{ Str::limit($news->title, 60) }}</h3>
                    <p style="color: #64748b; font-size: 0.9rem; line-height: 1.6; margin: 0;">{{ Str::limit($news->excerpt ?? $news->content, 100) }}</p>
                </div>
            </article>
        </a>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 2rem; background: linear-gradient(135deg, rgba(39,103,73,0.05), rgba(56,161,105,0.05)); border-radius: 20px;">
            <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, rgba(39,103,73,0.1), rgba(56,161,105,0.1)); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#276749" stroke-width="1.5">
                    <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/>
                    <path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/>
                </svg>
            </div>
            <h3 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">Belum Ada Berita</h3>
            <p style="color: #64748b; font-size: 0.95rem; margin: 0 0 1.5rem 0;">Tim kami sedang mempersiapkan informasi terkini.</p>
        </div>
        @endforelse
    </div>
</section>
