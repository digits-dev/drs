<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendSalesToEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $infos;
    public $filename;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($infos)
    {
        $this->infos = $infos;
        $this->filename = $infos['filename'];
        $this->attachment = $infos['folder'];
        $this->name = $infos['employee'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Sales')
        ->view('emails.send-sales-email')
        ->attach(storage_path("app/{$this->attachment}/$this->filename"));
    }
}
