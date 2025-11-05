<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class MonthlyReport extends Mailable
{
    use Queueable, SerializesModels;

    public $month;
    public $year;
    public $reportPath;
    public $summary;

    /**
     * Create a new message instance.
     */
    public function __construct($month, $year, $reportPath, $summary)
    {
        $this->month = $month;
        $this->year = $year;
        $this->reportPath = $reportPath;
        $this->summary = $summary;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $monthName = \Carbon\Carbon::create($this->year, $this->month, 1)->locale('id')->translatedFormat('F');
        
        return new Envelope(
            subject: "📊 Laporan Bulanan {$monthName} {$this->year} - BMKG Monitoring System",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.monthly-report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->reportPath)
                ->as("Laporan_BMKG_{$this->year}_{$this->month}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
