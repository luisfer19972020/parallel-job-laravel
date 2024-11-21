<?php

namespace App\Models;

use App\Enums\TtsState;
use Database\Factories\TtsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tts extends Model
{
    /** @use HasFactory<TtsFactory> */
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'tts';

    protected $primaryKey = 'tts_id';

    public $timestamps = true;

    protected $casts = [
        'state' => TtsState::class,
    ];

    protected $hidden = [
    ];

    protected $fillable = [
        'hash',
        'voice',
        'text',
        'filepath',
        'filename',
        'duration_ms',
        'file_size_kb',
        'process_time_ms',
        'render_count',
        'access_count',
        'error_nr',
        'error_txt',
        'state',
        'state_datetime',
    ];

    public function getFullFilenameAttribute(): string
    {
        return $this->filepath.$this->filename;
    }
}