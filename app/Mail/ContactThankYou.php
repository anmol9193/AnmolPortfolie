<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The "thank you" email sent to someone who filled in the contact form.
 */
class ContactThankYou extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            // sent from the configured mailbox, but under the site owner's name
            from: new Address(config('mail.from.address'), content('site.name')),
            replyTo: array_filter([content('contact.email') ? new Address(content('contact.email'), content('site.name')) : null]),
            subject: 'Thank you for your message, '.$this->contactMessage->name,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.contact-thank-you');
    }
}
