<?php

/*
 * This file is part of the Neos.ContentRepository package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

declare(strict_types=1);

namespace Neos\ContentRepository\Core\Feature\NodeModification\Dto;

/**
 * "Raw" / Serialized property value as saved in the event log // in projections.
 *
 * This means: "value" must be a simple PHP data type (no objects allowed!)
 * Null is not permitted. To unset a property {@see UnsetPropertyValue} must be used.
 *
 * @api used as part of commands/events
 */
final class SerializedPropertyValue implements \JsonSerializable
{
    /**
     * @param int|float|string|bool|array<int|string,mixed>|\ArrayObject<int|string,mixed> $value
     */
    private function __construct(
        public readonly int|float|string|bool|array|\ArrayObject $value,
        public readonly string $type
    ) {
    }

    /**
     * If the value is NULL an unset-property instruction will be returned instead.
     *
     * @param int|float|string|bool|array<int|string,mixed>|\ArrayObject<int|string,mixed>|null $value
     */
    public static function create(
        int|float|string|bool|array|\ArrayObject|null $value,
        string $type
    ): self|UnsetPropertyValue {
        if ($value === null) {
            return UnsetPropertyValue::get();
        }
        return new self($value, $type);
    }

    /**
     * @param array{type:string,value:mixed} $valueAndType
     */
    public static function fromArray(array $valueAndType): self|UnsetPropertyValue
    {
        if (!array_key_exists('value', $valueAndType)) {
            throw new \InvalidArgumentException('Missing array key "value"', 1546524597);
        }
        if (!array_key_exists('type', $valueAndType)) {
            throw new \InvalidArgumentException('Missing array key "type"', 1546524609);
        }

        return self::create($valueAndType['value'], $valueAndType['type']);
    }

    /**
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value,
            'type' => $this->type
        ];
    }

    /**
     * @return array<string, string>
     * @throws \JsonException
     */
    public function __debugInfo(): array
    {
        return [
            'type' => $this->type,
            'value' => json_encode($this->value, JSON_THROW_ON_ERROR)
        ];
    }
}
