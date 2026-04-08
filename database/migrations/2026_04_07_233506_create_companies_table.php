<?php

declare(strict_types=1);

use App\Models\CurrentStatus;
use App\Models\Region;
use App\Models\TaxCondition;
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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CurrentStatus::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(TaxCondition::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Region::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('fiscal_identifier')->unique();
            $table->string('phone')->unique();
            $table->string('email')->unique();
            $table->string('address');
            $table->string('postal_code');
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            // Auditoría y control de cambios.
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
