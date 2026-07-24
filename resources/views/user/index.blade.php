@extends('layouts.app')

@section('title', 'Data Anggota')



@section('mobile-nav')
    <a href="{{ route('admin.ringkasan') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150 hover:text-[var(--color-accent)]" style="color:var(--paper-dim);">
        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="13" r="8" />
            <path d="M12 13l4-4" />
            <path d="M8 3h8" />
        </svg>
        Admin
    </a>
    <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150" style="color:var(--color-accent);">
        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="9" cy="8" r="3.2" />
            <path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6" />
            <circle cx="17" cy="8.5" r="2.6" />
            <path d="M15.5 14.2c2.8.3 5 2.4 5 5.8" />
        </svg>
        Anggota
    </a>
    <a href="{{ route('katalog.index') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150 hover:text-[var(--color-accent)]" style="color:var(--paper-dim);">
        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3" y="3" width="7" height="7" rx="1.5" />
            <rect x="14" y="3" width="7" height="7" rx="1.5" />
            <rect x="3" y="14" width="7" height="7" rx="1.5" />
            <rect x="14" y="14" width="7" height="7" rx="1.5" />
        </svg>
        Katalog
    </a>
@endsection

@section('content')
<div class="animate-fade-in max-w-7xl mx-auto" x-data="{
    searchQ: '{{ request('q') }}',
    selectedRole: '{{ request('role', 'all') }}',
    showModal: false,
    modalMode: 'create', // 'create' or 'edit'
    formData: {
        id: '',
        nama_lengkap: '',
        username: '',
        role: 'mahasiswa',
        is_aktif: true,
    },
    submitFilter() {
        this.$refs.filterForm.submit();
    },
    openCreate() {
        this.modalMode = 'create';
        this.formData = { id: '', nama_lengkap: '', username: '', role: 'mahasiswa', is_aktif: true };
        this.$refs.mainForm.action = '{{ route('admin.users.store') }}';
        document.getElementById('method-input').value = 'POST';
        document.getElementById('pwd-label').innerHTML = 'Password <span class=\'text-red-500\'>*</span>';
        this.showModal = true;
    },
    openEdit(user) {
        this.modalMode = 'edit';
        this.formData = { 
            id: user.id_user, 
            nama_lengkap: user.nama_lengkap, 
            username: user.username, 
            role: user.role, 
            is_aktif: user.is_aktif == 1 
        };
        this.$refs.mainForm.action = '/admin/users/' + user.id_user;
        document.getElementById('method-input').value = 'PUT';
        document.getElementById('pwd-label').innerHTML = 'Password (Kosongkan jika tidak diubah)';
        this.showModal = true;
    }
}">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-serif font-semibold mb-1 tracking-tight" style="color:var(--paper);">Data Anggota</h2>
            <p class="text-[13.5px]" style="color:var(--paper-dim);">Manajemen akun pengguna dan hak akses</p>
        </div>
        <button @click="openCreate()" 
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-transform duration-200 hover:-translate-y-0.5 shadow-lg text-white" 
                style="background:var(--color-accent); box-shadow:0 4px 12px rgba(46,178,83,0.3);">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Tambah Akun
        </button>
    </div>

    {{-- Validation Errors or Success --}}
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl border flex gap-3 animate-fade-in" style="background:var(--color-red-soft); border-color:rgba(185, 80, 63, 0.4); color:var(--text-error);">
            <div class="text-[13px]">
                <div class="font-semibold mb-1">Terdapat kesalahan:</div>
                <ul class="list-disc list-inside space-y-0.5 ml-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl border animate-fade-in text-[13px] font-medium" style="background:var(--color-green-soft); border-color:rgba(46, 178, 83, 0.4); color:var(--color-accent);">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 rounded-2xl border animate-fade-in text-[13px] font-medium" style="background:var(--color-red-soft); border-color:rgba(185, 80, 63, 0.4); color:var(--text-error);">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter Bar --}}
    <form x-ref="filterForm" action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <div class="flex items-center gap-2 px-3 py-2 rounded-lg w-full md:w-[280px] transition-colors" style="background:var(--bg-input); border:1px solid var(--ink-line-2);">
            <svg class="w-4 h-4" style="color:var(--paper-dim)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="7" />
                <path d="M21 21l-4.3-4.3" />
            </svg>
            <input type="text" name="q" x-model="searchQ" @keydown.enter="submitFilter()" placeholder="Cari nama atau NIM…" class="bg-transparent border-none outline-none text-[13px] w-full" style="color:var(--paper);">
        </div>
        
        <select name="role" x-model="selectedRole" @change="submitFilter()" 
                class="px-3 py-2 rounded-lg border outline-none text-[13px] cursor-pointer" 
                style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper-dim);">
            <option value="all" style="background:var(--bg-panel); color:var(--paper);">Semua Role</option>
            <option value="super_admin" style="background:var(--bg-panel); color:var(--paper);">Super Admin</option>
            <option value="admin_pdd" style="background:var(--bg-panel); color:var(--paper);">Admin PDD</option>
            <option value="mahasiswa" style="background:var(--bg-panel); color:var(--paper);">Mahasiswa</option>
        </select>
    </form>

    {{-- Data Table --}}
    <div class="rounded-2xl border overflow-hidden" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-[13px]">
                <thead>
                    <tr>
                        <th class="py-3 px-4 font-semibold text-xs border-b" style="color:var(--text-muted); border-color:var(--ink-line);">Nama Lengkap</th>
                        <th class="py-3 px-4 font-semibold text-xs border-b" style="color:var(--text-muted); border-color:var(--ink-line);">NIM / Username</th>
                        <th class="py-3 px-4 font-semibold text-xs border-b" style="color:var(--text-muted); border-color:var(--ink-line);">Role</th>
                        <th class="py-3 px-4 font-semibold text-xs border-b" style="color:var(--text-muted); border-color:var(--ink-line);">Status</th>
                        <th class="py-3 px-4 font-semibold text-xs border-b text-right" style="color:var(--text-muted); border-color:var(--ink-line);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="transition-colors hover:bg-[var(--ink-line)]">
                        <td class="py-3 px-4 border-b font-medium whitespace-nowrap" style="border-color:var(--ink-line-2); color:var(--paper);">
                            {{ $user->nama_lengkap }}
                        </td>
                        <td class="py-3 px-4 border-b font-mono text-[11.5px] whitespace-nowrap" style="border-color:var(--ink-line-2); color:var(--paper-dim);">
                            {{ $user->username }}
                        </td>
                        <td class="py-3 px-4 border-b whitespace-nowrap" style="border-color:var(--ink-line-2);">
                            @if($user->role === 'super_admin')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" style="background:var(--color-navy-soft); color:var(--text-navy);">Super Admin</span>
                            @elseif($user->role === 'admin_pdd')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" style="background:var(--color-green-soft); color:var(--color-accent);">Admin PDD</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" style="background:var(--ink-line-2); color:var(--paper-dim);">Mahasiswa</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 border-b whitespace-nowrap" style="border-color:var(--ink-line-2);">
                            @if($user->is_aktif)
                                <span class="flex items-center gap-1.5 text-[11.5px]" style="color:var(--color-accent);"><span class="w-1.5 h-1.5 rounded-full bg-current"></span>Aktif</span>
                            @else
                                <span class="flex items-center gap-1.5 text-[11.5px]" style="color:var(--text-error);"><span class="w-1.5 h-1.5 rounded-full bg-current"></span>Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 border-b whitespace-nowrap text-right" style="border-color:var(--ink-line-2);">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openEdit({{ \Illuminate\Support\Js::from($user) }})" class="p-1.5 rounded transition-colors hover:bg-[var(--ink-line)] text-gray-400 hover:text-white" title="Edit">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>
                                
                                @if($user->id_user !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id_user) }}" method="POST" id="delete-user-{{ $user->id_user }}" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="showConfirm('Hapus Akun', 'Apakah Anda yakin ingin menghapus akun ini?', () => document.getElementById('delete-user-{{ $user->id_user }}').submit())" class="p-1.5 rounded transition-colors hover:bg-[var(--ink-line)] text-gray-400 hover:text-red-400" title="Hapus">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-[13px]" style="color:var(--text-muted);">Tidak ada data anggota ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4 flex justify-center">
        {{ $users->links() }}
    </div>

    {{-- Modal Create/Edit --}}
    <div x-show="showModal" style="display:none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showModal = false"
             class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
             
        {{-- Modal Content --}}
        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative w-full max-w-md rounded-2xl p-6 shadow-2xl overflow-y-auto max-h-[90vh]"
             style="background:var(--bg-panel); border:1px solid var(--ink-line-2);">
            
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-serif font-semibold" style="color:var(--paper);" x-text="modalMode === 'create' ? 'Tambah Akun' : 'Edit Akun'"></h3>
                    <p class="text-[12.5px] mt-1" style="color:var(--paper-dim);">Lengkapi form di bawah ini.</p>
                </div>
                <button @click="showModal = false" class="p-1 rounded-md transition-colors hover:bg-[var(--ink-line)]" style="color:var(--paper-dim);">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <form x-ref="mainForm" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" id="method-input" value="POST">
                
                <div class="space-y-4">
                    <div class="flex flex-col">
                        <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" x-model="formData.nama_lengkap" required
                               class="px-3 py-2 rounded-lg border outline-none text-[13px] transition-colors focus:border-[var(--color-accent)]" 
                               style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                    </div>
                    
                    <div class="flex flex-col">
                        <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">NIM / Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" x-model="formData.username" required
                               class="px-3 py-2 rounded-lg border outline-none text-[13px] font-mono transition-colors focus:border-[var(--color-accent)]" 
                               style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                    </div>
                    
                    <div class="flex flex-col">
                        <label class="text-[11.5px] font-medium mb-1.5" id="pwd-label" style="color:var(--paper-dim);">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" :required="modalMode === 'create'" minlength="6"
                               class="px-3 py-2 rounded-lg border outline-none text-[13px] transition-colors focus:border-[var(--color-accent)]" 
                               style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                    </div>
                    
                    <div class="flex flex-col">
                        <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Role Sistem <span class="text-red-500">*</span></label>
                        <select name="role" x-model="formData.role" required
                                class="px-3 py-2 rounded-lg border outline-none text-[13px] cursor-pointer" 
                                style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                            <option value="super_admin" style="background:var(--bg-panel);">Super Admin</option>
                            <option value="admin_pdd" style="background:var(--bg-panel);">Admin PDD</option>
                            <option value="mahasiswa" style="background:var(--bg-panel);">Mahasiswa</option>
                        </select>
                    </div>
                    
                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_aktif" value="1" x-model="formData.is_aktif" class="w-4 h-4 rounded border-gray-300 text-[var(--color-accent)] focus:ring-[var(--color-accent)]">
                            <span class="text-[13px]" style="color:var(--paper);">Akun Aktif (Dapat Login)</span>
                        </label>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" style="background:var(--bg-input); color:var(--paper-dim);">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-medium transition-colors shadow-lg text-white" style="background:var(--color-accent); box-shadow:0 4px 12px rgba(46,178,83,0.3);">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
