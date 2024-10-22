<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParalellJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $text;
    /**
     * Create a new job instance.
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //1000 peticiones de 7segs en 20 works se procesan en 5min 52secs sin afectaciones considerables de ram y cpu
        Log::info("Procesando texto: " . $this->text);
        sleep(7);
    }
}
