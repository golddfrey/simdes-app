{{-- resources/views/penduduk/anggota_pdf.blade.php --}}
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Biodata Anggota {{ $a->nama }}</title>
  <style> body{font-family: DejaVu Sans, sans-serif; font-size:12px;} .label{font-weight:bold;width:120px;display:inline-block;} </style>
</head>
<body>
  <h3>Biodata Anggota</h3>
  <div><span class="label">Nama</span>: {{ $a->nama }}</div>
  <div><span class="label">NIK</span>: {{ $a->nik ?? '-' }}</div>
  <div><span class="label">Status</span>: {{ $a->status_keluarga ?? '-' }}</div>
  <div><span class="label">Jenis Kelamin</span>: {{ $a->jenis_kelamin === 'L' ? 'Laki-laki' : ($a->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</div>
  <div><span class="label">Tempat Lahir</span>: {{ $a->tempat_lahir ?? '-' }}</div>
  <div><span class="label">Tanggal Lahir</span>: {{ $a->tanggal_lahir ?? '-' }}</div>
  <div style="margin-top:8px"><span class="label">KK</span>: {{ optional($a->kepalaKeluarga)->nama ?? '-' }}</div>
</body>
</html>
