<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('make');
            $table->string('model');
            $table->string('trim');
            $table->string('slug')->unique();
            $table->timestamps();

            $table->index(['year', 'make', 'model', 'trim']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
