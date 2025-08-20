<?php

use App\Enums\ConversionStatuses;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create(Upload::TABLE, function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(User::TABLE);
            $table->string('name', Upload::RULE_MAX_NAME);
            $table->boolean('is_private')->default(false);
            $table->string('csv_path')->nullable();
            $table->string('json_path')->nullable();
            $table->enum('status', ConversionStatuses::values())->default(ConversionStatuses::Queued);
            $table->text('error_message')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
