# Sistema de Coberturas Médicas — Guía de Arquitectura

> **Cronos Medical** · Guía de referencia permanente.
> Consultar antes de construir cualquier componente relacionado con coberturas de pacientes.

---

## 1. Los 5 tipos de cobertura

Un paciente puede registrarse a un servicio médico a través de **5 vías distintas**,
cada una con regulación, datos y lógica de facturación diferente.

| Tipo | Argentina | Colombia | Venezuela | Regulador AR |
|---|---|---|---|---|
| **Obra Social / Seguro Social** | Obra Social | EPS | IVSS | SSSalud |
| **Medicina Privada** | Prepaga | Medicina Prepagada | HCM (póliza) | SSSalud |
| **Riesgos Laborales** | ART | ARL | N/A (IVSS + LOPCYMAT) | SRT |
| **Convenio** | Convenio empresarial | Convenio | Convenio | Derecho civil |
| **Particular** | Particular | Particular | Particular | N/A |

> **Nota Venezuela:** No existe mercado privado de ART/ARL. Los accidentes laborales
> los cubre el IVSS (para afiliados) o el empleador directamente bajo LOPCYMAT.
> En el sistema se registra como **Obra Social** (IVSS) o **Particular** según el caso.

---

## 2. Arquitectura en 3 niveles

```
NIVEL 1 — MAESTROS (tablas de referencia)
├── insurances         → Obras Sociales, Prepagas, EPS, IVSS, HCM
├── art_companies      → ARTs (AR), ARLs (CO)
└── convenios          → Convenios directos con empresas/instituciones
    (Particular no tiene maestro — es ausencia de cobertura)

NIVEL 2 — COBERTURAS DEL PACIENTE (portfolio)
└── patient_coverages  → Un paciente puede tener N coberturas simultáneas
                         Relación polymorphic → apunta a insurances | art_companies | convenios | null
                         Ejemplo real: un trabajador tiene Obra Social OSDE + ART Provincia ART

NIVEL 3 — COBERTURA DEL EPISODIO (por turno/consulta)
└── episode_coverages  → Cada consulta/turno selecciona CUÁL cobertura aplica para ESE encuentro
                         Un mismo paciente puede usar OS el lunes y ART el miércoles
                         Si es ART → agrega: número de siniestro, fecha accidente, tipo contingencia
```

---

## 3. Maestros — Modelos y Migraciones

### 3.1 `insurance_companies` ✓ construido

Modelo: `InsuranceCompany` · Cubre: Obra Social · Prepaga · EPS · IVSS · HCM
Tiene `coverage_type_id` FK → `coverage_types`

```php
// Campos clave de la tabla
Schema::create('insurance_companies', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code', 10)->unique();
    $table->string('tax_id', 20)->nullable();           // CUIT / NIT / RIF
    $table->string('type');                              // obra_social | prepaga | eps | ivss | hcm
    $table->string('rnos_code', 10)->nullable();         // Registro SSSalud (Argentina)
    $table->string('address')->nullable();
    $table->unsignedBigInteger('region_id')->nullable();
    $table->string('phone')->nullable();
    $table->string('email')->nullable();
    $table->string('website')->nullable();
    $table->string('logo_path')->nullable();
    $table->unsignedTinyInteger('status')->default(1);   // 1=activo 2=inactivo 9=pendiente 10=suspendido
    $table->timestamps();
    $table->softDeletes();
});
```

**Relaciones:**
```php
// Insurance hasMany InsurancePlan
// InsurancePlan hasMany InsuranceCoverage
// Insurance morphMany PatientCoverage (as coverable)
```

---

### 3.2 `art_companies` (pendiente — FASE 1)

Cubre: ART (Argentina) · ARL (Colombia)
Regulador: SRT (Argentina) / Superintendencia Financiera (Colombia)

**Diferencia crítica:** El contrato es entre la ART y el *empleador*, NO el paciente.
El paciente no "elige" su ART — la tiene por su empleador.

```php
Schema::create('art_companies', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code', 10)->unique();
    $table->string('tax_id', 20)->nullable();            // CUIT (AR) / NIT (CO)
    $table->string('country_code', 3)->default('ARG');   // ISO 3166-1 alpha-3
    $table->string('srt_registration')->nullable();       // N° registro SRT (Argentina)
    $table->string('address')->nullable();
    $table->string('phone')->nullable();                  // Línea de autorizaciones
    $table->string('email')->nullable();
    $table->string('website')->nullable();
    $table->string('logo_path')->nullable();
    $table->unsignedTinyInteger('status')->default(1);
    $table->timestamps();
    $table->softDeletes();
});
```

**Relaciones:**
```php
// ARTCompany morphMany PatientCoverage (as coverable)
```

**Datos que van en el EPISODIO (no en el maestro):**
- `employer_name` — razón social del empleador
- `employer_tax_id` — CUIT/NIT del empleador
- `employer_policy_number` — N° de póliza del empleador con la ART
- `siniestro_number` — N° de siniestro (por accidente)
- `accident_date` — fecha del accidente
- `accident_type` — accidente_trabajo | accidente_in_itinere | enfermedad_profesional

---

### 3.3 `convenios` (pendiente — FASE 1)

Cubre: Acuerdos directos entre la clínica y una empresa/institución.
No es una aseguradora. No hay ley específica — se rige por derecho civil/comercial.
Ejemplos: exámenes preocupacionales, controles periódicos de empresa, convenio con mutual.

```php
Schema::create('convenios', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code', 10)->unique();
    $table->string('contracting_company')->nullable();    // Empresa que firmó el convenio
    $table->string('contracting_tax_id', 20)->nullable(); // CUIT/NIT de esa empresa
    $table->string('contract_number')->nullable();
    $table->text('services_included')->nullable();        // Qué prestaciones cubre
    $table->date('valid_from')->nullable();
    $table->date('valid_until')->nullable();
    $table->string('billing_contact')->nullable();        // A quién facturar
    $table->string('phone')->nullable();
    $table->string('email')->nullable();
    $table->unsignedTinyInteger('status')->default(1);
    $table->timestamps();
    $table->softDeletes();
});
```

---

## 4. Relación Polymorphic — PatientCoverage

Un paciente tiene un **portfolio de coberturas**. Cada cobertura apunta
polymorphically a su maestro correspondiente (o a null si es Particular).

```php
// Migración: patient_coverages
Schema::create('patient_coverages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('patient_id')->constrained('persons')->cascadeOnDelete();

    // Polymorphic: apunta a insurances | art_companies | convenios | null
    $table->nullableMorphs('coverable');                  // coverable_type + coverable_id

    // Clasificación
    $table->string('coverage_category');                  // social | privada | laboral | convenio | particular
    $table->string('country_code', 3)->default('ARG');    // ISO para internacionalización

    // Datos del afiliado (OS/Prepaga/EPS/IVSS/HCM)
    $table->string('affiliate_number')->nullable();
    $table->string('affiliate_type')->nullable();         // titular | familiar | pensionado
    $table->foreignId('plan_id')->nullable()->constrained('insurance_plans');

    // Estado
    $table->boolean('is_primary')->default(false);        // cobertura principal del paciente
    $table->boolean('is_active')->default(true);
    $table->date('valid_from')->nullable();
    $table->date('valid_until')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

### Modelos PHP

```php
// app/Models/PatientCoverage.php
class PatientCoverage extends Model
{
    public function coverable(): MorphTo
    {
        return $this->morphTo();
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'patient_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(InsurancePlan::class);
    }
}

// En Insurance (app/Models/Insurance.php)
public function patientCoverages(): MorphMany
{
    return $this->morphMany(PatientCoverage::class, 'coverable');
}

// En ARTCompany (app/Models/ARTCompany.php)
public function patientCoverages(): MorphMany
{
    return $this->morphMany(PatientCoverage::class, 'coverable');
}

// En Convenio (app/Models/Convenio.php)
public function patientCoverages(): MorphMany
{
    return $this->morphMany(PatientCoverage::class, 'coverable');
}

// En Person/Patient (app/Models/Person.php)
public function coverages(): HasMany
{
    return $this->hasMany(PatientCoverage::class, 'patient_id');
}

public function primaryCoverage(): HasOne
{
    return $this->hasOne(PatientCoverage::class, 'patient_id')
        ->where('is_primary', true)
        ->where('is_active', true);
}
```

### Uso en código

```php
// Cargar coberturas de un paciente con sus entidades
$patient->load(['coverages.coverable', 'coverages.plan']);

// Obtener la cobertura del episodio según tipo
$coverage = $patient->coverages()
    ->where('coverage_category', 'laboral')
    ->with('coverable')
    ->first();

$artCompany = $coverage->coverable; // instancia de ARTCompany

// Crear una cobertura polymorphic
PatientCoverage::create([
    'patient_id'        => $patient->id,
    'coverable_type'    => ARTCompany::class,
    'coverable_id'      => $artCompany->id,
    'coverage_category' => 'laboral',
    'is_primary'        => false,
    'is_active'         => true,
]);
```

---

## 5. Cobertura del Episodio

Cada consulta/turno registra **cuál cobertura aplica para ese encuentro específico**.
Independiente del portfolio del paciente.

```php
// Datos ART específicos del episodio — NO van en patient_coverages
// Van en una tabla episode_art_details o en el episodio clínico directamente:
// - employer_name
// - employer_tax_id
// - employer_policy_number
// - siniestro_number (puede existir ya de un episodio anterior del mismo accidente)
// - accident_date
// - accident_type: accidente_trabajo | accidente_in_itinere | enfermedad_profesional
```

---

## 6. Estructura del Menú

```
MAESTROS
├── Aseguradoras          (insurances — Obras Sociales + Prepagas + EPS + IVSS + HCM)
│   ├── Listado de Aseguradoras    → maestros.aseguradoras.index
│   └── Nueva Aseguradora          → maestros.obras-sociales.create  (ruta actual del componente)
├── ARTs / ARLs           (art_companies)
│   ├── Listado de ARTs            → maestros.arts.index
│   └── Nueva ART                  → maestros.arts.create
└── Convenios             (convenios)
    ├── Listado de Convenios       → maestros.convenios.index
    └── Nuevo Convenio             → maestros.convenios.create
```

---

## 7. Convenciones de Rutas y Componentes

| Entidad | Ruta | Componente Livewire |
|---|---|---|
| Aseguradoras | `maestros.obras-sociales.create` | `master.insurances` |
| Aseguradoras listado | `maestros.aseguradoras.index` | `master.insurances-list` |
| ARTs crear/editar | `maestros.arts.create` | `master.art-companies` |
| ARTs listado | `maestros.arts.index` | `master.art-companies-list` |
| Convenios crear/editar | `maestros.convenios.create` | `master.convenios` |
| Convenios listado | `maestros.convenios.index` | `master.convenios-list` |

---

## 8. Plan de construcción paso a paso

### FASE 1 — Maestros (en progreso)
- [x] Modelo `CoverageType` + migración + factory + seeder — **tabla `coverage_types`** ✓ 2026-05-30
- [x] Modelo `InsuranceCompany` + migración + factory + seeder — **tabla `insurance_companies`** ✓
- [x] `coverage_type_id` FK agregado a `insurance_companies` ✓ 2026-05-30
- [x] Componente `⚡insurances.blade.php` — maqueta estática ✓
- [x] MenuSeeder actualizado: Obras Sociales → Aseguradoras + ARTs/ARLs + Convenios ✓ 2026-05-30
- [ ] **PRÓXIMO:** Livewire completo de Aseguradoras (CRUD real con wire:model + `coverage_type_id` en formulario)
- [ ] Modelo `ARTCompany` + migración + factory + seeder (agregar `coverage_type_id`)
- [ ] Componente `⚡art-companies.blade.php`
- [ ] Modelo `Convenio` + migración + factory + seeder (agregar `coverage_type_id`)
- [ ] Componente `⚡convenios.blade.php`

### FASE 2 — Modelo Person / Paciente (base)
- [x] Modelo `Person` + migración + factory + seeder (ya existe)
- [ ] Migración `patient_coverages` + modelo `PatientCoverage`
- [ ] Morphmap en `AppServiceProvider` para nombres cortos de polymorphic

### FASE 3 — Registro de paciente con coberturas
- [ ] Componente de registro de paciente (módulo CLÍNICA)
- [ ] Tab "Coberturas" dentro del paciente — agregar N coberturas de cualquier tipo
- [ ] Formulario dinámico según `coverage_category` seleccionada

### FASE 4 — Episodio clínico
- [ ] Selección de cobertura activa por turno/consulta
- [ ] Formulario adicional ART al seleccionar cobertura laboral

---

## 9. Consideraciones internacionales

El sistema está diseñado para Argentina pero debe funcionar en Colombia y Venezuela.
La clave es el campo `country_code` (ISO 3166-1 alpha-3) en los maestros.

| Campo | ARG | COL | VEN |
|---|---|---|---|
| Tax ID | CUIT (xx-xxxxxxxx-x) | NIT | RIF |
| Social insurance | Obra Social / RNOS | EPS / REPS | IVSS |
| Work accident | ART / N° siniestro | ARL / FURAT | IVSS (no ARL privada) |
| Private insurance | Prepaga | Medicina Prepagada | HCM (póliza + certificado) |
| ID document | DNI | Cédula | Cédula |

> Para Venezuela: el campo `srt_registration` de ARTCompany no aplica.
> El componente debe ocultar/mostrar campos según el `country_code` del sistema.
