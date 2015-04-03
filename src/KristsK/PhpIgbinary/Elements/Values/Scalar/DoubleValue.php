<?php

namespace KristsK\PhpIgbinary\Elements\Values\Scalar;

/**
 * Class DoubleValue
 * @package KristsK\PhpIgbinary\Elements\Values\Scalar
 */
class DoubleValue extends AbstractNumericValue {

    /**
     * @param double $value
     */
    public function __construct($value) {

        parent::__construct($value);
    }

    /**
     * @return string
     */
    public function __toString() {

        return 'S:D:' . $this->value;
    }
}
