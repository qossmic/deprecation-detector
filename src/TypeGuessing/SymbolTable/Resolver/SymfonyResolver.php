<?php

namespace SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver;

use PhpParser\Node;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;
use SensioLabs\DeprecationDetector\TypeGuessing\Symfony\ContainerReader;

class SymfonyResolver implements ResolverInterface
{
    const CONTAINER = 'Symfony\Component\DependencyInjection\ContainerInterface';

    /**
     * @var SymbolTable
     */
    protected $table;

    /**
     * @var ContainerReader
     */
    protected $container;

    /**
     * @param SymbolTable     $table
     * @param ContainerReader $container
     */
    public function __construct(SymbolTable $table, ContainerReader $container)
    {
        $this->table = $table;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function resolveVariableType(Node $node)
    {
        // $this->call()
        if ($node instanceof Node\Expr\MethodCall
            && property_exists($node->var, 'name')
            && null === $node->getAttribute('guessedType', null)
        ) {
            if ('container' == $node->var->name && $this->isController($this->table->lookUp('this')->type())) {
                $context = self::CONTAINER;
            } else {
                $context = $this->table->lookUp($node->var->name)->type();
            }
            $type = $this->getType($context, $node->name, $node);

            if (null !== $type) {
                $node->setAttribute('guessedType', $type);
            }
        }

        // $x = $this->call()
        if ($node instanceof Node\Expr\Assign
            && $node->var instanceof Node\Expr\Variable
            && $node->expr instanceof Node\Expr\MethodCall
            && property_exists($node->expr->var, 'name')
            && null === $node->getAttribute('guessedType', null)
        ) {
            if ('container' == $node->expr->var->name && $this->isController($this->table->lookUp('this')->type())) {
                $context = self::CONTAINER;
            } else {
                $context = $this->table->lookUp($node->expr->var->name)->type();
            }
            $type = $this->getType($context, $node->expr->name, $node->expr);

            if (null !== $type) {
                $node->var->setAttribute('guessedType', $type);
                $this->table->setSymbol($node->var->name, $type);
            }
        }
    }

    /**
     * @param $context
     * @param $methodName
     * @param Node|null $node
     *
     * @return string|null
     */
    protected function getType($context, $methodName, Node $node = null)
    {
        if ($this->isController($context)) {
            switch ($methodName) {
                case 'getDoctrine':
                    return 'Doctrine\Bundle\DoctrineBundle\Registry';
                case 'createForm':
                    return 'Symfony\Component\Form\Form';
                case 'createFormBuilder':
                    return 'Symfony\Component\Form\FormBuilder';
            }
        }

        if ('get' === $methodName && ($this->isController($context) || self::CONTAINER == $context)) {
            if ($node instanceof Node && isset($node->var)
                && ($node->var->name == 'this' || $node->var->name == 'container')
            ) {
                if ($node->args[0]->value instanceof Node\Scalar\String_) {
                    $serviceId = $node->args[0]->value->value;
                } elseif ($node->args[0]->value instanceof Node\Expr\Variable) {
                    // TODO: resolve variables
                }

                if (isset($serviceId) && $this->container->has($serviceId)) {
                    return $this->container->get($serviceId);
                }
            }

            return;
        }

        return;
    }

    /**
     * @param $type
     *
     * @return bool
     */
    protected function isController($type)
    {
        return (substr($type, -10) === 'Controller');
    }
}
