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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('title'); // عنوان المادة (مثلاً: بحث الرياضيات)
            $table->enum('type', ['Link', 'PDF', 'Note', 'Code'])->default('Link'); // نوع المصدر
            $table->text('content'); // الرابط أو نص الملاحظة[cite: 1]
            $table->string('subject')->nullable(); // اسم المادة الدراسية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
