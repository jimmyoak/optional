<?php

namespace JimmyOak\Optional;

use JimmyOak\Optional\Exception\FlatMapCallbackMustReturnOptionalException;
use JimmyOak\Optional\Exception\NoSuchElementException;
use JimmyOak\Optional\Exception\NullPointerException;

final class Some extends Optional
{
    /**
     * Value holder. Null in case of no value.
     * @var mixed|null
     */
    private $value;

    public function __construct($value = null)
    {
        $this->requireNonNull($value);
        $this->value = $value;
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
        return $this->value;
    }

    /**
     * Returns true if optional has a value, otherwise, returns false
     *
     * @return bool
     */
    public function isPresent(): bool
    {
        return true;
    }

    /**
     * Executes given $callback in case value is present
     *
     * @param callable $callback
     */
    public function ifPresent(callable $callback)
    {
        $callback($this->value);
    }

    /**
     * Filters held value. Returns empty optional in case given $predicate returns false
     *
     * @param callable $predicate
     *
     * @return Optional
     * @throws NullPointerException
     */
    public function filter(callable $predicate)
    {
        $this->requireNonNull($predicate);
        return $predicate($this->value) ? $this : parent::empty();
    }

    /**
     * In case a value is present applies given $mapper callback.
     * Returns new optional if $mapper returns non null value
     *
     * @param callable $mapper
     *
     * @return Optional
     * @throws NullPointerException
     */
    public function map(callable $mapper)
    {
        $this->requireNonNull($mapper);
        return self::ofNullable($mapper($this->value));
    }

    /**
     * In case a value is present applies given $mapper callback.
     * Returns returned Optional from $mapper callback
     *
     * @param callable $mapper
     *
     * @return Optional
     * @throws FlatMapCallbackMustReturnOptionalException
     * @throws NullPointerException
     */
    public function flatMap(callable $mapper)
    {
        $this->requireNonNull($mapper);
        return $this->requireOptional($mapper($this->value));
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
        return $this->value;
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
        return $this->value;
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
        return $this->value;
    }

    /**
     * Compares two optionals. Returns true in case value is the same.
     *
     * @param Optional $obj
     *
     * @return bool
     */
    public function equals(Optional $obj): bool
    {
        return $this === $obj || ($obj instanceof Some && $this->value === $obj->value);
    }

    public function __toString()
    {
        return 'Optional[' . $this->value . ']';
    }

    private function requireOptional($value)
    {
        if (!$value instanceof Optional) {
            throw new FlatMapCallbackMustReturnOptionalException();
        }
        return $value;
    }
}