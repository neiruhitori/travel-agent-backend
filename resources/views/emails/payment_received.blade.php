{{-- filepath: resources/views/emails/payment_received.blade.php --}}
<h2>Pembayaran Anda Telah Diterima</h2>
<p>Halo {{ $pengajuan->applicant }},</p>
<p>
  Pembayaran untuk pengajuan perjalanan <b>#PGJ{{ str_pad($pengajuan->pengajuan_id, 5, '0', STR_PAD_LEFT) }}</b> telah kami terima.<br>
  Berikut rincian pembayaran:
</p>
<ul>
  <li><b>Instansi:</b> {{ $pengajuan->institution }}</li>
  <li><b>Tujuan:</b> {{ $pengajuan->destination->name ?? '-' }}</li>
  <li><b>Tanggal Berangkat:</b> {{ $pengajuan->departure_date }}</li>
  <li><b>Tanggal Kembali:</b> {{ $pengajuan->return_date }}</li>
  <li><b>Jumlah Dibayar:</b> Rp{{ number_format($paymentsub->amount_paid ?? 0, 0, ',', '.') }}</li>
  <li><b>Metode:</b> {{ $paymentsub->method ?? '-' }}</li>
  <li><b>Tanggal Bayar:</b> {{ $paymentsub->paid_at ?? '-' }}</li>
</ul>
@if($paymentsub->path_file)
  <p>Bukti pembayaran terlampir.</p>
  <img src="{{ asset('storage/' . $paymentsub->path_file) }}" alt="Bukti Pembayaran" style="max-width:300px;">
@endif
<p>Terima kasih telah melakukan pembayaran. Perjalanan Anda akan segera diproses.</p>
<p>Hormat kami,<br>Admin Tripnesia</p>