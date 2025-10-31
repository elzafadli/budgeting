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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number')->unique();
            $table->string('account_description');
            $table->boolean('active_indicator')->default(true);
            $table->string('account_number_parent')->nullable();
            $table->integer('account_level')->default(1);
            $table->enum('account_type', ['asset', 'liability', 'equity', 'revenue', 'expense'])->default('expense');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
