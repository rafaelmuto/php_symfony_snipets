<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractRequestDTO
{
    protected const STRING = 'string';

    protected const INTEGER = 'integer';
    protected const FLOAT = 'float';
    protected const DATETIME = 'datetime';
    protected const EMAIL = 'email';
    protected const ROOM_SKU = 'room_sku';

    private const TYPES = [
        self::STRING => 'is_string',
        self::INTEGER => 'is_int',
        self::FLOAT => 'is_float',
        self::DATETIME => [self::class, 'checkDatetimeString'],
        self::EMAIL => [self::class, 'checkIsEmail'],
        self::ROOM_SKU => [self::class, 'checkIsRoomSku'],
    ];

    private array $payload_keys;

    protected array $payload;

    public function __construct(Request $request, array $payload_keys)
    {
        $this->payload_keys = $payload_keys;
        $this->payload = json_decode($request->getContent(), true);

        $this->checkPayloadKeys($this->payload);

        $this->checkPayload($this->payload);
    }

    abstract protected function checkPayload(array $payload): void;

    private function checkPayloadKeys(array $payload): void
    {
        $missing_payload_keys = array_diff(array_keys($this->payload_keys), array_keys($this->payload));

        if (!empty($missing_payload_keys)) {
            $expected_keys = implode(', ', $missing_payload_keys);

            throw new \Exception('Bad payload. Missing key(s): ' . $expected_keys, Response::HTTP_BAD_REQUEST);
        }

        $errors = [];
        foreach ($payload as $key => $value) {
            $data_type = $this->payload_keys[$key];

            if (is_callable($data_type) && !call_user_func($data_type, $value)) {
                $errors[] = $key;
            } elseif (!call_user_func(self::TYPES[$data_type], $value)) {
                $errors[] = $key;
            }
        }

        if (!empty($errors)) {
            $exception_msg = 'Bad payload. Bad data for: ' . implode(', ', $errors);

            throw new \Exception($exception_msg, Response::HTTP_BAD_REQUEST);
        }
    }

    protected function checkIsEmail($value): bool
    {
        return is_string($value) && str_contains($value, '@');
    }

    protected function checkIsRoomSku($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $exploded_string = explode('-', $value);

        if (3 != count($exploded_string)) {
            return false;
        }

        return true;
    }

    public static function checkDatetimeString($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        try {
            new \DateTime($value);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
