<?php

declare(strict_types=1);

namespace App\Rules;
use Illuminate\Validation\Rules\Enum;

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

    /**
     * Generates a validation rule array for ensuring the uniqueness of an email address
     * while adhering to specific constraints such as format and length.
     *
     * @param  string  $model  The table or model name where the unique check is performed.
     * @param  string  $uniqueField  The database field to validate uniqueness against.
     * @param  int|null  $id  Optional ID to exclude from the unique check, typically for updates.
     * @return array Array of validation rules to be applied on the input.
     */
    public static function uniqueEmail(string $model, string $uniqueField, int $id = null): array
    {
        if ($id) {
            return ['required', 'email:rfc,dns', 'unique:' . $model . ',' . $uniqueField . ',' . $id, 'regex:' . self::XSS_PREVENTION_PATTERN, 'max:' . self::MAX_STRING_LENGTH];
        }

        return ['required', 'email:rfc,dns', 'unique:' . $model . ',' . $uniqueField, 'regex:' . self::XSS_PREVENTION_PATTERN, 'max:' . self::MAX_STRING_LENGTH];
    }

    public static function uniqueIdNameLength(string $length, string $model, string $uniqueField, $id = null): array
    {
        if ($id) {
            return ['required', 'unique:' . $model . ',' . $uniqueField . ',' . $id, 'min:' . $length, 'regex:' . self::XSS_PREVENTION_PATTERN, 'max:' . self::MAX_STRING_LENGTH];
        }

        return ['required', 'unique:' . $model . ',' . $uniqueField, 'min:' . $length, 'regex:' . self::XSS_PREVENTION_PATTERN, 'max:' . self::MAX_STRING_LENGTH];
    }

    public static function digitValid(string $length, $required): array
    {
        if ($required) {
            return ['required', 'min:' . $length, 'regex:' . self::DIGIT_PATTERN, 'max:' . self::MAX_STRING_LENGTH];
        }

        return ['sometimes', 'min:' . $length, 'regex:' . self::DIGIT_PATTERN, 'max:' . self::MAX_STRING_LENGTH];
    }

    public static function emailValid(string $model, string $uniqueField, $id = null): array
    {
        return $id ?
           ['sometimes', 'unique:' . $model . ',' . $uniqueField . ',' . $id, 'email:rfc', 'regex:' . self::XSS_PREVENTION_PATTERN, 'max:' . self::MAX_STRING_LENGTH] :
           ['sometimes', 'unique:' . $model . ',' . $uniqueField, 'email:rfc', 'regex:' . self::XSS_PREVENTION_PATTERN, 'max:' . self::MAX_STRING_LENGTH];
    }

    public static function emailValidById(string $id, string $model, string $uniqueField): array
    {
        return ['sometimes', 'email:rfc,dns', 'unique:' . $model . ',' . $uniqueField . ',' . $id, 'regex:' . self::XSS_PREVENTION_PATTERN, 'max:' . self::MAX_STRING_LENGTH];
    }

    public static function stringValid($required, string $length): array
    {
        return $required ?
            ['required', 'min:' . $length, 'regex:' . self::XSS_PREVENTION_PATTERN, 'max:' . self::MAX_STRING_LENGTH] :
            ['sometimes', 'min:' . $length, 'regex:' . self::XSS_PREVENTION_PATTERN, 'max:' . self::MAX_STRING_LENGTH];

    }

    public static function stringValidUnique(string $model, string $uniqueField, string $length, $id = null): array
    {
        return $id ?
            ['sometimes', 'min:' . $length, 'unique:' . $model . ',' . $uniqueField . ',' . $id, 'regex:' . self::XSS_PREVENTION_PATTERN, 'max:' . self::MAX_STRING_LENGTH] :
            ['sometimes', 'min:' . $length, 'unique:' . $model . ',' . $uniqueField, 'regex:' . self::XSS_PREVENTION_PATTERN, 'max:' . self::MAX_STRING_LENGTH];
    }

    public static function webValid($required): array
    {
        return $required
            ? ['required', 'url', 'active_url', 'regex:' . self::XSS_PREVENTION_PATTERN, 'max:' . self::MAX_STRING_LENGTH]
            : ['sometimes', 'url', 'active_url', 'regex:' . self::XSS_PREVENTION_PATTERN, 'max:' . self::MAX_STRING_LENGTH];

    }

    public static function mayorValid(): string
    {
        return 'gt:0';
    }


    public static function dateValid($required): array
    {
        return $required
            ? ['required', 'date_format:d-m-Y', 'max:' . self::MAX_STRING_LENGTH, 'regex:' . self::XSS_PREVENTION_PATTERN] :
              ['sometimes', 'date_format:d-m-Y', 'max:' . self::MAX_STRING_LENGTH, 'regex:' . self::XSS_PREVENTION_PATTERN];
    }

    public static function hasTobeArray(string $length): string
    {
        return 'array|min:' . $length;
    }

    public static function requireAndExists(string $model, string $uniqueField, string $column, $require = null): array
    {
        return $require ? ['required', 'integer', 'exists:' . $model . ',' . $uniqueField]
            : ['nullable', 'exclude_if:' . $column . ',0', 'integer'];

    }

    public static function booleanValue($required): array
    {
        return $required ? ['required', 'boolean'] : ['sometimes', 'boolean'];

    }

    public static function dateAfther($required, string $date): array
    {
        return $required ? ['required', 'date_format:d-m-Y', 'after:' . $date] :
            ['sometimes', 'date_format:d-m-Y', 'after:' . $date];
    }


    public static function numericDecimal($required): array
    {
        $pattern = '/^\d{1,3}(,\d{3})*(\.\d+)?$|^\d+(\.\d+)?$/';

        return $required ? ['required', 'regex:' . $pattern, 'max:' . self::MAX_STRING_LENGTH] :
            ['sometimes', 'nullable', 'regex:' . $pattern, 'max:' . self::MAX_STRING_LENGTH];
    }

    public static function numericInteger($required): array
    {
        return $required ? ['required', 'integer:strict', 'max:' . self::MAX_STRING_LENGTH, 'regex:' . self::XSS_PREVENTION_PATTERN] :
            ['sometimes', 'nullable', 'integer:strict', 'max:' . self::MAX_STRING_LENGTH, 'regex:' . self::XSS_PREVENTION_PATTERN];
    }

    public static function moneyInteger($required): array
    {
        return $required ? ['required', 'integer:strict', 'regex:' . self::XSS_PREVENTION_PATTERN] :
            ['sometimes', 'nullable', 'integer:strict', 'regex:' . self::XSS_PREVENTION_PATTERN];
    }
}
