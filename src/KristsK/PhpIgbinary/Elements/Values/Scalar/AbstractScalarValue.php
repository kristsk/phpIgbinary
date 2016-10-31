<?php

namespace KristsK\PhpIgbinary\Elements\Values\Scalar;

use KristsK\PhpIgbinary\Elements\Values\AbstractValue;

/**
 * Class AbstractScalarValue
 * @package KristsK\PhpIgbinary\Values
 */
abstract class AbstractScalarValue extends AbstractValue {

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param mixed $value
     */
    public function __construct($value) {

        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getPhpValue() {

        return $this->value;
    }

    /**
     * @return string
     */
    abstract public function __toString();
}
