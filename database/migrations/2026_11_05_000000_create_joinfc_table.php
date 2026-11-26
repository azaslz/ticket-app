<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('joinfc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('fanclub_id');
            $table->date('joinfc_join_day');
            $table->date('joinfc_limit')->nullable();
            $table->string('joinfc_status', 20)->default('加入中');
            $table->timestamps();
            $table->unique(['account_id', 'fanclub_id']);
            $table->index(['account_id', 'joinfc_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('joinfc');
    }
};
