<?php

namespace KristsK\PhpIgbinary\Elements\Values\Scalar;

/**
 * Class StringValue
 * @package KristsK\PhpIgbinary\Values
 */
class StringValue extends AbstractScalarValue {

    /**
     * @param string $value
     */
    public function __construct($value) {

        parent::__construct($value);
    }

    /**
     * @return string
     */
    public function __toString() {

        return 'S:S:' . $this->value;
    }
}
