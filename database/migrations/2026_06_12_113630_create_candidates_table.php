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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('nik')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('education')->nullable();
            $table->text('experience')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('ktp_path')->nullable();
            $table->string('ijazah_path')->nullable();
            $table->string('status')->default('Menunggu Interview HRD');
            $table->date('hrd_interview_date')->nullable();
            $table->text('hrd_interview_notes')->nullable();
            $table->boolean('hrd_recommendation')->nullable();
            $table->boolean('pic_decision')->nullable();
            $table->text('pic_reject_reason')->nullable();
            $table->dateTime('client_interview_schedule')->nullable();
            $table->text('client_interview_notes')->nullable();
            $table->boolean('final_decision')->nullable();
            $table->text('client_reject_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
