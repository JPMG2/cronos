<?php

declare(strict_types=1);

namespace App\Models;

use Attribute;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

final class Person extends Model
{
    /** @use HasFactory<PersonFactory> */
    use HasFactory;

    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'document_type_id',
        'document_number',
        'first_name',
        'second_name',
        'last_name',
        'second_last_name',
        'birth_date',
        'gender_id',
        'blood_type_id',
        'nationality_id',
        'marital_status_id',
        'occupation_id',
        'degree_id',
        'email',
        'phone_mobile',
        'phone_home',
        'phone_work',
        'address_line1',
        'address_line2',
        'country_id',
        'province_id',
        'region_id',
        'postal_code',
        'latitude',
        'longitude',
        'tax_condition_id',
        'known_allergies',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'current_status_id',
        'photo_path',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'document_type_id', 'document_number',
                'first_name', 'second_name', 'last_name', 'second_last_name',
                'birth_date', 'gender_id', 'blood_type_id', 'nationality_id',
                'email', 'phone_mobile', 'phone_home', 'phone_work',
                'address_line1', 'country_id', 'province_id', 'region_id',
                'current_status_id',
            ])
            ->logOnlyDirty()
            ->useLogName('person')
            ->dontLogEmptyChanges();
    }

    // ── Relaciones ───────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function bloodType(): BelongsTo
    {
        return $this->belongsTo(BloodType::class);
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function maritalStatus(): BelongsTo
    {
        return $this->belongsTo(MaritalStatus::class);
    }

    public function occupation(): BelongsTo
    {
        return $this->belongsTo(Occupation::class);
    }

    public function degree(): BelongsTo
    {
        return $this->belongsTo(Degree::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function taxCondition(): BelongsTo
    {
        return $this->belongsTo(TaxCondition::class);
    }

    public function currentStatus(): BelongsTo
    {
        return $this->belongsTo(CurrentStatus::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => mb_trim("{$this->first_name} {$this->last_name}"),
        );
    }

    protected function fullNameWithSecond(): Attribute
    {
        return Attribute::make(
            get: fn () => mb_trim(implode(' ', array_filter([
                $this->first_name,
                $this->second_name,
                $this->last_name,
                $this->second_last_name,
            ]))),
        );
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->birth_date?->age,
        );
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value !== null ? mb_strtolower(mb_trim($value)) : null,
        );
    }

    // ── Casts ────────────────────────────────────────────────────────────────

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'user_id' => 'integer',
            'document_type_id' => 'integer',
            'gender_id' => 'integer',
            'blood_type_id' => 'integer',
            'nationality_id' => 'integer',
            'marital_status_id' => 'integer',
            'occupation_id' => 'integer',
            'degree_id' => 'integer',
            'country_id' => 'integer',
            'province_id' => 'integer',
            'region_id' => 'integer',
            'tax_condition_id' => 'integer',
            'current_status_id' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }
}
