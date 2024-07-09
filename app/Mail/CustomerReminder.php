<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $customerPrompt;
    public $emailDate;
    public $customerName;

    /**
     * Create a new message instance.
     */
    public function __construct($customerPrompt, $customerName)
    {
        $this->customerPrompt = $customerPrompt;
        $this->customerName = $customerName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Customer Reminder',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'customer_reminder',
            with: [
                'yesUrl' => route('customer.response', ['response' => 'YES', 'id' => $this->customerPrompt->id]),
                'noUrl' => route('customer.response', ['response' => 'NO', 'id' => $this->customerPrompt->id]),
                'pickupDate' => $this->customerPrompt->pickup_date->format('F j, Y'),
                'customerName' => $this->customerName,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
