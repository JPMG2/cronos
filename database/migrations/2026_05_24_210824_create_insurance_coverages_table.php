<?php

declare(strict_types=1);

use App\Models\InsurancePlan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_coverages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InsurancePlan::class)->constrained()->cascadeOnDelete();
            // Categoría general: Consulta, Internación, Cirugía, Estudios, etc.
            // Cuando se implemente el nomenclador, se agregará medical_procedure_id nullable
            $table->string('category');
            $table->decimal('coverage_percentage', 5, 2)->nullable();
            $table->decimal('copay_amount', 10, 2)->nullable();
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->boolean('requires_authorization')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_coverages');
    }
};
