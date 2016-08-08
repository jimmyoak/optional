<?php

namespace JimmyOak\Optional;

use JimmyOak\Optional\Exception\FlatMapCallbackMustReturnOptionalException;
use JimmyOak\Optional\Exception\NoSuchElementException;
use JimmyOak\Optional\Exception\NullPointerException;

final class Optional
{
    /**
     * Value holder. Null in case of no value.
     * @var mixed|null
     */
    private $value;

    private function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * Create empty optional with no value.
     *
     * @return Optional
     */
    public static function empty(): Optional
    {
        static $empty = null;
        if (null === $empty) {
            $empty = new Optional();
        }

        return $empty;
    }

    /**
     * Create optional with given value
     *
     * @throws NoSuchElementException when null value is specified
     *
     * @param $value
     *
     * @return Optional
     */
    public static function of($value)
    {
        self::requireNonNull($value);
        return new Optional($value);
    }

    /**
     * Creates an optional given a possibly null value
     *
     * @param $value
     *
     * @return Optional
     */
    public static function ofNullable($value)
    {
        return $value === null ? self::empty() : self::of($value);
    }

    /**
     * Gets value if present
     *
     * @throws NoSuchElementException in case no value
     *
     * @return mixed|null
     */
    public function get()
    {
        if (null === $this->value) {
            throw new NoSuchElementException("No value present");
        }

        return $this->value;
    }

    /**
     * Returns true if optional has a value, otherwise, returns false
     *
     * @return bool
     */
    public function isPresent(): bool
    {
        return $this->value != null;
    }

    /**
     * Executes given $callback in case value is present
     *
     * @param callable $callback
     */
    public function ifPresent(callable $callback)
    {
        if ($this->value !== null) {
            $callback($this->value);
        }
    }

    /**
     * Filters held value. Returns empty optional in case given $predicate returns false
     *
     * @param callable $predicate
     *
     * @return $this|Optional
     * @throws \Exception
     */
    public function filter(callable $predicate)
    {
        $this->requireNonNull($predicate);
        if (!$this->isPresent()) {
            return $this;
        } else {
            return $predicate($this->value) ? $this : self::empty();
        }
    }

    /**
     * In case a value is present applies given $mapper callback.
     * Returns new optional if $mapper returns non null value
     *
     * @param callable $mapper
     *
     * @return Optional
     * @throws \Exception
     */
    public function map(callable $mapper)
    {
        $this->requireNonNull($mapper);
        if (!$this->isPresent()) {
            return self::empty();
        } else {
            return self::ofNullable($mapper($this->value));
        }
    }

    /**
     * In case a value is present applies given $mapper callback.
     * Returns returned Optional from $mapper callback
     *
     * @param callable $mapper
     *
     * @return Optional
     * @throws \Exception
     */
    public function flatMap(callable $mapper)
    {
        $this->requireNonNull($mapper);
        if (!$this->isPresent()) {
            return self::empty();
        } else {
            return self::requireOptional($mapper($this->value));
        }
    }

    /**
     * If present returns held value, otherwise returns given $other value
     *
     * @param $other
     *
     * @return mixed|null
     */
    public function orElse($other)
    {
        return $this->value !== null ? $this->value : $other;
    }

    /**
     * If present returns held value, otherwise returns
     * value returned from given $other callback
     *
     * @param callable $other
     *
     * @return mixed|null
     */
    public function orElseGet(callable $other)
    {
        return $this->value !== null ? $this->value : $other();
    }

    /**
     * If present returns held value, othwerise throws given $exception
     *
     * @param \Exception $exception
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function orElseThrow(\Exception $exception)
    {
        if ($this->value !== null) {
            return $this->value;
        }

        throw $exception;
    }

    /**
     * Compares two optionals. Returns true in case value is the same.
     *
     * @param Optional $obj
     *
     * @return bool
     */
    public function equals(self $obj): bool
    {
        return $this === $obj || $this->value === $obj->value;
    }

    public function __toString()
    {
        return 'Optional' . ($this->value ? '[' . $this->value . ']' : '.empty');
    }

    private static function requireNonNull($value)
    {
        if (null === $value) {
            throw new NullPointerException();
        }

        return $value;
    }

    private static function requireOptional($value)
    {
        if (!$value instanceof self) {
            throw new FlatMapCallbackMustReturnOptionalException();
        }
        return $value;
    }
}
