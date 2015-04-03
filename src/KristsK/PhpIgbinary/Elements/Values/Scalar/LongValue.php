<?php

namespace KristsK\PhpIgbinary\Elements\Values\Scalar;

/**
 * Class LongValue
 * @package KristsK\PhpIgbinary\Elements\Values\Scalar
 */
class LongValue extends AbstractNumericValue {

    /**
     * @param int $value
     */
    public function __construct($value) {

        parent::__construct($value);
    }

    /**
     * @return string
     */
    public function __toString() {

        return 'S:L:' . $this->value;
    }
}
