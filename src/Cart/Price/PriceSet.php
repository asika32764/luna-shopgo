<?php

/**
 * Part of 372 project.
 *
 * @copyright  Copyright (C) 2017 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Cart\Price;

use Brick\Math\BigNumber;
use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use JetBrains\PhpStorm\Internal\TentativeType;
use Traversable;

/**
 * The PriceSet class.
 *
 * @method PriceObject plus(string $name, string | PriceObject $price)
 * @method PriceObject minus(string $name, string | PriceObject $price)
 * @method PriceObject divide(string $name, string | PriceObject $price, int $scale = null)
 * @method PriceObject exactlyDivide(string $name, string | PriceObject $price)
 * @method PriceObject multiply(string $name, string | PriceObject $price)
 * @method PriceObject remainder(string $name, string | PriceObject $price)
 * @method int         compare(string $name, string | PriceObject $price)
 * @method PriceObject power(string $name, string | PriceObject $price)
 * @method PriceObject powermod(string $name, string | PriceObject $price, int | PriceObject $modulus)
 *
 * @since  __DEPLOY_VERSION__
 */
class PriceSet implements \IteratorAggregate, \JsonSerializable, \ArrayAccess
{
    /**
     * Property prices.
     *
     * @var  PriceObject[]
     */
    protected array $prices = [];

    /**
     * PriceSet constructor.
     *
     * @param PriceObject[] $prices
     */
    public function __construct(array $prices = [])
    {
        $this->prices = $prices;
    }

    /**
     * sum
     *
     * @return  PriceObject
     */
    public function sum(): PriceObject
    {
        $price = new PriceObject('sum', '0');

        /** @var PriceObject $item */
        foreach ($this->prices as $item) {
            $price = $price->plus($item);
        }

        return $price;
    }

    /**
     * has
     *
     * @param string $name
     *
     * @return  bool
     */
    public function has(string $name): bool
    {
        return isset($this->prices[$name]);
    }

    /**
     * set
     *
     * @param PriceObject $price
     *
     * @return  static
     */
    public function set(PriceObject $price): static
    {
        $name = $price->getName();

        $this->prices[$name] = $price;

        return $this;
    }

    /**
     * get
     *
     * @param string $name
     * @param bool   $createIfNotExists
     *
     * @return  PriceObject
     */
    public function get(string $name, bool $createIfNotExists = false): PriceObject
    {
        if (!isset($this->prices[$name])) {
            if (!$createIfNotExists) {
                throw new \InvalidArgumentException('Price name with: ' . $name . ' not exists.');
            }

            $this->set(new PriceObject($name, '0', $name));
        }

        return $this->prices[$name];
    }

    /**
     * remove
     *
     * @param string $name
     *
     * @return  PriceSet
     *
     * @since  __DEPLOY_VERSION__
     */
    public function remove(string $name): self
    {
        unset($this->prices[$name]);

        return $this;
    }

    /**
     * modify
     *
     * @param string                      $name
     * @param \Closure|string|PriceObject $newPrice
     *
     * @return  PriceObject
     */
    public function modify(string $name, \Closure|string|PriceObject $newPrice): PriceObject
    {
        $price = $this->get($name);

        if ($newPrice instanceof \Closure) {
            $modified = $newPrice($price);
        } elseif ($newPrice instanceof PriceObject) {
            $modified = $newPrice->getPrice();
        } else {
            $modified = $newPrice;
        }

        $price = $price->withPrice($modified);

        $this->set($price);

        return $price;
    }

    /**
     * Method to get property Prices
     *
     * @return  PriceObject[]
     */
    public function getPrices(): array
    {
        return $this->prices;
    }

    /**
     * Method to set property prices
     *
     * @param   PriceObject[] $prices
     *
     * @return  static  Return self to support chaining.
     */
    public function setPrices(array $prices): static
    {
        $this->prices = $prices;

        return $this;
    }

    /**
     * Retrieve an external iterator
     *
     * @return Traversable An instance of an object implementing Iterator or Traversable.
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->prices);
    }

    /**
     * __call
     *
     * @param string $name
     * @param array  $args
     *
     * @return  PriceSet
     */
    public function __call(string $name, array $args)
    {
        $allow = [
            'plus',
            'minus',
            'divide',
            'exactlyDivide',
            'multiply',
            'remainder',
            'compare',
            'power',
            'powermod'
        ];

        if (\count($args) > 1 && \in_array(strtolower($name), $allow, true)) {
            $priceName = array_shift($args);

            if (!$this->has($priceName)) {
                throw new \InvalidArgumentException('Price name: ' . $priceName . ' not found.');
            }

            $price = $this->get($priceName)->$name(...$args);

            return $this->set($price);
        }

        throw new \BadMethodCallException('Method name: ' . $name . ' not found.');
    }

    /**
     * toArray
     *
     * @param bool $recursive
     *
     * @return  PriceObject[]|array
     */
    public function toArray(bool $recursive = false): array
    {
        $prices = $this->prices;

        if ($recursive) {
            foreach ($prices as &$price) {
                $price = $price->toArray();
            }
        }

        return $prices;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): mixed
    {
        return $this->prices;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has((string) $offset);
    }

    public function offsetGet(mixed $offset): PriceObject
    {
        return $this->get((string) $offset, true);
    }

    /**
     * offsetSet
     *
     * @param  mixed        $offset
     * @param  PriceObject  $value
     *
     * @return  void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($value->getName() !== $offset) {
            $value = $value->withName((string) $offset);
        }

        $this->set($value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }
}
