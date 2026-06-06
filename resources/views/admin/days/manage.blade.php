@extends('admin.layout')
@section('header', 'Kelola Jadwal: ' . $day->name)

@section('content')
<div class="p-6" x-data="{ 
    showModal: false, 
    editMode: false, 
    formUrl: '',
    formData: { time_slot_id: '', rombel_id: '', subject_id: '', teacher_id: '', classroom_id: '' }
}">

    <div class="mb-6">
        <button @click="
            showModal = true; editMode = false; 
            formUrl = '{{ route('admin.schedules.store') }}';
            formData = { time_slot_id: '', rombel_id: '', subject_id: '', teacher_id: '', classroom_id: '' }
        " class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-700 transition shadow-lg font-semibold">
            + Tambah Jadwal
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($timeSlots as $slot)
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 flex flex-col hover:shadow-xl transition duration-300">
                <div class="p-4 border-b bg-gray-50 rounded-t-xl flex justify-between items-center">
                    <div>
                        <h4 class="font-bold text-indigo-700 text-lg">Jam Ke-{{ $slot->jam_ke }}</h4>
                        <span class="text-xs text-gray-500 font-medium">
                            {{ date('H:i', strtotime($slot->start_time)) }} - {{ date('H:i', strtotime($slot->end_time)) }}
                        </span>
                    </div>
                </div>

                <div class="p-4 flex-grow">
                    @if($slot->schedules->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($slot->schedules as $sched)
                                <div class="p-3 bg-indigo-50/50 rounded-lg border border-indigo-100">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-bold text-gray-800">{{ $sched->subject->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $sched->rombel->name }} | {{ $sched->classroom->name }}</p>
                                            <p class="text-xs text-indigo-600 font-semibold mt-1">{{ $sched->teacher->name }}</p>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <button @click="
                                                showModal = true; editMode = true; 
                                                formUrl = '{{ route('admin.days.manage.update', $sched->id) }}';
                                                formData = { 
                                                    time_slot_id: '{{ $slot->id }}', 
                                                    rombel_id: '{{ $sched->rombel_id }}', 
                                                    subject_id: '{{ $sched->subject_id }}', 
                                                    teacher_id: '{{ $sched->teacher_id }}', 
                                                    classroom_id: '{{ $sched->classroom_id }}' 
                                                }
                                            " class="text-blue-500 hover:text-blue-700 text-xs font-bold">Edit</button>
                                            
                                            <form action="{{ route('admin.days.manage.delete', $sched->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                                @csrf @method('POST')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="h-full flex items-center justify-center text-gray-400 italic py-6">
                            <p>Belum ada jadwal</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50 p-4">
        <div class="bg-white p-6 rounded-2xl w-full max-w-lg shadow-2xl" @click.away="showModal = false">
            <h3 class="text-lg font-bold mb-4" x-text="editMode ? 'Edit Jadwal' : 'Tambah Jadwal'"></h3>
            <form :action="formUrl" method="POST">
                @csrf
                <template x-if="editMode"> <input type="hidden" name="_method" value="POST"> </template>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold">Jam Pelajaran</label>
                        <select name="time_slot_id" x-model="formData.time_slot_id" class="w-full border p-2 rounded-lg" required>
                            <option value="">-- Pilih Jam --</option>
                            @foreach($timeSlots as $slot)
                                <option value="{{ $slot->id }}">Jam Ke-{{ $slot->jam_ke }} ({{ date('H:i', strtotime($slot->start_time)) }} - {{ date('H:i', strtotime($slot->end_time)) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Rombel</label>
                        <select name="rombel_id" x-model="formData.rombel_id" class="w-full border p-2 rounded-lg" required>
                            @foreach($rombels as $r) <option value="{{ $r->id }}">{{ $r->name }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Mata Pelajaran</label>
                        <select name="subject_id" x-model="formData.subject_id" class="w-full border p-2 rounded-lg" required>
                            @foreach($subjects as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Guru</label>
                        <select name="teacher_id" x-model="formData.teacher_id" class="w-full border p-2 rounded-lg" required>
                            @foreach($teachers as $t) <option value="{{ $t->id }}">{{ $t->name }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold">Ruangan</label>
                        <select name="classroom_id" x-model="formData.classroom_id" class="w-full border p-2 rounded-lg" required>
                            @foreach($classrooms as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection