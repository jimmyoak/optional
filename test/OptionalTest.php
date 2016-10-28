<?php

namespace JimmyOak\Test\Optional;

use JimmyOak\Optional\Exception\FlatMapCallbackMustReturnOptionalException;
use JimmyOak\Optional\Exception\NoSuchElementException;
use JimmyOak\Optional\Exception\NullPointerException;
use JimmyOak\Optional\Optional;

class OptionalTest extends \PHPUnit_Framework_TestCase
{
    const VALUE = "something";
    const NO_VALUE = null;
    const VALUE_LENGTH = 9;
    const ANOTHER_VALUE = "such.wow";
    const OPTIONAL_WITH_VALUE_STRING_REPRESENTATION = 'Optional[' . self::VALUE . ']';
    const EMPTY_OPTIONAL_STRING_REPRESENTATION = 'Optional.empty';

    /**
     * @test
     */
    public function should_create_with_specified_value_or_throw_exception_in_case_of_null()
    {
        $optional = Optional::of(self::VALUE);
        $this->assertSame(self::VALUE, $optional->get());

        $this->expectException(NullPointerException::class);
        Optional::of(self::NO_VALUE);
    }

    /**
     * @test
     */
    public function should_create_with_specified_value_or_empty_in_case_of_null()
    {
        $optional = Optional::ofNullable(self::VALUE);
        $this->assertSame(self::VALUE, $optional->get());

        $optional = Optional::ofNullable(self::NO_VALUE);
        $this->assertFalse($optional->isPresent());
    }

    /**
     * @test
     */
    public function should_create_empty_with_no_value()
    {
        $optional = Optional::empty();

        $this->assertFalse($optional->isPresent());
    }

    /**
     * @test
     */
    public function should_get_value_from_optional_with_value()
    {
        $optional = Optional::of(self::VALUE);
        $this->assertSame(self::VALUE, $optional->get());
    }

    /**
     * @test
     */
    public function should_throw_exception_when_getting_value_from_empty_optional()
    {
        $optional = Optional::empty();
        $this->expectException(NoSuchElementException::class);
        $optional->get();
    }

    /**
     * @test
     */
    public function should_throw_exception_when_getting_value_from_empty_optional_craeted_with_no_value()
    {
        $optional = Optional::ofNullable(self::NO_VALUE);
        $this->expectException(NoSuchElementException::class);
        $optional->get();
    }

    /**
     * @test
     */
    public function should_execute_callback_if_value_is_present()
    {
        $optional = Optional::of(self::VALUE);
        $pass = false;
        $optional->ifPresent(function ($value) use (&$pass) {
            $this->assertSame(self::VALUE, $value);
            $pass = true;
        });

        $this->assertTrue($pass);
    }

    /**
     * @test
     */
    public function should_not_execute_callback_if_no_value_is_present()
    {
        $optional = Optional::empty();
        $pass = false;
        $optional->ifPresent(function ($value) use (&$pass) {
            $pass = true;
        });

        $this->assertFalse($pass);
    }

    /**
     * @test
     */
    public function should_execute_if_value_is_present_on_ifPresentOrElse()
    {
        $optional = Optional::of(self::VALUE);
        $passIfPresent = false;
        $passOrElse = false;
        $optional->ifPresentOrElse(function ($value) use (&$passIfPresent) {
            $this->assertSame(self::VALUE, $value);
            $passIfPresent = true;
        }, function () use (&$passOrElse) {
            $passOrElse = true;
        });

        $this->assertTrue($passIfPresent);
        $this->assertFalse($passOrElse);
    }

    /**
     * @test
     */
    public function should_execute_orElse_if_no_value_present_on_ifPresentOrElse()
    {
        $optional = Optional::empty();
        $passIfPresent = false;
        $passOrElse = false;
        $optional->ifPresentOrElse(function ($value) use (&$passIfPresent) {
            $passIfPresent = true;
        }, function () use (&$passOrElse) {
            $passOrElse = true;
        });

        $this->assertFalse($passIfPresent);
        $this->assertTrue($passOrElse);
    }

    /**
     * @test
     */
    public function should_return_optional_helding_value_in_case_filter_callback_returns_true()
    {
        $valueIsString = function ($value) {
            return is_string($value);
        };

        $optional = Optional::of(self::VALUE)->filter($valueIsString);

        $this->assertTrue($optional->isPresent());
    }

    /**
     * @test
     */
    public function should_return_empty_optional_in_case_filter_callback_returns_false()
    {
        $valueIsInteger = function ($value) {
            return is_int($value);
        };
        $optional = Optional::of(self::VALUE)->filter($valueIsInteger);

        $this->assertFalse($optional->isPresent());
    }

    /**
     * @test
     */
    public function should_map_value_in_case_it_is_present()
    {
        $optional = Optional::of(self::VALUE)->map(function ($value) {
            return strlen($value);
        });

        $this->assertSame(self::VALUE_LENGTH, $optional->get());
    }

    /**
     * @test
     */
    public function should_return_empty_optional_in_case_map_returns_null()
    {
        $optional = Optional::of(self::VALUE)->map(function ($value) {
            return null;
        });

        $this->assertFalse($optional->isPresent());
    }

    /**
     * @test
     */
    public function should_do_nothing_in_case_of_mapping_empty_optional()
    {
        $pass = false;
        Optional::empty()->map(function ($value) use (&$pass) {
            $pass = true;

            return null;
        });

        $this->assertFalse($pass);
    }

    /**
     * @test
     */
    public function should_flat_map_if_value_is_present()
    {
        $optional = Optional::of(self::VALUE)->flatMap(function ($value) {
            return Optional::of(strlen($value));
        });

        $this->assertSame(self::VALUE_LENGTH, $optional->get());
    }

    /**
     * @test
     */
    public function should_not_execute_flat_map_callback_in_case_of_empty_optional_and_return_empty_optional()
    {
        $pass = false;
        $optional = Optional::empty()->flatMap(function ($value) use (&$pass) {
            $pass = true;
            return Optional::of(strlen($value));
        });

        $this->assertFalse($pass);
        $this->assertFalse($optional->isPresent());
    }

    /**
     * @test
     */
    public function should_throw_exception_in_case_flat_map_returns_no_optional_value()
    {
        $this->expectException(FlatMapCallbackMustReturnOptionalException::class);
        Optional::of(self::VALUE)->flatMap(function ($value) {
            return $value;
        });
    }

    /**
     * @test
     */
    public function should_return_first_optional_if_value_is_present()
    {
        $anotherOptionalValue = function () {
            return Optional::of(self::ANOTHER_VALUE);
        };

        $optional = Optional::of(self::VALUE)
            ->or($anotherOptionalValue);

        $this->assertSame(self::VALUE, $optional->get());
    }

    /**
     * @test
     */
    public function should_return_or_optional_if_value_is_not_present_in_first_optional()
    {
        $someOptionalWithValue = function () {
            return Optional::of(self::VALUE);
        };

        $optional = Optional::empty()
            ->or($someOptionalWithValue);

        $this->assertSame(self::VALUE, $optional->get());
    }

    /**
     * @test
     */
    public function should_return_empty_optional_if_value_is_not_present_in_any_or()
    {
        $someOptionalWithNoValue = function () {
            return Optional::ofNullable(self::NO_VALUE);
        };

        $optional = Optional::empty()
            ->or($someOptionalWithNoValue);

        $this->assertTrue(Optional::empty()->equals($optional));
    }

    /**
     * @test
     */
    public function should_get_value_if_present_or_else_specified_one()
    {
        $presentValue = Optional::of(self::VALUE)->orElse(self::ANOTHER_VALUE);
        $noPresentValue = Optional::empty()->orElse(self::ANOTHER_VALUE);

        $this->assertSame(self::VALUE, $presentValue);
        $this->assertSame(self::ANOTHER_VALUE, $noPresentValue);
    }

    /**
     * @test
     */
    public function should_get_value_if_present_or_else_return_callback_one()
    {
        $anotherLazyValue = function () {
            return self::ANOTHER_VALUE;
        };

        $presentValue = Optional::of(self::VALUE)->orElseGet($anotherLazyValue);
        $noPresentValue = Optional::empty()->orElseGet($anotherLazyValue);

        $this->assertSame(self::VALUE, $presentValue);
        $this->assertSame(self::ANOTHER_VALUE, $noPresentValue);
    }

    /**
     * @test
     */
    public function should_get_value_if_present_or_throw_exception()
    {
        $exception = new \Exception();

        $presentValue = Optional::of(self::VALUE)->orElseThrow($exception);
        $this->assertSame(self::VALUE, $presentValue);

        $this->expectException(get_class($exception));
        Optional::empty()->orElseThrow($exception);
    }

    /**
     * @test
     */
    public function should_equal_on_same_held_value_optionals()
    {
        $optionalWithValue = Optional::of(self::VALUE);
        $optionalWithSameValue = Optional::of(self::VALUE);
        $optionalWithAnotherValue = Optional::of(self::ANOTHER_VALUE);
        $emptyOptional = Optional::empty();
        $anotherEmptyOptional = Optional::empty();

        $this->assertTrue($optionalWithValue->equals($optionalWithSameValue));
        $this->assertTrue($optionalWithValue->equals($optionalWithValue));
        $this->assertFalse($optionalWithValue->equals($optionalWithAnotherValue));
        $this->assertFalse($optionalWithValue->equals($emptyOptional));
        $this->asserTtrue($emptyOptional->equals($anotherEmptyOptional));
    }

    /**
     * @test
     */
    public function should_stringify()
    {
        $this->assertSame(self::OPTIONAL_WITH_VALUE_STRING_REPRESENTATION, (string)Optional::of(self::VALUE));
        $this->assertSame(self::EMPTY_OPTIONAL_STRING_REPRESENTATION, (string)Optional::empty());
    }
}
