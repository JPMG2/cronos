<?php

declare(strict_types=1);

use App\Models\BloodType;
use App\Models\Country;
use App\Models\CurrentStatus;
use App\Models\Degree;
use App\Models\DocumentType;
use App\Models\Gender;
use App\Models\MaritalStatus;
use App\Models\Nationality;
use App\Models\Occupation;
use App\Models\Province;
use App\Models\Region;
use App\Models\TaxCondition;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();

            // Vínculo opcional al sistema de autenticación (unique: 1 usuario = 1 persona)
            $table->foreignIdFor(User::class)->nullable()->unique()->constrained()->nullOnDelete();

            // Documento de identidad
            $table->foreignIdFor(DocumentType::class)->nullable()->constrained()->nullOnDelete();
            $table->string('document_number')->nullable()->index();

            // Nombres y apellidos
            $table->string('first_name');
            $table->string('second_name')->nullable();
            $table->string('last_name');
            $table->string('second_last_name')->nullable();

            // Datos demográficos
            $table->date('birth_date')->nullable();
            $table->foreignIdFor(Gender::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(BloodType::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Nationality::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(MaritalStatus::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Occupation::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Degree::class)->nullable()->constrained()->nullOnDelete();

            // Contacto
            $table->string('email')->nullable()->index();
            $table->string('phone_mobile', 30)->nullable();
            $table->string('phone_home', 30)->nullable();
            $table->string('phone_work', 30)->nullable();

            // Domicilio
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->foreignIdFor(Country::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Province::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Region::class)->nullable()->constrained()->nullOnDelete();
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Datos fiscales
            $table->foreignIdFor(TaxCondition::class)->nullable()->constrained()->nullOnDelete();

            // Datos médicos de emergencia
            $table->text('known_allergies')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->string('emergency_contact_relationship', 60)->nullable();

            // Estado y metadatos
            $table->foreignIdFor(CurrentStatus::class)->nullable()->constrained()->nullOnDelete();
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();

            // Auditoría de cambios
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
