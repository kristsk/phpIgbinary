<?php

namespace KristsK\PhpIgbinary\Elements\Tags;

use KristsK\PhpIgbinary\Elements\Values\AbstractValue;

/**
 * Class ReferenceTag
 * @package KristsK\PhpIgbinary\Elements\Tags
 */
class ReferenceTag extends AbstractTag {

    /**
     * @var AbstractValue
     */
    protected $value;

    /**
     * @param int $id
     * @param AbstractValue $value
     */
    public function __construct($id, AbstractValue $value) {

        parent::__construct($id);

        $this->value = $value;
    }

    /**
     * @return AbstractValue
     */
    public function getValue() {

        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString() {

        return 'T:REF:' . parent::__toString() . ':' . $this->getUseCount();
    }
}
