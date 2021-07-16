<?php

namespace Jakmall\Recruitment\Calculator\Commands;

//use Jakmall\Recruitment\Calculator\Commands\HasCommand;

class AddCommand extends BaseCommand
{
    //use HasCommand;

    /**
     * @var string
     */
    protected $command = 'add';

    /**
     * @var string
     */
    protected $commandPassive = 'added';

    /**
     * @var string
     */
    protected $operator = '+';

    /**
     * using null if calculate all argument 
     * 
     * @var int
     */
    protected $argument = null;

    /**
     * @param int|float $number1
     * @param int|float $number2
     *
     * @return int|float
     */
    protected function calculate($number1, $number2)
    {
        return $number1 + $number2;
    }
}
