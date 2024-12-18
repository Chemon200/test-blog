<?php
namespace App\Helper;

use PhpParser\Node\Expr\FuncCall;

class FizzBuzz
{
    private const FIZZ_NUMBER = 3;
    private const BUZZ_NUMBER = 5;
    private const FIZZ_BUZZ_NUMBER = 15;

    public function fizzBuzz($number)
    {
        if (0 === $number % self::FIZZ_BUZZ_NUMBER) {
            return 'FizzBuzz';
        }

        if (0 === $number % self::FIZZ_NUMBER) {
            return 'Fizz';
        }

        if (0 === $number % self::BUZZ_NUMBER) {
            return 'Buzz';
        }

        return $number;
    }
}