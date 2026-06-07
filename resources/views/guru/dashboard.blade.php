@extends('guru.layout')

@section('content')
<div class="max-w-3xl mx-auto p-4 md:p-6" x-data="dashboard()">
     <div>
            <h1 class="text-2xl font-bold text-slate-800">Jadwal Kelas Anda</h1>
            
        </div>
    <div class="bg-white p-5  shadow-sm border border-gray-100 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">
                {{ $currentDate->translatedFormat('F Y') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="?date={{ $weekStart->copy()->subWeek()->format('Y-m-d') }}"
                   class="p-2 text-gray-500 hover:bg-gray-100  transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <button @click="showDatePicker = true"
                        class="p-2 text-blue-600 hover:bg-blue-50  transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>
                <a href="?date={{ $weekStart->copy()->addWeek()->format('Y-m-d') }}"
                   class="p-2 text-gray-500 hover:bg-gray-100  transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Grid 7 hari --}}
        <div class="grid grid-cols-7 gap-2">
            @foreach($dates as $d)
                <a href="?date={{ $d->format('Y-m-d') }}"
                   class="flex flex-col items-center justify-center py-3 rounded-2xl border-2 transition-all
                   {{ $d->isSameDay($currentDate)
                       ? 'border-blue-500 text-blue-600'
                       : 'border-transparent hover:bg-gray-50 text-gray-700' }}">
                    <span class="text-[10px] font-bold opacity-60 mb-1">
                        {{ $d->translatedFormat('D') }}
                    </span>
                    <span class="text-base font-bold">{{ $d->format('d') }}</span>
                </a>
            @endforeach
        </div>
    </div>


    {{-- JADWAL --}}
    <div class="grid grid-cols-1 gap-4">
        @forelse($schedules as $schedule)
            @php
                $presence     = $schedule->merged_presences->first();
                $hasJournal   = $presence && $presence->journal;
                $presenceId   = $presence ? $presence->id : '';
                $journalTopic = $hasJournal ? addslashes($presence->journal->topic) : '';
                $journalUrl   = $presenceId ? route('guru.jurnal.store', $presenceId) : '';
            @endphp

            <div class="bg-white p-5  shadow-sm border border-gray-100">
                <div class="mb-4">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">
                            {{ \Carbon\Carbon::parse($schedule->merged_start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->merged_end_time)->format('H:i') }}
                        </span>
                        @if(count($schedule->schedule_ids) > 1)
                            <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">
                                {{ count($schedule->schedule_ids) }} sesi
                            </span>
                        @endif
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg mt-2">{{ $schedule->subject->name }}</h3>
                </div>

                <div class="space-y-1 text-sm text-gray-600 mb-5">
                    <div class="flex items-center gap-2"><span>📍</span> {{ $schedule->classroom->name }}</div>
                    <div class="flex items-center gap-2"><span>🏢</span> {{ $schedule->rombel->name }}</div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('guru.presensi.create', ['id' => $schedule->id, 'date' => $date]) }}"
                       class="flex-1 text-center py-2.5 bg-blue-600 text-white  text-sm font-semibold hover:bg-blue-700 transition">
                        {{ $schedule->merged_presences->isNotEmpty() ? 'Detail Presensi' : 'Presensi Siswa' }}
                    </a>

                    <button type="button"
                            @click="openJournalModal('{{ $presenceId }}', '{{ $journalTopic }}', '{{ $journalUrl }}')"
                            class="flex-1 text-center py-2.5  text-sm font-semibold transition-all
                            {{ $presence ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
                            {{ !$presence ? 'disabled' : '' }}>
                        {{ $hasJournal ? 'Edit Jurnal' : 'Add Jurnal' }}
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-20 text-gray-400">Tidak ada jadwal untuk hari ini</div>
        @endforelse
    </div>

    <div x-show="showDatePicker"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
         @click.self="showDatePicker = false">
        <div class="bg-white rounded-2xl p-6 w-80 shadow-xl">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Pilih Tanggal</h3>
            <input type="date"
                   x-model="pickedDate"
                   class="w-full border border-gray-200  px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <div class="flex gap-2 mt-4">
                <button @click="showDatePicker = false"
                        class="flex-1 py-2  border border-gray-200 text-sm text-gray-500 hover:bg-gray-50 transition">
                    Batal
                </button>
                <button @click="goToDate()"
                        class="flex-1 py-2  bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                    Pilih
                </button>
            </div>
        </div>
    </div>

    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
         @click.self="showModal = false">
        <div class="bg-white rounded-2xl p-6 w-96 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Jurnal Mengajar</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mb-4">
                <label class="text-xs text-gray-500 mb-1 block">Topik / Tema Pembelajaran</label>
                <textarea x-model="journalTopic"
                          rows="4"
                          placeholder="Contoh: Persamaan kuadrat dan penerapannya..."
                          class="w-full border border-gray-200  px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-400 resize-none"></textarea>
            </div>

            <p x-show="journalMessage"
               x-text="journalMessage"
               x-cloak
               class="text-xs text-emerald-600 mb-3 font-medium"></p>
            <p x-show="journalError"
               x-text="journalError"
               x-cloak
               class="text-xs text-red-500 mb-3"></p>

            <div class="flex gap-2">
                <button @click="showModal = false"
                        class="flex-1 py-2  border border-gray-200 text-sm text-gray-500 hover:bg-gray-50 transition">
                    Batal
                </button>
                <button @click="saveJournal()"
                        :disabled="journalLoading"
                        class="flex-1 py-2  bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition disabled:opacity-50">
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