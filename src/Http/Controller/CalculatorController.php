<?php

namespace Jakmall\Recruitment\Calculator\Http\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;

class CalculatorController
{
    private $history;
    private $actions;
    private $operators;

    public function __construct(CommandHistoryManagerInterface $history)
    {
        $this->history = $history;
        $this->actions = ['add', 'subtract', 'multiply', 'divide', 'power'];
        $this->operators = [
            $this->actions[0] => '+',
            $this->actions[1] => '-',
            $this->actions[2] => '*',
            $this->actions[3] => '/',
            $this->actions[4] => '^'
        ];
    }

    public function calculate(Request $request, $action)
    {
        if(!in_array($action, $this->actions)) {
            return JsonResponse::create([
                'message' => 'Action is not valid'
            ], 400);
        }

        $inputs = $request->input('input');
        if(!is_array($inputs)) {
            return JsonResponse::create([
                'message' => 'Input is not valid'
            ], 400);
        }

        $operation = $this->generateCalculationDescription($action, $inputs);
        $result = $this->calculateAll($action, $inputs);

        // Save History Log
        $this->history->log([
            'command' => $action,
            'description' => $operation,
            'result' => $result
        ]);

        return JsonResponse::create([
            'command' => $action,
            'operation' => $operation,
            'input' => $inputs,
            'result' => $result
        ], 200);
    }

    /**
     * @param string $action
     * @param array $inputs
     *
     * @return string
     */
    protected function generateCalculationDescription($action, $inputs): string
    {
        $glue = sprintf(' %s ', $this->operators[$action]);
        return implode($glue, $inputs);
    }

    /**
     * @param string $action
     * @param array $inputs
     *
     * @return float|int
     */
    protected function calculateAll($action, $inputs)
    {
        $number = array_pop($inputs);
        if (empty($inputs)) {
            return $number;
        }
        return $this->calculateAction($this->calculateAll($action, $inputs), $number, $action);
    }

    /**
     * @param int|float $number1
     * @param int|float $number2
     * @param string $action
     *
     * @return int|float
     */
    protected function calculateAction($number1, $number2, $action)
    {
        $result = 0;
        switch ($action) {
            case 'add':
                $result = $number1 + $number2;
                break;
            case 'subtract':
                $result = $number1 - $number2;
                break;
            case 'multiply':
                $result = $number1 * $number2;
                break;
            case 'divide':
                $result = $number1 / $number2;
                break;
            case 'pow':
                $result = pow($number1, $number2);
                break;
        }
        return $result;
    }
}
