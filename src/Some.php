<?php

namespace JimmyOak\Optional;

use JimmyOak\Optional\Exception\BadMethodCallException;
use JimmyOak\Optional\Exception\FlatMapCallbackMustReturnOptionalException;
use JimmyOak\Optional\Exception\NoSuchElementException;
use JimmyOak\Optional\Exception\NullPointerException;

final class Some extends Optional
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
     * Value holder. Null in case of no value.
     * @var mixed
     */
    private $value;

    public function __construct($value)
    {
        self::requireNonNull($value);
        $this->value = $value;
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
     * If a value is present, performs the given action with the value,
     * otherwise performs the given empty-based action
     *
     * @param callable $ifPresent
     * @param callable $orElse
     */
    public function ifPresentOrElse(callable $ifPresent, callable $orElse)
    {
        $ifPresent($this->value);
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
        self::requireNonNull($mapper);
        return parent::ofNullable($mapper($this->value));
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
        return $this->requireOptional($mapper($this->value));
    }

    /**
     * If a value is present, returns the Optional describing the value,
     * otherwise returns an Optional produced by the supplying function.
     *
     * @param callable $supplier
     *
     * @return Optional
     */
    public function or (callable $supplier)
    {
        return $this;
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
        return $this->value;
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
        return $this->value;
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