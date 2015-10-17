<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable;

class Symbol
{
    /**
     * @var string
     */
    protected $symbol;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $symbol
     * @param string $type
     */
    public function __construct($symbol, $type)
    {
        $this->symbol = $symbol;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function symbol()
    {
        return $this->symbol;
    }

    /**
     * @return string
     */
    public function type()
    {
        return $this->type;
    }
}
