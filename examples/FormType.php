<?php

namespace {

    /**
     * @deprecated since version 2.6, to be removed in 3.0. Use {@link OptionsResolver} instead.
     */
    interface OptionsResolverInterface
    {
    }
}

namespace ExampleForm {

    interface FormTypeInterface
    {
        /**
         * Sets the default options for this type.
         *
         * @param \OptionsResolverInterface $resolver The resolver for the options.
         *
         * @deprecated since version 2.7, to be renamed in 3.0.
         *             Use the method configureOptions instead. This method will be
         *             added to the FormTypeInterface with Symfony 3.0.
         */
        public function setDefaultOptions(\OptionsResolverInterface $resolver);
    }

    abstract class AbstractType implements FormTypeInterface
    {
        /**
         * {@inheritdoc}
         */
        public function setDefaultOptions(\OptionsResolverInterface $resolver)
        {
            if (!$resolver instanceof \OptionsResolver) {
                throw new \InvalidArgumentException(sprintf('Custom resolver "%s" must extend "Symfony\Component\OptionsResolver\OptionsResolver".', get_class($resolver)));
            }

            $this->configureOptions($resolver);
        }
    }
}

namespace SomeType {

    use ExampleForm\AbstractType;

    class CustomFormType extends AbstractType
    {
        public function setDefaultOptions(\OptionsResolverInterface $resolver)
        {
            // DO ANYTHING

            parent::setDefaultOptions($resolver);
        }
    }

}
