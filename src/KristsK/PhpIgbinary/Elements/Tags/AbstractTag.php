<?php

namespace KristsK\PhpIgbinary\Elements\Tags;

use KristsK\PhpIgbinary\Elements\AbstractElement;

/**
 * Class AbstractTag
 * @package KristsK\PhpIgbinary\Tags
 */
abstract class AbstractTag extends AbstractElement {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $useCount = 1;

    /**
     * @param int $id
     */
    public function __construct($id) {

        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId() {

        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString() {

        return '[' . $this->getId() . ']';
    }

    /**
     *
     */
    public function increaseUseCount() {

        $this->useCount++;
    }

    /**
     * @return int
     */
    public function getUseCount() {

        return $this->useCount;
    }
}