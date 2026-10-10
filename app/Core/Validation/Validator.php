<?php

namespace Tomvis\HIMPro\Core\Validation;

class Validator
{
    protected array $data;
    protected array $rules;
    protected array $errors = [];

    protected array $messages = [
        'required' => 'ฟิลด์ :field จำเป็นต้องระบุ',
        'string' => 'ฟิลด์ :field ต้องเป็นข้อความ',
        'integer' => 'ฟิลด์ :field ต้องเป็นจำนวนเต็ม',
        'numeric' => 'ฟิลด์ :field ต้องเป็นตัวเลข',
        'email' => 'ฟิลด์ :field รูปแบบอีเมลไม่ถูกต้อง',
        'hn' => 'รูปแบบ HN ไม่ถูกต้อง (เช่น 67000123)',
        'an' => 'รูปแบบ AN ไม่ถูกต้อง (เช่น 67000456)',
        'in' => 'ค่าในฟิลด์ :field ไม่ถูกต้อง',
        'min' => 'ฟิลด์ :field ต้องมีขนาดอย่างน้อย :param',
        'max' => 'ฟิลด์ :field ต้องมีขนาดไม่เกิน :param',
    ];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make(array $data, array $rules): static
    {
        $validator = new static($data, $rules);
        $validator->validate();
        return $validator;
    }

    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $ruleset) {
            $value = $this->data[$field] ?? null;
            $ruleList = is_array($ruleset) ? $ruleset : explode('|', $ruleset);

            foreach ($ruleList as $rule) {
                $param = null;
                if (str_contains($rule, ':')) {
                    list($rule, $param) = explode(':', $rule, 2);
                }

                $method = 'validate' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    if (!$this->$method($value, $param)) {
                        $msg = $this->messages[$rule] ?? "ฟิลด์ {$field} ไม่ถูกต้อง";
                        $msg = str_replace([':field', ':param'], [$field, (string)$param], $msg);
                        $this->errors[$field][] = $msg;
                    }
                }
            }
        }

        return empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    protected function validateRequired(mixed $value): bool
    {
        if (is_null($value)) return false;
        if (is_string($value) && trim($value) === '') return false;
        if (is_array($value) && empty($value)) return false;
        return true;
    }

    protected function validateString(mixed $value): bool
    {
        return is_null($value) || is_string($value);
    }

    protected function validateInteger(mixed $value): bool
    {
        return is_null($value) || filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    protected function validateNumeric(mixed $value): bool
    {
        return is_null($value) || is_numeric($value);
    }

    protected function validateEmail(mixed $value): bool
    {
        return is_null($value) || filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateHn(mixed $value): bool
    {
        if (empty($value)) return true;
        // Standard hospital HN format check (e.g., 67000123 or 6 to 10 digits)
        return (bool)preg_match('/^[0-9]{6,10}$/', (string)$value);
    }

    protected function validateAn(mixed $value): bool
    {
        if (empty($value)) return true;
        return (bool)preg_match('/^[0-9]{6,10}$/', (string)$value);
    }

    protected function validateIn(mixed $value, ?string $param): bool
    {
        if (is_null($value) || $param === null) return true;
        $allowed = explode(',', $param);
        return in_array((string)$value, $allowed, true);
    }

    protected function validateMin(mixed $value, ?string $param): bool
    {
        if (is_null($value)) return true;
        $min = (int)$param;
        if (is_numeric($value)) return $value >= $min;
        if (is_string($value)) return mb_strlen($value) >= $min;
        return true;
    }

    protected function validateMax(mixed $value, ?string $param): bool
    {
        if (is_null($value)) return true;
        $max = (int)$param;
        if (is_numeric($value)) return $value <= $max;
        if (is_string($value)) return mb_strlen($value) <= $max;
        return true;
    }
}
