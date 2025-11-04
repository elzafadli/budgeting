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
        Schema::table('budgets', function (Blueprint $table) {
            $table->foreignId('account_from_id')->nullable()->after('project_id')->constrained('account_banks')->onDelete('set null');
            $table->string('account_to')->nullable()->after('account_from_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            // Check if the old column exists and drop it
            if (Schema::hasColumn('budgets', 'account_bank_id')) {
                $table->dropForeign(['account_bank_id']);
                $table->dropColumn('account_bank_id');
            }
            
            // Check if the new columns exist and drop them
            if (Schema::hasColumn('budgets', 'account_from_id')) {
                $table->dropForeign(['account_from_id']);
                $table->dropColumn(['account_from_id', 'account_to']);
            }
        });
    }
};
