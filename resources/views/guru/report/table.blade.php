<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left border-b border-gray-100">
                <th class="py-3 px-4 font-semibold text-gray-500 w-12">#</th>
                <th class="py-3 px-2 font-semibold text-gray-500">Siswa</th>
                <th class="py-3 px-4 text-right font-semibold text-gray-500">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($students as $i => $student)
                @php $hasRombel = $student->rombels->isNotEmpty(); @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-3 px-4 text-gray-400">
                        {{ $students->firstItem() + $i }}
                    </td>
                    <td class="py-3 px-2">
                        <p class="font-semibold text-gray-800">{{ $student->name }}</p>
                        <p class="text-xs text-gray-400">{{ $student->nisn }}</p>
                        @unless($hasRombel)
                            <span class="inline-block mt-1 text-[10px] font-semibold text-amber-600 bg-amber-50 border border-amber-200 rounded-full px-2 py-0.5">
                                Belum terdaftar di rombel
                            </span>
                        @endunless
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex gap-2 justify-end items-center">
                            @if($hasRombel)
                                <button type="button"
                                        @click="
                                            if (window.innerWidth >= 768) {
                                                previewUrl = `{{ route('walikelas.report.preview', $student->id) }}?from=${from}&to=${to}&scope={{ $scope }}`;
                                                showModal = true;
                                            } else {
                                                window.open(`{{ route('walikelas.report.preview', $student->id) }}?from=${from}&to=${to}&scope={{ $scope }}`, '_blank');
                                            }
                                        "
                                        title="Preview laporan"
                                        class="p-2 text-gray-400 hover:text-blue-600 transition">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                <a :href="`{{ route('walikelas.report.download', $student->id) }}?from=${from}&to=${to}&scope={{ $scope }}`"
                                   title="Download PDF"
                                   class="px-3 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl hover:bg-blue-700 transition">
                                    PDF
                                </a>
                            @else
                                {{-- Disabled preview --}}
                                <span title="Siswa belum terdaftar di rombel"
                                      class="p-2 text-gray-200 cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </span>
                                <span title="Siswa belum terdaftar di rombel"
                                      class="px-3 py-2 bg-gray-100 text-gray-300 text-xs font-semibold rounded-xl cursor-not-allowed">
                                    PDF
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-10 text-gray-400">
                        Data siswa tidak ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">
    @if(is_object($students) && method_exists($students, 'links'))
        {{ $students->appends(request()->query())->links() }}
    @endif
</div>