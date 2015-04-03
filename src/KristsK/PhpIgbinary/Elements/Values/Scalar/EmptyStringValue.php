<?php

namespace KristsK\PhpIgbinary\Elements\Values\Scalar;

/**
 * Class EmptyStringValue
 * @package KristsK\PhpIgbinary\Values
 */
class EmptyStringValue extends StringValue {

    /**
     *
     */
    public function __construct() {

        parent::__construct('');
    }

    /**
     * @return string
     */
    public function __toString() {

        return 'S:ES';
    }
}
