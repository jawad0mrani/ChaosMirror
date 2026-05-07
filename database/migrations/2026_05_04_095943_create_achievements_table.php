<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('points')->default(0); // عدد النقاط المكتسبة[cite: 1]
            $table->string('badge_name')->nullable(); // اسم الوسام (مثلاً: محارب الفوضى)[cite: 1]
            $table->boolean('reward_unlocked')->default(false); // هل تم فتح مكافأة؟[cite: 1]
            $table->text('reward_description')->nullable(); // وصف المكافأة (مثلاً: رسالة تشجيعية من جواد)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
