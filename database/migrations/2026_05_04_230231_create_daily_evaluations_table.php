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
        Schema::create('daily_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_interval_id')->constrained()->onDelete('cascade'); // ربط بالخطة الحالية
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('day_number'); // اليوم 1، 2، حتى 10
            
            // التقييمات الأساسية (إجبارية)
            $table->integer('focus_score'); // التركيز العام
            $table->integer('exercises_score'); // جودة حل التمارين
            $table->integer('mental_fatigue'); // مقترح: مستوى الإرهاق الذهني (عكسي)
            
            // التقييمات المتغيرة (null تعني لم يتم استخدام المصدر)
            $table->integer('youtube_score')->nullable();
            $table->integer('notebook_score')->nullable();
            $table->integer('book_score')->nullable();
            
            // المحصلة الخوارزمية
            $table->decimal('final_score', 4, 2);
            
            // المرفقات والملاحظات
            $table->text('notes')->nullable();
            $table->string('image_path')->nullable(); // مسار الصورة المضغوطة جداً
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_evaluations');
    }
};
