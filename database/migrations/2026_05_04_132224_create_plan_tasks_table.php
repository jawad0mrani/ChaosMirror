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
        Schema::create('plan_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_interval_id')->constrained()->onDelete('cascade');
            $table->string('subject'); // رياضيات، علوم، إلخ...[cite: 1]
            $table->string('task_name'); // مثلاً: "وحدة النهايات" أو "10 دروس عصبية"[cite: 1]
            $table->boolean('is_completed')->default(false); // حالة الإنجاز
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_tasks');
    }
};
