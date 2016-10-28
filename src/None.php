<?php

namespace JimmyOak\Optional;

use JimmyOak\Optional\Exception\BadMethodCallException;
use JimmyOak\Optional\Exception\FlatMapCallbackMustReturnOptionalException;
use JimmyOak\Optional\Exception\NoSuchElementException;
use JimmyOak\Optional\Exception\NullPointerException;

final class None extends Optional
{
    public static function empty(): Optional
    {
        throw new BadMethodCallException('Use Optional::empty instead');
    }

    public static function of($value)
    {
        throw new BadMethodCallException('Use Optional::of instead');
    }

    public static function ofNullable($value)
    {
        throw new BadMethodCallException('Use Optional::ofNullable instead');
    }

    /**
     * Gets value if present
     *
     * @throws NoSuchElementException in case no value
     *
     * @return mixed
     */
    public function get()
    {
        throw new NoSuchElementException("No value present");
    }

    /**
     * Returns true if optional has a value, otherwise, returns false
     *
     * @return bool
     */
    public function isPresent() : bool
    {
        return false;
    }

    /**
     * Executes given $callback in case value is present
     *
     * @param callable $callback
     */
    public function ifPresent(callable $callback)
    {
        // do nothing
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
        self::requireNonNull($predicate);
        return $this;
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
        self::requireNonNull($mapper);
        return parent::empty();
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
        self::requireNonNull($mapper);
        return parent::empty();
    }

    /**
     * If present returns held value, otherwise returns given $other value
     *
     * @param $other
     *
     * @return mixed
     */
    public function orElse($other)
    {
        return $other;
    }

    /**
     * If present returns held value, otherwise returns
     * value returned from given $other callback
     *
     * @param callable $other
     *
     * @return mixed
     */
    public function orElseGet(callable $other)
    {
        return $other();
    }

    /**
     * If present returns held value, othwerise throws given $exception
     *
     * @param \Exception $exception
     *
     * @return mixed
     * @throws \Exception
     */
    public function orElseThrow(\Exception $exception)
    {
        throw $exception;
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
        return $this === $obj || $obj instanceof None;
    }

    public function __toString()
    {
        return 'Optional.empty';
    }
}