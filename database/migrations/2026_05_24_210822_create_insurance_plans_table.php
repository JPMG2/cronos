<?php

declare(strict_types=1);

use App\Models\CurrentStatus;
use App\Models\InsuranceCompany;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InsuranceCompany::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(CurrentStatus::class)->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('code', 30)->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['insurance_company_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_plans');
    }
};
