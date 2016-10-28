<?php

namespace JimmyOak\Optional;

use JimmyOak\Optional\Exception\FlatMapCallbackMustReturnOptionalException;
use JimmyOak\Optional\Exception\NoSuchElementException;
use JimmyOak\Optional\Exception\NullPointerException;

abstract class Optional
{
    /**
     * Create empty optional with no value.
     *
     * @return Optional
     */
    public static function empty(): Optional
    {
        static $empty = null;
        if (null === $empty) {
            $empty = new None();
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
        return new Some($value);
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
        return null === $value ? self::empty() : self::of($value);
    }

    /**
     * Gets value if present
     *
     * @throws NoSuchElementException in case no value
     *
     * @return mixed
     */
    public abstract function get();

    /**
     * Returns true if optional has a value, otherwise, returns false
     *
     * @return bool
     */
    public abstract function isPresent() : bool;

    /**
     * Executes given $callback in case value is present
     *
     * @param callable $callback
     */
    public abstract function ifPresent(callable $callback);

    /**
     * Filters held value. Returns empty optional in case given $predicate returns false
     *
     * @param callable $predicate
     *
     * @return Optional
     * @throws NullPointerException
     */
    public abstract function filter(callable $predicate);

    /**
     * In case a value is present applies given $mapper callback.
     * Returns new optional if $mapper returns non null value
     *
     * @param callable $mapper
     *
     * @return Optional
     * @throws NullPointerException
     */
    public abstract function map(callable $mapper);

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
    public abstract function flatMap(callable $mapper);

    /**
     * If present returns held value, otherwise returns given $other value
     *
     * @param $other
     *
     * @return mixed
     */
    public abstract function orElse($other);

    /**
     * If present returns held value, otherwise returns
     * value returned from given $other callback
     *
     * @param callable $other
     *
     * @return mixed
     */
    public abstract function orElseGet(callable $other);

    /**
     * If present returns held value, othwerise throws given $exception
     *
     * @param \Exception $exception
     *
     * @return mixed
     * @throws \Exception
     */
    public abstract function orElseThrow(\Exception $exception);

    /**
     * Compares two optionals. Returns true in case value is the same.
     *
     * @param Optional $obj
     *
     * @return bool
     */
    public abstract function equals(Optional $obj): bool;

    public abstract function __toString();

    protected static function requireNonNull($value)
    {
        if (null === $value) {
            throw new NullPointerException();
        }

        return $value;
    }
}
