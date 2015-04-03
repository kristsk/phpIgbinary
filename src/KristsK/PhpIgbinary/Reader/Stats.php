<?php namespace KristsK\PhpIgbinary\Reader;

/**
 * Class Stats
 * @package KristsK\PhpIgbinary
 */
class Stats {

    /**
     * @var int
     */
    protected $longCount = 0;

    /**
     * @var int
     */
    protected $doubleCount = 0;

    /**
     * @var int
     */
    protected $stringCount = 0;

    /**
     * @var int
     */
    protected $arrayCount = 0;

    /**
     * @var int
     */
    protected $boolCount = 0;

    /**
     * @var int
     */
    protected $objectCount = 0;

    /**
     * @var int
     */
    protected $nullCount = 0;

    /**
     * @var int
     */
    protected $emptyStringCount = 0;

    /**
     *
     */
    public function reset() {

        $this->longCount = 0;
        $this->stringCount = 0;
        $this->nullCount = 0;
        $this->arrayCount = 0;
        $this->objectCount = 0;
        $this->boolCount = 0;
        $this->emptyStringCount = 0;
        $this->doubleCount = 0;
    }

    /**
     *
     */
    public function addLong() {

        $this->longCount++;
    }

    /**
     *
     */
    public function addBool() {

        $this->boolCount++;
    }

    /**
     *
     */
    public function addDouble() {

        $this->doubleCount++;
    }

    /**
     *
     */
    public function addString() {

        $this->stringCount++;
    }

    /**
     *
     */
    public function addArray() {

        $this->arrayCount++;
    }

    /**
     *
     */
    public function addObject() {

        $this->objectCount++;
    }

    /**
     *
     */
    public function addNull() {

        $this->nullCount++;
    }

    /**
     *
     */
    public function addEmptyString() {

        $this->emptyStringCount++;
    }

    /**
     * @return int
     */
    public function getLongCount() {

        return $this->longCount;
    }

    /**
     * @return int
     */
    public function getDoubleCount() {

        return $this->doubleCount;
    }

    /**
     * @return int
     */
    public function getStringCount() {

        return $this->stringCount;
    }

    /**
     * @return int
     */
    public function getArrayCount() {

        return $this->arrayCount;
    }

    /**
     * @return int
     */
    public function getBoolCount() {

        return $this->boolCount;
    }

    /**
     * @return int
     */
    public function getObjectCount() {

        return $this->objectCount;
    }

    /**
     * @return int
     */
    public function getNullCount() {

        return $this->nullCount;
    }

    /**
     * @return int
     */
    public function getEmptyStringCount() {

        return $this->emptyStringCount;
    }
}
