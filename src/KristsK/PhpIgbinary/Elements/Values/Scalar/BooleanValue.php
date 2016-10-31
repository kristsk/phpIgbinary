<?php

namespace KristsK\PhpIgbinary\Elements\Values\Scalar;

/**
 * Class BooleanValue
 * @package KristsK\PhpIgbinary\Values
 */
class BooleanValue extends AbstractScalarValue {

    /**
     * @param bool $value
     */
    public function __construct($value) {

        parent::__construct($value);
    }

    /**
     * @return string
     */
    public function __toString() {

        return 'S:B:' . ($this->value ? 'TRUE' : 'FALSE');
    }
}
