<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // project name
            $table->string('vendor'); // project code
            $table->string('no_project')->unique(); // project number
            $table->date('start_date'); // start date
            $table->date('end_date')->nullable(); // end date (nullable)
            $table->decimal('amount', 15, 2)->default(0); // project value
            $table->text('description')->nullable(); // optional description
            $table->enum('status', ['in_progress', 'canceled', 'invoice', 'completed'])->default('in_progress'); // project status
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('projects');
        Schema::enableForeignKeyConstraints();
    }
};
