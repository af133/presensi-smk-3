@extends('guru.layout')

@section('content')
<div class="max-w-3xl mx-auto p-4 md:p-6 pb-10" x-data="dashboard()">

    {{-- ===== KARTU SELAMAT DATANG ===== --}}
    <div class="relative overflow-hidden rounded-2xl bg-slate-900 p-5 md:p-7 mb-5 shadow-sm">
        <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-indigo-500/20"></div>
        <div class="absolute -right-2 bottom-0 w-20 h-20 rounded-full bg-amber-400/10"></div>

        <div class="relative">
            <p class="text-slate-400 text-xs font-medium uppercase tracking-wider">Selamat datang kembali</p>
            <h2 class="text-white text-xl md:text-2xl font-bold mt-1.5 truncate">
                {{ auth()->user()->guru->name ?? auth()->user()->name ?? '-' }}
            </h2>
            <p class="text-slate-400 text-sm mt-1">
                NIP {{ auth()->user()->guru->nip ?? auth()->user()->nip ?? '-' }}
            </p>
        </div>
    </div>

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl md:text-2xl font-bold text-slate-800">Jadwal Kelas Anda</h1>
        <span class="text-xs font-semibold text-slate-400">{{ count($schedules) }} jadwal</span>
    </div>

    <div class="bg-white rounded-2xl p-4 md:p-5 shadow-sm border border-gray-100 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">
                {{ $currentDate->translatedFormat('F Y') }}
            </h2>
            <div class="flex items-center gap-1">
                <a href="?date={{ $weekStart->copy()->subWeek()->format('Y-m-d') }}"
                   class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 active:bg-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <button @click="showDatePicker = true"
                        class="p-2 rounded-lg text-indigo-600 hover:bg-indigo-50 active:bg-indigo-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>
                <a href="?date={{ $weekStart->copy()->addWeek()->format('Y-m-d') }}"
                   class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 active:bg-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
        <div class="grid grid-cols-7 gap-1 sm:gap-2">
            @foreach($dates as $d)
                <a href="?date={{ $d->format('Y-m-d') }}"
                   class="flex flex-col items-center justify-center py-2 sm:py-3 rounded-xl sm:rounded-2xl border-2 transition-all
                   {{ $d->isSameDay($currentDate)
                       ? 'border-indigo-600 bg-indigo-600 text-white shadow-sm'
                       : 'border-transparent hover:bg-gray-50 text-gray-700' }}">
                    <span class="text-[10px] font-bold opacity-70 mb-1">
                        {{ $d->translatedFormat('D') }}
                    </span>
                    <span class="text-sm sm:text-base font-bold">{{ $d->format('d') }}</span>
                </a>
            @endforeach
        </div>
    </div>
    <div class="grid grid-cols-1 gap-3 sm:gap-4">
        @forelse($schedules as $schedule)
            @php
                $presence     = $schedule->merged_presences->first();
                $hasJournal   = $presence && $presence->journal;
                $presenceId   = $presence ? $presence->id : '';
                $journalTopic = $hasJournal ? addslashes($presence->journal->topic) : '';
                $journalUrl   = $presenceId ? route('guru.jurnal.store', $presenceId) : '';
            @endphp

            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:border-gray-200 transition-colors">
                <div class="mb-4">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">
                            {{ \Carbon\Carbon::parse($schedule->merged_start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->merged_end_time)->format('H:i') }}
                        </span>
                        @if(count($schedule->schedule_ids) > 1)
                            <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">
                                {{ count($schedule->schedule_ids) }} sesi
                            </span>
                        @endif
                    </div>
                    <h3 class="font-bold text-gray-900 text-base sm:text-lg mt-2">{{ $schedule->subject->name }}</h3>
                </div>

                <div class="space-y-1.5 text-sm text-gray-600 mb-5">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span class="truncate">{{ $schedule->classroom->name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m4-3h2m6 0h2M7 14h2m6 0h2M7 10h2m6 0h2M7 6h2m6 0h2" /></svg>
                        <span class="truncate">{{ $schedule->rombel->name }}</span>
                    </div>
                </div>

               <div class="flex flex-row gap-2">
                    <a href="{{ route('guru.presensi.create', ['id' => $schedule->id, 'date' => $date]) }}"
                    class="flex-1 text-center py-2.5 rounded-xl bg-indigo-600 text-white text-xs sm:text-sm font-semibold hover:bg-indigo-700 active:bg-indigo-800 transition whitespace-nowrap">
                        {{ $schedule->merged_presences->isNotEmpty() ? 'Detail' : 'Presensi' }}
                    </a>

                    <button type="button"
                            @click="openJournalModal('{{ $presenceId }}', '{{ $journalTopic }}', '{{ $journalUrl }}')"
                            class="flex-1 text-center py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all whitespace-nowrap
                            {{ $presence ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 active:bg-emerald-300' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
                            {{ !$presence ? 'disabled' : '' }}>
                        {{ $hasJournal ? 'Edit Jurnal' : 'Tambah Jurnal' }}
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-16 sm:py-20 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <p class="text-gray-400 font-medium">Tidak ada jadwal untuk hari ini</p>
            </div>
        @endforelse
    </div>

    <div x-show="showDatePicker"
         x-cloak
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
         @click.self="showDatePicker = false">
        <div class="bg-white rounded-2xl p-5 sm:p-6 w-full max-w-xs shadow-xl">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Pilih Tanggal</h3>
            <input type="date"
                   x-model="pickedDate"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <div class="flex gap-2 mt-4">
                <button @click="showDatePicker = false"
                        class="flex-1 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-500 hover:bg-gray-50 transition">
                    Batal
                </button>
                <button @click="goToDate()"
                        class="flex-1 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
                    Pilih
                </button>
            </div>
        </div>
    </div>
    <div x-show="showModal"
         x-cloak
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 overflow-y-auto"
         @click.self="showModal = false">
        <div class="bg-white rounded-2xl p-5 sm:p-6 w-full max-w-sm shadow-xl my-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Jurnal Mengajar</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 p-1 -mr-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mb-4">
                <label class="text-xs text-gray-500 mb-1.5 block font-medium">Topik / Tema Pembelajaran</label>
                <textarea x-model="journalTopic"
                          rows="4"
                          placeholder="Contoh: Persamaan kuadrat dan penerapannya..."
                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-400 resize-none"></textarea>
            </div>

            <p x-show="journalMessage"
               x-text="journalMessage"
               x-cloak
               class="text-xs text-emerald-600 mb-3 font-medium"></p>
            <p x-show="journalError"
               x-text="journalError"
               x-cloak
               class="text-xs text-red-500 mb-3"></p>

            <div class="flex flex-col-reverse sm:flex-row gap-2">
                <button @click="showModal = false"
                        class="flex-1 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-500 hover:bg-gray-50 transition">
                    Batal
                </button>
                <button @click="saveJournal()"
                        :disabled="journalLoading"
                        class="flex-1 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition disabled:opacity-50">
                    <span x-show="!journalLoading">Simpan</span>
                    <span x-show="journalLoading" x-cloak>Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function dashboard() {
    return {
        showModal: false,
        showDatePicker: false,
        pickedDate: '{{ $date }}',
        journalSaveUrl: '',
        journalTopic: '',
        journalPresenceId: '',
        journalMessage: '',
        journalError: '',
        journalLoading: false,

        openJournalModal(presenceId, topic, url) {
            if (!presenceId) return;
            this.journalPresenceId = presenceId;
            this.journalTopic      = topic;
            this.journalSaveUrl    = url;
            this.journalMessage    = '';
            this.journalError      = '';
            this.showModal         = true;
        },

        async saveJournal() {
            if (!this.journalTopic.trim()) {
                this.journalError = 'Topik tidak boleh kosong.';
                return;
            }
            this.journalLoading = true;
            this.journalMessage = '';
            this.journalError   = '';

            try {
                const res = await fetch(this.journalSaveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ topic: this.journalTopic }),
                });

                const data = await res.json();

                if (res.ok) {
                    this.journalMessage = '✓ Jurnal berhasil disimpan.';
                    setTimeout(() => {
                        this.showModal = false;
                        window.location.reload();
                    }, 1200);
                } else {
                    this.journalError = data.message || 'Terjadi kesalahan.';
                }
            } catch (e) {
                this.journalError = 'Gagal terhubung ke server: ' + e.message;
            } finally {
                this.journalLoading = false;
            }
        },

        goToDate() {
            if (!this.pickedDate) return;
            window.location.href = '?date=' + this.pickedDate;
        }
    }
}
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection