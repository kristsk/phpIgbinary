<?php

namespace KristsK\PhpIgbinary\Elements\Values\Compound;

use KristsK\PhpIgbinary\Elements\AbstractElement;
use KristsK\PhpIgbinary\Elements\Values\AbstractValue;
use KristsK\PhpIgbinary\Elements\Values\Scalar\AbstractScalarValue;

/**
 * Class AbstractCompoundValue
 * @package KristsK\PhpIgbinary\Elements\Values\Compound
 */
abstract class AbstractCompoundValue extends AbstractValue {

    /**
     * @var CompoundValueElement[]
     */
    protected $values;

    /**
     * @return CompoundValueElement[]
     */
    public function getValues() {

        return $this->values;
    }

    /**
     * @param AbstractScalarValue $key
     * @param AbstractElement $value
     * @return CompoundValueElement
     */
    public function add(AbstractScalarValue $key, AbstractElement $value) {

        $compoundValue = new CompoundValueElement($key, $value);

        $this->values[$key->getPhpValue()] = $compoundValue;

        return $compoundValue;
    }
}