<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SensorAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $alertType;
    public $sensorData;
    public $message;

    /**
     * Create a new message instance.
     */
    public function __construct($alertType, $sensorData, $message)
    {
        $this->alertType = $alertType;
        $this->sensorData = $sensorData;
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjects = [
            'temperature' => '🌡️ Peringatan Suhu Abnormal - BMKG Monitoring',
            'humidity' => '💧 Peringatan Kelembaban Abnormal - BMKG Monitoring',
            'power' => '⚡ Peringatan Power OFF - BMKG Monitoring'
        ];

        return new Envelope(
            subject: $subjects[$this->alertType] ?? 'Peringatan Sensor - BMKG Monitoring',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.sensor-alert',
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
