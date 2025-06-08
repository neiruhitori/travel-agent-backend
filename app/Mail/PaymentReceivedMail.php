<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pengajuan;
    public $paymentsub;

    public function __construct($pengajuan, $paymentsub)
    {
        $this->pengajuan = $pengajuan;
        $this->paymentsub = $paymentsub;
    }

    public function build()
    {
        return $this->subject('[Tripnesia] Pembayaran Diterima')
            ->view('emails.payment_received')
            ->with([
                'pengajuan' => $this->pengajuan,
                'paymentsub' => $this->paymentsub,
            ]);
    }
}