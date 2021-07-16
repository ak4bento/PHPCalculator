<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;

class BaseCommand extends Command
{
    /**
     * @var string
     */
    protected $signature;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $commandPassive;

    /**
     * @var int
     */
    protected $argument;

    /**
     * @var string
     */
    protected $operator;

    public function __construct()
    {
        $commandVerb = $this->command;
        
        $this->getSignature($commandVerb);
        
        $this->getDescriptions();
        
        parent::__construct();
    }

    public function handle(CommandHistoryManagerInterface $history): void
    {
        $lastId = end($history->findAll('mesinhitung'));
        $numbers = $this->getInput();
        $bool = ($this->argument != null) && (count($numbers) > $this->argument);

        if ($this->operator != '^') {
            $result = $this->calculateAll($numbers);
        } else {
            $result = $this->calculate($numbers[0],$numbers[1]);
            $numbers = array_splice($numbers, 0, 2);
        }

        $description = $this->generateCalculationDescription($numbers);

        if ($bool) {
            $this->comment(sprintf('Too many argument, accepts only two arguments as its input. Example %s = %s', $description, $result));
        } else {
            $this->comment(sprintf('%s = %s', $description, $result));
            $id = $lastId['id'] + 1 ?? 0;
            $json = json_encode(
                array(
                    'id' => $id,
                    'command' => $this->command,
                    'operation' => $description,
                    'result' => $result,
                )
            );
            $history->saveToStorage($json, 'mesinhitung');
            $history->saveToStorage($json, 'latest');
        }

        $data = $history->findAll('latest');
        
        if (count($data) > 10) {
            unset($data[0]);
            $history->clearAll('latest.log', 'clean');
            foreach ($data as $value) {
                $history->saveToStorage(json_encode($value), 'latest');
            }
        }
        
        parent::handle();
    }

    /**
     * 
     * 
     * 
     */
    protected function getSignature($commandVerb) : void
    {
        $this->signature = sprintf(
            '%s {numbers* : The numbers to be %s}',
            $commandVerb,
            $this->commandPassive
        );
    }

    protected function getDescriptions() : void
    {
        $this->description = sprintf('%s all given Numbers', ucfirst($this->command));
    }

    protected function getConditonArgument()
    {
        # code...
    }

    protected function getInput(): array
    {
        return $this->argument('numbers');
    }

    protected function generateCalculationDescription(array $numbers): string
    {
        $glue = sprintf(' %s ', $this->operator);

        return implode($glue, $numbers);
    }

    /**
     * @param array $numbers
     *
     * @return float|int
     */
    protected function calculateAll(array $numbers)
    {
        $number = array_pop($numbers);

        if (count($numbers) <= 0) {
            return $number;
        }

        return $this->calculate($this->calculateAll($numbers), $number);
    }
}
