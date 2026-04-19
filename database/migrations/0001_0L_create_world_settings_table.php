<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\Province;
use App\Models\Region;
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
        Schema::create('world_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary()->default(1);
            $table->foreignIdFor(Country::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Province::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Region::class)->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('world_settings');
    }
};
