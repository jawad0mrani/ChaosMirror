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
        Schema::create('focus_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ربط الجلسة بالمستخدم (ريم أو جواد)
            $table->dateTime('start_time'); // وقت بدء الدراسة[cite: 1]
            $table->dateTime('end_time')->nullable(); // وقت الاستراحة[cite: 1]
            $table->integer('focus_level')->default(5); // تقييم التركيز من 1 لـ 10[cite: 1]
            $table->text('mood_notes')->nullable(); // ملاحظات ريم عن حالتها النفسية أثناء الدراسة[cite: 1]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('focus_sessions');
    }
};
