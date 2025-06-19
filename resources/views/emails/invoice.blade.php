{{-- filepath: c:\laragon\www\travel-agent-backend\resources\views\emails\invoice.blade.php --}}
<h2>[Rincian Biaya] Pengajuan Perjalanan Anda – Mohon Persetujuan</h2>

<p>Halo {{ $invoice->pengajuan->applicant ?? '-' }},</p>

<p>
  Terima kasih telah melakukan pengajuan perjalanan dengan nomor: <b>#INV{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</b>.
</p>

<p>
  Berikut kami lampirkan rincian estimasi biaya yang diperlukan untuk perjalanan Anda:
</p>

<ul>
    <li><b>ID Pengajuan:</b> {{ $invoice->pengajuan->id ?? '-' }}</li>
    <li><b>Instansi:</b> {{ $invoice->pengajuan->institution ?? '-' }}</li>
    <li><b>Tujuan:</b> {{ $invoice->pengajuan->destination->name ?? '-' }}</li>
    <li><b>Tanggal Berangkat:</b> {{ $invoice->pengajuan->departure_date ?? '-' }}</li>
    <li><b>Tanggal Kembali:</b> {{ $invoice->pengajuan->return_date ?? '-' }}</li>
    <li><b>Total Estimasi Biaya:</b> Rp{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</li>
</ul>

<p>
  Mohon konfirmasi apakah Anda menyetujui rincian biaya ini.<br>
  Silakan klik tombol berikut untuk memberikan respon:
</p>

<p>
  <a href="{{ $confirmationUrl }}" style="display:inline-block;padding:10px 20px;background:#2563eb;color:#fff;border-radius:6px;text-decoration:none;">
    Konfirmasi Persetujuan
  </a>
</p>

<p>
  Jika Anda menyetujui, perjalanan akan segera dijadwalkan dan dilanjutkan ke tahap berikutnya.
</p>

<br>
<p>Hormat kami,<br>
Admin Tripnesia</p>