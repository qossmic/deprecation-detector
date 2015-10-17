<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable;

class SymbolTable
{
    /**
     * @var TableScope[]
     */
    protected $table;

    public function __construct()
    {
        $this->table = array(new TableScope(TableScope::GLOBAL_SCOPE));
    }

    /**
     * @param TableScope $scope
     */
    public function enterScope(TableScope $scope)
    {
        $this->table[] = $scope;
    }

    public function leaveScope()
    {
        array_pop($this->table);
    }

    /**
     * @param string $symbolString
     * @param string $type
     */
    public function setSymbol($symbolString, $type)
    {
        /* @TODO change the API to setSymbol(Symbol $symbol) */
        $this->table[count($this->table) - 1]->setSymbol(new Symbol($symbolString, $type));
    }

    /**
     * @return TableScope
     */
    public function currentScope()
    {
        return $this->table[count($this->table) - 1];
    }

    /**
     * @param string $symbolString
     *
     * @return Symbol
     */
    public function lookUp($symbolString)
    {
        $i = 0;
        $x = count($this->table) - 1;
        while (isset($this->table[$x - $i])) {
            $symbol = $this->table[$x - $i]->findSymbol($symbolString);
            if ($symbol instanceof Symbol) {
                return $symbol;
            }

            ++$i;
        }

        return new Symbol($symbolString, '');
    }

    /**
     * @param string $symbolString
     *
     * @return Symbol
     *
     * looks for the next class scope and searches for a given class property
     */
    public function lookUpClassProperty($symbolString)
    {
        $i = 0;
        $x = count($this->table) - 1;
        while (isset($this->table[$x - $i])) {
            if ($this->table[$x - $i]->scope() === TableScope::CLASS_LIKE_SCOPE) {
                $symbol = $this->table[$x - $i]->findSymbol($symbolString);
                if ($symbol instanceof Symbol) {
                    return $symbol;
                }

                return new Symbol($symbolString, '');
            }

            ++$i;
        }

        return new Symbol($symbolString, '');
    }

    /**
     * @param string $symbolString
     * @param string $type
     *
     * @throws \Exception
     */
    public function setClassProperty($symbolString, $type)
    {
        $i = 0;
        $x = count($this->table) - 1;

        while (isset($this->table[$x - $i])) {
            if ($this->table[$x - $i]->scope() === TableScope::CLASS_LIKE_SCOPE) {
                /* @TODO change the API to setClassProperty(Symbol $symbol) */
                $this->table[$x - $i]->setSymbol(new Symbol($symbolString, $type));

                return;
            }

            ++$i;
        }

        throw new \Exception('Illegal State there is no class scope above');
    }
}
