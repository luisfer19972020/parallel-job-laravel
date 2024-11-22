<?php

namespace App\Services\Tts;

use App\Data\SynthesizeResponseData;
use App\Enums\TtsState;
use App\Models\Tts;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TtsService
{
    protected string $storageDisk;

    private IFileService $fileService;

    public function __construct()
    {
        $this->storageDisk = 'local';
    }

    /**
     * @var array<string, class-string<TtsProviderInterface>>
     */
    protected array $providers = [
        'ES-PP-FEMALE-01'    => PiperTtsProvider::class,
        'ES-PP-FEMALE-01-RT' => PiperTtsRtProvider::class,
    ];

    public function synthesize(string $text, string $voice): SynthesizeResponseData
    {
        $provider = $this->getTtsProvider($voice);

        /** @var array{status: string, message?: string, data?: array{audio?: string}} $result */
        $result = $provider->synthesize($text, $voice);

        return new SynthesizeResponseData(
            status: $result['status'],
            message: ($result['status'] == 'error') ? ($result['message'] ?? 'Unknown error') : null,
            audio: ($result['status'] == 'ok') ? ($result['data']['audio'] ?? null) : null,
            contentType: $provider->getContentType(),
            extension: $provider->getFileExtension()
        );
    }

    private function getTtsProvider(string $voice): TtsProviderInterface
    {
        $providerClass = $this->providers[$voice] ?? PiperTtsProvider::class;

        return app($providerClass);
    }

    public function getAudio(string $text,string $hash, string $voice): false|string
    {
        if (empty($text) || empty($voice))
        {
            logs()->info('TTS cannot be created without the text or voice.');

            return false;
        }


            $lockKey = "tts_creation_lock_{$hash}";
            $lock    = Cache::lock($lockKey, 30);

            if ($lock->block(10))
            {
                try
                {
                    // Double-check if the TTS record was created while waiting for the lock
                    $tts = Tts::where('hash', $hash)->first();

                    if (! $tts || $tts->state !== TtsState::OK)
                    {
                        if (! $this->createAudio($text,$hash,$voice))
                        {
                            logs()->error("Failed to create audio. {$hash}");

                            return false;
                        }
                    }
                } finally
                {
                    $lock->release();
                }
            } else
            {
                // Could not acquire the lock within 10 seconds
                logs()->error("Timeout waiting for lock to create audio. {$hash}");

                return false;
            }

            // Retrieve the TTS record again after creation
            $tts = Tts::where('hash', $hash)->first();
            if (! $tts || $tts->state !== TtsState::OK)
            {
                logs()->error('TTS record not found or not in OK state after creation', ['hash' => $hash]);

                return false;
            }
     

        if (! Storage::disk($this->storageDisk)->exists($tts->full_filename))
        {
            logs()->warning("File missing in storage: {$tts->full_filename}");

            if (! $this->createAudio($text, $hash,$voice))
            {
                return false;
            }

            $tts->refresh();
        }

        $tts->increment('access_count');

        return $this->retrieveAudioFile($tts);
    }

    /**
     * Retrieves the audio file content and handles exceptions.
     *
     * @param  Tts  $tts  The TTS record.
     * @return false|string The audio file content, or false on failure.
     */
    private function retrieveAudioFile(Tts $tts): false|string
    {
        try
        {
            return 'SUJEFGIUWNESFG';
        } catch (\Exception $e)
        {
            logs()->error("Failed to retrieve audio file: {$e->getMessage()}", ['filename' => $tts->full_filename]);

            return false;
        }
    }

    public function createAudio(string $text,string $hash ,string $voice): bool
    {
        $provider  = $this->getTtsProvider($voice);
        $extension = $provider->getFileExtension();

        $tts        = $this->initializeTtsRecord($hash, $text, $voice);
        $time_start = microtime(true);

        $synthesisResult = $this->synthesize($text, $voice);
        if ($synthesisResult->status == 'error')
        {
            return $this->handleErrorState($tts, $synthesisResult->message ?? 'Unknown error', 1);
        }

        $time_end = microtime(true);

        $execution_time = ($time_end - $time_start) * 1000;

        $filepath = $this->generateFilePath();
        $filename = "{$hash}.{$extension}";
        $tts->update([
            'filepath'        => $filepath,
            'filename'        => $filename,
            'process_time_ms' => $execution_time,
            'render_count'    => DB::raw('render_count + 1'),
            'file_size_kb'    => strlen($synthesisResult->audio) / 1024, // is this different than file_size from storage facade?
        ]);

        if (! $this->uploadAudio($tts, $synthesisResult))
        {
            return false;
        }

        return $this->finalizeTtsRecord($tts);
    }

    public function generateHash(string $text, string $voice): string
    {
        return hash('sha256', $text.$voice);
    }

    private function generateFilePath(): string
    {
        $level1 = $this->generateSecureRandomPrefix(2);
        $level2 = $this->generateSecureRandomPrefix(2);

        return "{$level1}/{$level2}/";
    }

    private function initializeTtsRecord(string $hash, string $text, string $voice): Tts
    {
        $tts = Tts::updateOrCreate(
            ['hash' => $hash],
            ['text' => $text, 'voice' => $voice, 'state' => TtsState::INIT, 'state_datetime' => now()]
        );

        $tts->update(['state' => TtsState::PROGRESS, 'state_datetime' => now()]);

        return $tts;
    }

    private function handleErrorState(Tts $tts, string $errorText, int $errorNr): bool
    {
        $tts->update(['state' => TtsState::ERROR, 'state_datetime' => now(), 'error_txt' => $errorText, 'error_nr' => $errorNr]);

        return false;
    }

    private function generateSecureRandomPrefix(int $length = 2): string
    {
        $characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        $randomString     = '';

        for ($i = 0; $i < $length; $i++)
        {
            $index = random_int(0, $charactersLength - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    private function uploadAudio(Tts $tts, SynthesizeResponseData $response): bool
    {
        //Suponemos que se sube correctamente el audio
        return true;
    }

    private function finalizeTtsRecord(Tts $tts): bool
    {
        $this->fileService = new FileService;
        try
        {
            //Simulo la duracion
            $duration_ms = 10000;
            $fileSize = 10000;
            $tts->update(['state' => TtsState::OK, 'state_datetime' => now(), 'duration_ms' => $duration_ms, 'file_size_kb' => $fileSize]);
            logs()->info("Audio file created and uploaded: {$tts->full_filename}");

            return true;
        } catch (\Exception $e)
        {
            logs()->error("File size retrieval exception: {$e->getMessage()}");

            return $this->handleErrorState($tts, 'File size retrieval failed', 4);
        }
    }
}