<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCodeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;

    public $content;

    public $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title, $content, $code)
    {
        $this->title = $title;
        $this->content = $content;
        $this->code = $code;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->title)
            ->markdown('email.send-code-email');
    }
}
