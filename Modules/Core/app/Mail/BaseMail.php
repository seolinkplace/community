<?php
namespace Modules\Core\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

abstract class BaseMail extends Mailable
{
    // locale inherited from Mailable

    public function __construct(string $locale = 'uk')
    {
        $this->locale = $locale;
    }

    protected function t(string $key): string
    {
        return trans($key, [], $this->locale);
    }
}
