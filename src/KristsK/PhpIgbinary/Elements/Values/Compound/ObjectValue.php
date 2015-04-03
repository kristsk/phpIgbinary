<?php

namespace KristsK\PhpIgbinary\Elements\Values\Compound;

/**
 * Class ObjectValue
 * @package KristsK\PhpIgbinary\Elements\Values\Compound
 */
class ObjectValue extends AbstractCompoundValue {

    /**
     * @var string
     */
    protected $className;

    /**
     * @param string $className
     */
    public function setClassName($className) {

        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getClassName() {

        return $this->className;
    }

    /**
     * @param ArrayValue $arrayValue
     */
    public function setValuesFromArrayValue(ArrayValue $arrayValue) {

        $this->values = $arrayValue->getValues();
    }

    /**
     * @return string
     */
    public function __toString() {

        return 'C:O:' . $this->getClassName();
    }
}
