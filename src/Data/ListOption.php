<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Data;

use Windwalker\Data\ValueObject;

/**
 * The ListOption class.
 */
class ListOption extends ValueObject
{
    public string $uid = '';
    public string $value = '';
    public string $text = '';
    public string $color = '';

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param  string  $value
     *
     * @return  static  Return self to support chaining.
     */
    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param  string  $text
     *
     * @return  static  Return self to support chaining.
     */
    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }
    /**
     * @param  string  $color
     *
     * @return  static  Return self to support chaining.
     */
    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @param  string  $uid
     *
     * @return  static  Return self to support chaining.
     */
    public function setUid(string $uid): static
    {
        $this->uid = $uid;

        return $this;
    }
}
