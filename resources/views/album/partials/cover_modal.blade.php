<!-- Cover Repositioning Modal -->
<div x-data="{
    isOpen: false,
    isLoading: false,
    postUrl: '',
    imageSrc: '',
    posX: 50,
    posY: 50,
    isDragging: false,
    startX: 0,
    startY: 0,
    startPosX: 50,
    startPosY: 50,
    previousOverflow: '',
    get position() { return `${this.posX}% ${this.posY}%`; },
    openModal(e) {
        this.postUrl = e.detail.url;
        this.imageSrc = e.detail.imageSrc;
        this.posX = 50;
        this.posY = 50;
        this.isOpen = true;
        this.previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
    },
    closeModal() {
        this.isOpen = false;
        document.body.style.overflow = this.previousOverflow;
    },
    startDrag(e) {
        this.isDragging = true;
        this.startX = e.clientX || (e.touches && e.touches[0].clientX);
        this.startY = e.clientY || (e.touches && e.touches[0].clientY);
        this.startPosX = this.posX;
        this.startPosY = this.posY;
        document.body.style.userSelect = 'none';
    },
    onDrag(e) {
        if (!this.isDragging) return;
        let clientX = e.clientX;
        let clientY = e.clientY;
        if (e.touches && e.touches.length > 0) {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        }
        if (clientX === undefined) return;
        
        const deltaX = clientX - this.startX;
        const deltaY = clientY - this.startY;
        
        const sensitivity = 0.3;
        this.posX = Math.max(0, Math.min(100, this.startPosX - (deltaX * sensitivity)));
        this.posY = Math.max(0, Math.min(100, this.startPosY - (deltaY * sensitivity)));
    },
    stopDrag() {
        this.isDragging = false;
        document.body.style.userSelect = '';
    },
    async saveCover() {
        this.isLoading = true;
        try {
            const response = await fetch(this.postUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ cover_position: this.position })
            });
            const data = await response.json();
            
            if (response.ok && data.success) {
                showToast('success', 'Berhasil', data.message || 'Cover album berhasil diperbarui.');
                this.closeModal();
            } else {
                showToast('error', 'Gagal', data.error || 'Terjadi kesalahan.');
            }
        } catch (error) {
            showToast('error', 'Gagal', 'Terjadi kesalahan jaringan.');
        } finally {
            this.isLoading = false;
        }
    }
}" @open-cover-modal.window="openModal($event)" @mousemove.window="onDrag($event)" @touchmove.window="onDrag($event)" @mouseup.window="stopDrag()" @touchend.window="stopDrag()" x-show="isOpen" x-cloak class="fixed inset-0 z-[10000] flex items-center justify-center p-4">
    
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" x-show="isOpen" x-transition.opacity @click="closeModal()"></div>
    
    <!-- Modal Content -->
    <div class="relative w-full max-w-2xl rounded-2xl shadow-2xl flex flex-col overflow-hidden" 
         style="background:var(--bg-panel); border:1px solid var(--ink-line-2);"
         x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        
        <div class="flex justify-between items-center px-6 py-4 border-b" style="border-color:var(--ink-line-2); background:var(--bg-input);">
            <div>
                <h3 class="text-lg font-bold" style="color:var(--paper);">Atur Posisi Cover</h3>
                <p class="text-[12px] mt-0.5" style="color:var(--paper-dim);">Sesuaikan tampilan area gambar untuk cover album.</p>
            </div>
            <button @click="closeModal()" class="p-2 -mr-2 rounded-lg transition-colors hover:bg-black/5 text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <div class="p-6 flex flex-col items-center gap-6">
            <!-- Preview Box (Aspect Ratio usually ~16:9 or similar to catalog card 160px height, full width) -->
            <!-- We will use a 16:9 container as an approximation of the cover card aspect ratio -->
            <div class="w-full relative overflow-hidden border cursor-move" 
                 style="border-color:var(--ink-line-2); aspect-ratio: 16/9; background: var(--bg-body);"
                 @mousedown.prevent="startDrag($event)"
                 @touchstart.prevent="startDrag($event)">
                <img :src="imageSrc" class="w-full h-full object-cover" :style="`object-position: ${position}; ${isDragging ? '' : 'transition: object-position 0.3s;'}`" alt="Preview Cover" draggable="false">
                
                <!-- Overlay Grid to help visual alignment -->
                <div class="absolute inset-0 pointer-events-none opacity-20 border border-white" style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 33.33% 33.33%;"></div>
                <div class="absolute inset-0 pointer-events-none flex items-center justify-center" x-show="!isDragging && posX === 50 && posY === 50">
                     <div class="px-3 py-1.5 rounded-md bg-black/50 text-white text-[11px] font-medium backdrop-blur-sm">Seret untuk mengatur posisi</div>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 border-t flex justify-end gap-3" style="border-color:var(--ink-line-2); background:var(--bg-input);">
            <button type="button" @click="closeModal()" class="px-4 py-2 rounded-lg text-[13px] font-medium transition-colors hover:bg-black/5" style="color:var(--paper-dim);">Batal</button>
            <button type="button" @click="saveCover()" :disabled="isLoading" class="px-4 py-2 rounded-lg text-[13px] font-medium text-white transition-all shadow-lg flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed" style="background:var(--color-accent);">
                <svg x-show="isLoading" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>
                Simpan Cover
            </button>
        </div>
    </div>
</div>
