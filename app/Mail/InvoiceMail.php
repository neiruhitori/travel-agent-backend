<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $pengajuan = $this->invoice->pengajuan;
        $confirmationUrl = "http://localhost:3000/pesanan-saya/pengajuan/" . $pengajuan->id;

        return $this->view('emails.invoice')
            ->with([
                'invoice' => $this->invoice,
                'confirmationUrl' => $confirmationUrl,
            ]);
    }
}
