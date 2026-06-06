<?php

declare(strict_types=1);

use App\Models\CoverageType;
use App\Models\CurrentStatus;
use App\Models\Region;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CurrentStatus::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Region::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(CoverageType::class)->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('code', 20)->nullable()->index();
            $table->string('cuit', 20)->nullable()->unique();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->string('logo')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_companies');
    }
};
