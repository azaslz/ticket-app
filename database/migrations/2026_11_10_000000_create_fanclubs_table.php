<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fanclubs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('category', 80)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('monthly_fee')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('fanclubs')->insert([
            ['name' => 'Moonlight', 'category' => 'MUSIC', 'description' => '音楽とライブでつながる、特別なファンクラブ。', 'monthly_fee' => 550, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sora Official', 'category' => 'CREATOR', 'description' => 'ここでしか読めない日記や限定配信をお届け。', 'monthly_fee' => 770, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Atelier 27', 'category' => 'ART & LIFE', 'description' => '制作の裏側や小さな発見を一緒に楽しもう。', 'monthly_fee' => 330, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('fanclubs');
    }
};
