<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use Carbon\Carbon;

class GenerateMonthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:monthly {--month=} {--year=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send monthly monitoring report via email';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get month and year from options or use previous month
        $month = $this->option('month') ?? Carbon::now()->subMonth()->month;
        $year = $this->option('year') ?? Carbon::now()->subMonth()->year;

        $this->info("Generating monthly report for {$year}-{$month}...");

        try {
            $reportPath = $this->notificationService->generateMonthlyReport($month, $year);

            if ($reportPath) {
                $this->info("✅ Monthly report generated successfully!");
                $this->info("📄 PDF saved to: {$reportPath}");
                $this->info("📧 Email sent to administrators");
            } else {
                $this->warn("⚠️  No data available for the specified month");
            }

        } catch (\Exception $e) {
            $this->error("❌ Failed to generate monthly report: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
