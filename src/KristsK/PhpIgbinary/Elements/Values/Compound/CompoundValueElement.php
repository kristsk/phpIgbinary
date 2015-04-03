<?php

namespace KristsK\PhpIgbinary\Elements\Values\Compound;

use KristsK\PhpIgbinary\Elements\AbstractElement;
use KristsK\PhpIgbinary\Elements\Values\Scalar\AbstractScalarValue;

/**
 * Class CompoundValueElement
 * @package KristsK\PhpIgbinary\Elements\Values\Compound
 */
class CompoundValueElement {

    /**
     * @var AbstractScalarValue
     */
    protected $key;

    /**
     * @var AbstractElement
     */
    protected $value;

    /**
     * @param AbstractScalarValue $key
     * @param AbstractElement $value
     */
    public function __construct($key, $value) {

        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return AbstractScalarValue
     */
    public function getKey() {

        return $this->key;
    }

    /**
     * @return AbstractElement
     */
    public function getValue() {

        return $this->value;
    }

    /**
     * @param AbstractScalarValue $key
     */
    public function setKey(AbstractScalarValue $key) {

        $this->key = $key;
    }

    /**
     * @param AbstractElement $value
     */
    public function setValue(AbstractElement $value) {

        $this->value = $value;
    }
}
