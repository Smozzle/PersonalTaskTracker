<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Change status column to include 'Ongoing'
            $table->enum('status', ['Pending', 'Ongoing', 'Completed'])
                ->default('Pending')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'Completed'])
                ->default('Pending')
                ->change();
        });
    }
};