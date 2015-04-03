<?php

namespace KristsK\PhpIgbinary\Elements\Values\Scalar;

/**
 * Class NullValue
 * @package KristsK\PhpIgbinary\Values
 */
class NullValue extends AbstractScalarValue {

    /**
     *
     */
    public function __construct() {

        parent::__construct(null);
    }

    /**
     * @return string
     */
    public function __toString() {

        return 'S:NULL';
    }
}
