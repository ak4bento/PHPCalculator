<?php

namespace Jakmall\Recruitment\Calculator\Commands;

class PowerCommand extends BaseCommand
{
    //use HasCommand;

    /**
     * @var string
     */
    protected $command = 'power';

    /**
     * @var string
     */
    protected $commandPassive = 'exponent';

    /**
     * @var string
     */
    protected $operator = '^';

    /**
     * using null if calculate all argument 
     * 
     * @var int
     */
    protected $argument = 2;
    
    /**
     * @param int|float $number1
     * @param int|float $number2
     *
     * @return int|float
     */
    protected function calculate($number1, $number2)
    {
        return pow($number1, $number2);
    }

    protected function getDescriptions() : void
    {
        parent::getDescriptions();
        $this->description = sprintf('%s calculate the exponent of the given numbers', ucfirst($this->command));
    }
}
