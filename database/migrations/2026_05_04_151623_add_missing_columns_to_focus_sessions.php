<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('focus_sessions', function (Blueprint $table) {
            // إضافة عمود الدقائق إذا لم يكن موجوداً
            if (!Schema::hasColumn('focus_sessions', 'duration_minutes')) {
                $table->integer('duration_minutes')->default(25)->after('user_id');
            }
            
            // إضافة عمود وقت الانتهاء إذا لم يكن موجوداً
            if (!Schema::hasColumn('focus_sessions', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('duration_minutes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('focus_sessions', function (Blueprint $table) {
            $table->dropColumn(['duration_minutes', 'completed_at']);
        });
    }
};