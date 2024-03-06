<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VisitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $message)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        sleep(random_int(2,15));
        $this->message;
    }

    public  function tags(): array
    {
        return ['VisitJob'];
    }
}
