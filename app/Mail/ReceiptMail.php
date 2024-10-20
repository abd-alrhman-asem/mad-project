<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $receiptData;

    public function __construct($receiptData)
    {
        $this->receiptData = $receiptData;
    }

    public function build()
    {
        return $this->view('emails.invoice')  // ensure this view exists
            ->with(['data' => $this->receiptData])  // passing as 'data'
            ->subject('Your Receipt');
    }
}
