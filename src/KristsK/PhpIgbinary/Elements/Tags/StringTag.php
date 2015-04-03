<?php

namespace KristsK\PhpIgbinary\Elements\Tags;

use KristsK\PhpIgbinary\Elements\Values\Scalar\StringValue;

/**
 * Class StringTag
 * @package KristsK\PhpIgbinary\Elements\Tags
 */
class StringTag extends AbstractTag {

    /**
     * @var StringValue
     */
    protected $stringValue;

    /**
     * @param int $id
     * @param StringValue $stringValue
     */
    public function __construct($id, $stringValue) {

        parent::__construct($id);

        $this->stringValue = $stringValue;
    }

    /**
     * @return StringValue
     */
    public function getStringValue() {

        return $this->stringValue;
    }

    /**
     * @return string
     */
    public function __toString() {

        return 'T:STR:' . parent::__toString();
    }
}