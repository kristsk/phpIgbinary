<?php

namespace KristsK\PhpIgbinary\Reader;

use KristsK\PhpIgbinary\Elements\Tags\StringTag;
use KristsK\PhpIgbinary\Elements\Values\Scalar\StringValue;
use KristsK\PhpIgbinary\Exception;

/**
 * Class Strings
 * @package KristsK\PhpIgbinary\Reader
 */
class Strings {

    /**
     * @var StringTag[]
     */
    protected $tags = array();

    /**
     * @param StringValue|string $stringValue
     * @return StringTag
     */
    public function add($stringValue) {

        if (!$stringValue instanceof StringValue) {
            $stringValue = new StringValue($stringValue);
        }

        $tag = new StringTag(count($this->tags), $stringValue);

        $this->tags[] = $tag;

        return $tag;
    }

    /**
     * @param int $id
     * @return StringTag
     */
    public function getTag($id) {

        if (!isset($this->tags[$id])) {
            throw new Exception('String tag "' . $id . '" not found');
        }

        return $this->tags[$id];
    }

    /**
     * @return StringTag[]
     */
    public function getTags() {

        return $this->tags;
    }
}
