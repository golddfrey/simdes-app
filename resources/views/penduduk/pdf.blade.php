{{-- resources/views/penduduk/pdf.blade.php --}}
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Biodata KK {{ $kk->nik }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; color: #222; font-size: 12px; }
    h2 { margin-bottom: 0; }
    .muted { color: #666; font-size: 11px; }
    .box { border: 1px solid #333; padding: 10px; margin-top: 10px; }
    .member { border-top: 1px dashed #ccc; padding-top: 8px; margin-top: 8px; }
    .label { font-weight: bold; width: 140px; display:inline-block; }
  </style>
</head>
<body>
  <h2>Biodata Kepala Keluarga</h2>
  <div class="muted">NIK: {{ $kk->nik }} — Tanggal: {{ now()->format('Y-m-d') }}</div>

  <div class="box">
    <div><span class="label">Nama</span> : {{ $kk->nama }}</div>
    <div><span class="label">Jenis Kelamin</span> : {{ ($kk->jenis_kelamin === 'L') ? 'Laki-laki' : (($kk->jenis_kelamin === 'P') ? 'Perempuan' : '-') }}</div>
    <div><span class="label">Phone</span> : {{ $kk->phone ?? '-' }}</div>
    <div><span class="label">Alamat</span> : {{ $kk->alamat ?? '-' }}</div>
    <div><span class="label">RT / RW</span> : {{ $kk->rt ?? '-' }} / {{ $kk->rw ?? '-' }}</div>
  </div>

  <h4 style="margin-top:12px;">Anggota Keluarga ({{ $kk->anggotas->count() }})</h4>
  @foreach($kk->anggotas as $a)
    <div class="box member">
      <div><span class="label">Nama</span> : {{ $a->nama }}</div>
      <div><span class="label">NIK</span> : {{ $a->nik ?? '-' }}</div>
      <div><span class="label">Status</span> : {{ $a->status_keluarga ?? '-' }}</div>
      <div><span class="label">Jenis Kelamin</span> : {{ $a->jenis_kelamin === 'L' ? 'Laki-laki' : ($a->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</div>
      <div><span class="label">Tempat Lahir</span> : {{ $a->tempat_lahir ?? '-' }}</div>
      <div><span class="label">Tanggal Lahir</span> : {{ $a->tanggal_lahir ?? '-' }}</div>
    </div>
  @endforeach

</body>
</html>
