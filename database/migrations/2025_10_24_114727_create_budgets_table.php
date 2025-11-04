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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('document_date');
            $table->text('description')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('approved_total', 15, 2)->default(0);
            $table->enum('status', ['draft', 'submitted', 'pm_approved', 'finance_approved', 'rejected', 'completed'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('budgets');
        Schema::enableForeignKeyConstraints();
    }
};
