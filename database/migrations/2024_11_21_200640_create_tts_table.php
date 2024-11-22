<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tts', function (Blueprint $table) {
            $table->id('tts_id');
            $table->string('hash', 64)->nullable(false);
            $table->string('voice', 64)->nullable(false);
            $table->text('text')->nullable(false);
            $table->string('filepath', 256)->nullable();
            $table->string('filename', 128)->nullable();
            $table->unsignedInteger('file_size_kb')->nullable();
            $table->unsignedMediumInteger('duration_ms')->nullable();
            $table->unsignedMediumInteger('process_time_ms')->nullable();
            $table->unsignedInteger('render_count')->nullable()->default(0);
            $table->unsignedInteger('access_count')->nullable()->default(0);
            $table->unsignedInteger('error_nr')->nullable();
            $table->text('error_txt')->nullable();
            $table->string('state')->nullable(false)->default('INIT');
            $table->timestamp('state_datetime')->nullable(false)->useCurrent();
            $table->timestamps();

            $table->index('hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tts');
    }
};