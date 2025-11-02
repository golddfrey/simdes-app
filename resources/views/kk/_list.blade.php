{{-- resources/views/kk/_list.blade.php --}}
@foreach($kks as $kk)
  <div class="bg-white p-4 rounded shadow md:flex md:items-start md:justify-between mb-4">
    <div class="md:flex-1">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
        <div>
          <div class="text-sm text-gray-500">NIK</div>
          <div class="font-medium text-lg">{{ $kk->nik }}</div>
          <a href="{{ route('kk.show', $kk->id) }}#anggota" class="text-sm text-blue-600 hover:underline">Tampilkan Anggota ({{ $kk->anggotas->count() }})</a>
        </div>

        <div class="md:col-span-1">
          <div class="text-sm text-gray-500">Nama</div>
          <div class="font-medium">{{ $kk->nama }}</div>
        </div>

        <div class="text-right md:text-left md:col-span-1">
          <div class="text-sm text-gray-500">RT / RW</div>
          <div>{{ $kk->rt ?? '-' }} / {{ $kk->rw ?? '-' }}</div>
          <div class="text-xs text-gray-500 mt-2">Terdafar</div>
          <div class="text-xs text-gray-600">{{ $kk->created_at ? $kk->created_at->format('Y-m-d') : '-' }}</div>
        </div>
      </div>

      {{-- Mobile: optional show first anggota snippet (keputusan desain), tetap ringkas --}}
      <div class="mt-3 md:hidden">
        @if($kk->anggotas->isNotEmpty())
          <div class="text-sm text-gray-600">
            Anggota: {{ $kk->anggotas->pluck('nama')->take(3)->join(', ') }}{{ $kk->anggotas->count() > 3 ? ' ...' : '' }}
          </div>
        @endif
      </div>
    </div>

    {{-- aksi --}}
    <div class="mt-3 md:mt-0 md:ml-6 flex gap-2 items-center">
      <a href="{{ route('kk.show', $kk->id) }}" class="text-sm px-3 py-2 border rounded hover:bg-gray-50">Detail</a>
      <a href="{{ route('kk.edit', $kk->id) }}" class="text-sm px-3 py-2 border rounded hover:bg-gray-50">Edit</a>
    </div>
  </div>
@endforeach

{{-- jika paginasi tersedia --}}
@if(method_exists($kks, 'links'))
  <div class="mt-6">
    {{ $kks->links() }}
  </div>
@endif
