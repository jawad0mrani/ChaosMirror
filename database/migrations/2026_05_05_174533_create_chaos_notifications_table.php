<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل أوامر إنشاء جدول الإشعارات في قاعدة البيانات
     */
    public function up()
    {
        Schema::create('chaos_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان الإشعار
            $table->text('message'); // محتوى الرعب
            // نوع الضربة: فورية، لمرة واحدة، يومية، أسبوعية
            $table->enum('type', ['immediate', 'once', 'daily', 'weekly']); 
            $table->dateTime('scheduled_at')->nullable(); // وقت بدء الهجوم
            $table->integer('fired_count')->default(0); // عداد الضربات الناجحة
            // حالة الإشعار: قيد الانتظار، فعال (للمتكرر)، منتهي
            $table->enum('status', ['pending', 'active', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chaos_notifications');
    }
};