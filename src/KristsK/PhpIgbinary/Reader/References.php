<?php namespace KristsK\PhpIgbinary\Reader;

use KristsK\PhpIgbinary\Elements\Tags\ReferenceTag;
use KristsK\PhpIgbinary\Elements\Values\AbstractValue;
use KristsK\PhpIgbinary\Exception;

/**
 * Class References
 * @package KristsK\PhpIgbinary\Reader
 */
class References {

    /**
     * @var ReferenceTag[]
     */
    protected $tags = array();

    /**
     * @param AbstractValue $element
     * @return ReferenceTag
     */
    public function add(AbstractValue $element) {

        $tag = new ReferenceTag(count($this->tags), $element);

        $this->tags[] = $tag;

        return $tag;
    }

    /**
     * @param int $id
     * @return ReferenceTag
     */
    public function getTag($id) {

        if (!isset($this->tags[$id])) {
            throw new Exception('Reference tag "' . $id . '" not found');
        }

        return $this->tags[$id];
    }

    /**
     * @return ReferenceTag[]
     */
    public function getTags() {

        return $this->tags;
    }
}
