
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
        Schema::create('blood_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique();
            $table->string('name', 20);
            $table->string('abo_group', 3);
            $table->json('rh_factor');
            $table->json('can_donate_to');
            $table->json('can_receive_from');
            $table->boolean('is_universal_donor')->default(false);
            $table->boolean('is_universal_recipient')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_types');
    }
};
