<div class="flex items-center justify-center min-h-screen px-4 md:ml-20">
    <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 max-w-2xl w-full text-center">
        <!-- Construction Icon with Animation -->
        <div class="mb-8">
            <div class="inline-block animate-bounce">
                <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                    class="text-emerald-600">
                    <path d="M12 2v20"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
        </div>

        <!-- Title -->
        <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-4">
            Work in Progress
        </h1>

        <!-- Description -->
        <p class="text-lg text-slate-600 mb-8 leading-relaxed">
            Fitur ini sedang dalam tahap pengembangan. Kami bekerja keras untuk menghadirkan sesuatu yang luar biasa untuk Anda!
        </p>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full animate-pulse" 
                    style="width: 65%; animation: progress 2s ease-in-out infinite;">
                </div>
            </div>
            <p class="text-sm text-slate-500 mt-2">Estimasi: Segera hadir...</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="javascript:history.back()" 
                class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-emerald-700 text-white px-8 py-3 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 19-7-7 7-7"/>
                    <path d="M19 12H5"/>
                </svg>
                Kembali
            </a>
            
            <?php if (auth()->check()): ?>
                <?php if (auth()->user()->isAdmin()): ?>
                    <a href="/admin" 
                        class="inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-8 py-3 rounded-xl font-semibold transition-all focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/>
                            <path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        </svg>
                        Dashboard
                    </a>
                <?php else: ?>
                    <a href="/dashboard" 
                        class="inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-8 py-3 rounded-xl font-semibold transition-all focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/>
                            <path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        </svg>
                        Dashboard
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <a href="/" 
                    class="inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-8 py-3 rounded-xl font-semibold transition-all focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/>
                        <path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    </svg>
                    Beranda
                </a>
            <?php endif; ?>
        </div>

        <!-- Additional Info -->
        <div class="mt-8 pt-8 border-t border-gray-200">
            <p class="text-sm text-slate-500">
                Butuh bantuan? Hubungi 
                <a href="mailto:support@pnj.ac.id" class="text-emerald-600 hover:text-emerald-700 font-semibold underline">
                    support@pnj.ac.id
                </a>
            </p>
        </div>
    </div>
</div>

<style>
    @keyframes progress {
        0% { width: 45%; }
        50% { width: 75%; }
        100% { width: 45%; }
    }
</style>
