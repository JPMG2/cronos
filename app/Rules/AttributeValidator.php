<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Validation\Rule;

/**
 * Utility class for building reusable Laravel validation rules.
 *
 * This class centralizes common validation patterns to maintain consistency
 * across the application and reduce code duplication.
 */
final class AttributeValidator
{
    private const MAX_STRING_LENGTH = 255;

    private const XSS_PREVENTION_PATTERN = '/^([^<>]*)$/';

    private const DIGIT_PATTERN = '/^([0-9\s\-\+\(\)]*)$/';

    private const DECIMAL_PATTERN = '/^\d{1,3}(,\d{3})*(\.\d+)?$|^\d+(\.\d+)?$/';

    /**
     * Build a unique email rule (RFC + DNS), optionally ignoring an existing record id.
     *
     * @return array<int, string>
     */
    public static function uniqueEmail(string $model, string $uniqueField, ?int $id = null): array
    {
        return [
            'required',
            'email:rfc,dns',
            self::buildUniqueRule($model, $uniqueField, $id),
            'regex:' . self::XSS_PREVENTION_PATTERN,
            'max:' . self::MAX_STRING_LENGTH,
        ];
    }

    /**
     * Build a required, unique, length-bounded string rule (commonly used for names/codes).
     *
     * @return array<int, string>
     */
    public static function uniqueIdNameLength(string $length, string $model, string $uniqueField, ?int $id = null): array
    {
        return [
            'required',
            self::buildUniqueRule($model, $uniqueField, $id),
            'min:' . $length,
            'regex:' . self::XSS_PREVENTION_PATTERN,
            'max:' . self::MAX_STRING_LENGTH,
        ];
    }

    /**
     * Build a digit-only string rule allowing common phone separators.
     *
     * @return array<int, string>
     */
    public static function digitValid(string $length, bool $required): array
    {
        return [
            $required ? 'required' : 'sometimes',
            'min:' . $length,
            'regex:' . self::DIGIT_PATTERN,
            'max:' . self::MAX_STRING_LENGTH,
        ];
    }

    /**
     * Build an optional unique email rule (RFC only).
     *
     * @return array<int, string>
     */
    public static function emailValid(string $model, string $uniqueField, ?int $id = null): array
    {
        return [
            'sometimes',
            self::buildUniqueRule($model, $uniqueField, $id),
            'email:rfc',
            'regex:' . self::XSS_PREVENTION_PATTERN,
            'max:' . self::MAX_STRING_LENGTH,
        ];
    }

    /**
     * Build an optional unique email rule (RFC + DNS) tied to an existing record id.
     *
     * @return array<int, string>
     */
    public static function emailValidById(int $id, string $model, string $uniqueField): array
    {
        return [
            'sometimes',
            'email:rfc,dns',
            self::buildUniqueRule($model, $uniqueField, $id),
            'regex:' . self::XSS_PREVENTION_PATTERN,
            'max:' . self::MAX_STRING_LENGTH,
        ];
    }

    /**
     * Build a length-bounded string rule, required or optional.
     *
     * @return array<int, string>
     */
    public static function stringValid(bool $required, string $length): array
    {
        return [
            $required ? 'required' : 'sometimes',
            'min:' . $length,
            'regex:' . self::XSS_PREVENTION_PATTERN,
            'max:' . self::MAX_STRING_LENGTH,
        ];
    }

    /**
     * Build an optional unique string rule.
     *
     * @return array<int, string>
     */
    public static function stringValidUnique(string $model, string $uniqueField, string $length, ?int $id = null): array
    {
        return [
            'sometimes',
            'min:' . $length,
            self::buildUniqueRule($model, $uniqueField, $id),
            'regex:' . self::XSS_PREVENTION_PATTERN,
            'max:' . self::MAX_STRING_LENGTH,
        ];
    }

    /**
     * Build a URL rule that verifies the host responds, required or optional.
     *
     * @return array<int, string>
     */
    public static function webValid(bool $required): array
    {
        return [
            $required ? 'required' : 'sometimes',
            'url',
            'active_url',
            'regex:' . self::XSS_PREVENTION_PATTERN,
            'max:' . self::MAX_STRING_LENGTH,
        ];
    }

    public static function mayorValid(): string
    {
        return 'gt:0';
    }

    /**
     * Build a `d-m-Y` date rule, required or optional.
     *
     * @return array<int, string>
     */
    public static function dateValid(bool $required): array
    {
        return [
            $required ? 'required' : 'sometimes',
            'date_format:d-m-Y',
            'max:' . self::MAX_STRING_LENGTH,
            'regex:' . self::XSS_PREVENTION_PATTERN,
        ];
    }

    public static function hasTobeArray(string $length): string
    {
        return 'array|min:' . $length;
    }

    /**
     * Build an `exists` rule for foreign keys; when not required, excludes when the column is 0.
     *
     * @return array<int, string>
     */
    public static function requireAndExists(string $model, string $uniqueField, string $column, bool $require = false): array
    {
        if ($require) {
            return ['required', 'integer', 'exists:' . $model . ',' . $uniqueField];
        }

        return ['nullable', 'exclude_if:' . $column . ',0', 'integer'];
    }

    /**
     * @return array<int, string>
     */
    public static function booleanValue(bool $required): array
    {
        return [$required ? 'required' : 'sometimes', 'boolean'];
    }

    /**
     * @return array<int, string>
     */
    public static function dateAfther(bool $required, string $date): array
    {
        return [
            $required ? 'required' : 'sometimes',
            'date_format:d-m-Y',
            'after:' . $date,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function numericDecimal(bool $required): array
    {
        if ($required) {
            return ['required', 'regex:' . self::DECIMAL_PATTERN, 'max:' . self::MAX_STRING_LENGTH];
        }

        return ['sometimes', 'nullable', 'regex:' . self::DECIMAL_PATTERN, 'max:' . self::MAX_STRING_LENGTH];
    }

    /**
     * @return array<int, string>
     */
    public static function numericInteger(bool $required, int $min): array
    {
        if ($required) {
            return [
                'required',
                'integer:strict',
                'max:' . self::MAX_STRING_LENGTH,
                'regex:' . self::XSS_PREVENTION_PATTERN,
                'min:' . $min,
            ];
        }

        return [
            'sometimes',
            'nullable',
            'integer:strict',
            'max:' . self::MAX_STRING_LENGTH,
            'regex:' . self::XSS_PREVENTION_PATTERN,
            'min:' . $min,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function moneyInteger(bool $required): array
    {
        if ($required) {
            return ['required', 'integer:strict', 'regex:' . self::XSS_PREVENTION_PATTERN];
        }

        return ['sometimes', 'nullable', 'integer:strict', 'regex:' . self::XSS_PREVENTION_PATTERN];
    }

    /**
     * Build a required, scoped unique rule using Laravel's Rule::unique builder.
     *
     * @return array<int, mixed>
     */
    public static function requiredExistModelRelation(string $model, string $uniqueField, string $columRelation, ?int $columValue, ?int $id): array
    {
        $unique = Rule::unique($model, $uniqueField)->where($columRelation, $columValue);

        if ($id !== null) {
            $unique->ignore($id);
        }

        return ['required', $unique, 'regex:' . self::XSS_PREVENTION_PATTERN];
    }

    /**
     * Compose Laravel's string-based `unique` rule, ignoring an existing record id when provided.
     */
    private static function buildUniqueRule(string $model, string $uniqueField, ?int $id): string
    {
        $rule = 'unique:' . $model . ',' . $uniqueField;

        return $id !== null ? $rule . ',' . $id : $rule;
    }
}
