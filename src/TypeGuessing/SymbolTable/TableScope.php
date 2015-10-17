<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable;

class TableScope
{
    const GLOBAL_SCOPE = 'GLOBAL_SCOPE';
    const CLASS_LIKE_SCOPE = 'CLASS_LIKE_SCOPE';
    const CLASS_METHOD_SCOPE = 'CLASS_METHOD_SCOPE';
    const FUNCTION_SCOPE = 'FUNCTION_SCOPE';

    /**
     * @var string
     */
    protected $layerType;

    /**
     * @var Symbol[]
     */
    protected $symbols;

    /**
     * @param string $layerType
     */
    public function __construct($layerType)
    {
        $this->layerType = $layerType;
        $this->symbols = array();
    }

    /**
     * @param Symbol $symbol
     */
    public function setSymbol(Symbol $symbol)
    {
        $this->symbols[$symbol->symbol()] = $symbol;
    }

    /**
     * @param string $symbolName
     *
     * @return Symbol
     */
    public function findSymbol($symbolName)
    {
        /** @var $symbol Symbol */
        foreach ($this->symbols as $symbol) {
            if ($symbol->symbol() === $symbolName) {
                return $symbol;
            }
        }

        return;
    }

    /**
     * @return string
     */
    public function scope()
    {
        return $this->layerType;
    }
}
