<?php

use App\Models\Upload;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table(Upload::TABLE, function (Blueprint $table) {
            $table->renameColumn('csv_path', 'path_original');
            $table->renameColumn('json_path', 'path_converted');
        });
    }

    public function down(): void
    {
        Schema::table(Upload::TABLE, function (Blueprint $table) {
            $table->renameColumn('path_original', 'csv_path');
            $table->renameColumn('path_converted', 'json_path');
        });
    }
};
