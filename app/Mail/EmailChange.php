<?php
declare(strict_types=1);

namespace App\Mail;

use App\Models\Users\UserEmailConfirmation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailChange extends Mailable
{
    use Queueable, SerializesModels;

    public UserEmailConfirmation $emailConfirmation;

    /**
     * Create a new message instance.
     */
    public function __construct(UserEmailConfirmation $emailConfirmation)
    {
        $this->emailConfirmation = $emailConfirmation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $this->emailConfirmation->loadMissing('user');

        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS', 'hello@example.com'), env('MAIL_FROM_NAME', 'Example')),
            to: [new Address($this->emailConfirmation->new_email, $this->emailConfirmation->user->fullName)],
            subject: 'Подтверждение адреса электронной почты'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user.email_change'
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
