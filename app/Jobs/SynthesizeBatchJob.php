<?php

namespace App\Jobs;

use App\Services\Tts\TtsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Concurrency;

class SynthesizeBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  array<string>  $texts
     */
    public function __construct(private array $texts, private string $voice)
    {
        $this->texts = $texts;
        $this->voice = $voice;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $texts = $this->texts;

        $chunk_size  = 5;
        $texts       = new Collection($texts);
        $text_chunks = $texts->chunk($chunk_size);

        $voice = $this->voice;
        $text_chunks->each(function ($items, $key) use ($voice) {
            $batch_run = $this->createBatchRun($items, $voice);

            $results = Concurrency::run($batch_run);
            foreach ($results as $result_index => $result)
            {
                logs()->info("Audio created successfully {$result_index}, text {$result['status']}.");
            }
        });
    }

    /**
     * @param  Collection<int, string>  $items  A collection of text items for synthesis.
     * @return array<int, callable> An array of callables that execute the TTS process.
     */
    private function createBatchRun(Collection $items, string $voice): array
    {
        $batch_run = $items->map(function ($text) use ($voice) {
            return function () use ($text, $voice) {
                $result = (new TtsService)->getAudio($text, $voice);

                if (! $result)
                {
                    return ['status' => 'error'];
                }

                return ['status' => 'ok'];
            };
        });

        return $batch_run->toArray();
    }
}