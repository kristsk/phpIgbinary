<?php

namespace KristsK\PhpIgbinary\Elements\Values\Compound;

/**
 * Class ArrayValue
 * @package KristsK\PhpIgbinary\Elements\Values\Compound
 */
class ArrayValue extends AbstractCompoundValue {

    /**
     * @return string
     */
    public function __toString() {

        return 'C:A:' . count($this->values);
    }
}
