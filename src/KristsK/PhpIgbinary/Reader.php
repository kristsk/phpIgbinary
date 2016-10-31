<?php

namespace KristsK\PhpIgbinary;

use KristsK\PhpIgbinary\Elements\AbstractElement;
use KristsK\PhpIgbinary\Elements\Tags\ReferenceTag;
use KristsK\PhpIgbinary\Elements\Tags\StringTag;
use KristsK\PhpIgbinary\Elements\Values\AbstractValue;
use KristsK\PhpIgbinary\Elements\Values\Scalar\AbstractScalarValue;
use KristsK\PhpIgbinary\Elements\Values\Scalar\BooleanValue;
use KristsK\PhpIgbinary\Elements\Values\Scalar\DoubleValue;
use KristsK\PhpIgbinary\Elements\Values\Scalar\EmptyStringValue;
use KristsK\PhpIgbinary\Elements\Values\Scalar\LongValue;
use KristsK\PhpIgbinary\Elements\Values\Scalar\NullValue;
use KristsK\PhpIgbinary\Elements\Values\Scalar\StringValue;
use KristsK\PhpIgbinary\Elements\Values\Compound\ArrayValue;
use KristsK\PhpIgbinary\Elements\Values\Compound\ObjectValue;
use KristsK\PhpIgbinary\Reader\References;
use KristsK\PhpIgbinary\Reader\Stats;
use KristsK\PhpIgbinary\Reader\Strings;

/**
 * Class Reader
 * @package KristsK\PhpIgbinary
 */
class Reader {

    /**
     * @var array
     */
    protected $packed;

    /**
     * @var int
     */
    protected $packedVersion;

    /**
     * @var bool
     */
    protected $switchByteOrder;

    /**
     * @var string
     */
    protected $rootElement;

    /**
     * @var References
     */
    protected $references;

    /**
     * @var Strings
     */
    protected $strings;

    /**
     * @var Stats
     */
    protected $stats;

    /**
     * @param string $packed
     */
    public function __construct($packed) {

        $this->packed = $packed;

        $this->strings = new Strings();
        $this->references = new References();
        $this->stats = new Stats();

        $this->switchByteOrder = $this->isPlatformLittleEndian();

        $this->unpackHeader();

        $this->rootElement = $this->unpackElement();
    }

    /**
     * @return AbstractElement
     */
    public function getRootElement() {

        return $this->rootElement;
    }

    /**
     * @return References
     */
    public function getReferences() {

        return $this->references;
    }

    /**
     * @return Strings
     */
    public function getStrings() {

        return $this->strings;
    }

    /**
     * @return Stats
     */
    public function getStats() {

        return $this->stats;
    }

    /**
     * @param string $format
     * @return array
     */
    protected function unpackAndTake($format) {

        $unpacked = unpack($format, $this->packed);

        $paramsForRepack = $unpacked;
        array_unshift($paramsForRepack, $format);
        $repacked = call_user_func_array('pack', $paramsForRepack);
        $unpackedAsChars = unpack('C*', $repacked);
        $this->packed = substr($this->packed, count($unpackedAsChars));

        $unpacked = array_values($unpacked);

        return $unpacked;
    }

    /**
     *
     */
    protected function unpackHeader() {

        $this->packedVersion = $this->unpackLong32();
    }

    /**
     * @return NullValue
     */
    protected function loadNull() {

        $this->stats->addNull();

        return new NullValue();
    }

    /**
     * @param int $length
     * @return StringValue
     */
    protected function loadString($length) {

        $this->stats->addString();

        $stringValue = new StringValue($this->unpackAndTakeString($length));
        $this->strings->add($stringValue);

        return $stringValue;
    }

    /**
     * @return StringTag
     */
    protected function loadEmptyString() {

        $this->stats->addEmptyString();

        return new EmptyStringValue();
    }

    /**
     * @param int $elementCount
     * @return ReferenceTag
     */
    protected function loadArray($elementCount) {

        $this->stats->addArray();

        $array = new ArrayValue();

        $tag = $this->references->add($array);

        $this->unpackArray($elementCount, $array);

        return $tag;
    }

    /**
     * @param bool $value
     * @return BooleanValue
     */
    protected function loadBool($value) {

        $this->stats->addBool();

        return new BooleanValue($value);
    }

    /**
     * @param int $value
     * @return LongValue
     */
    protected function loadLong($value) {

        $this->stats->addLong();

        return new LongValue($value);
    }

    /**
     * @param int $value
     * @return DoubleValue
     */
    protected function loadDouble($value) {

        $this->stats->addDouble();

        return new DoubleValue($value);
    }

    /**
     * @param int $id
     * @return StringValue
     */
    protected function loadStringById($id) {

        return $this->strings->getTag($id)->getStringValue();
    }

    /**
     * @return AbstractValue|ReferenceTag
     */
    protected function loadReferencedElement() {

        $unpacked = $this->unpackElement();

        if ($unpacked instanceof ReferenceTag) {
            $result = $unpacked;
        }
        elseif ($unpacked instanceof AbstractValue) {

            $result = $this->references->add($unpacked);
        }
        else {
            throw new Exception('Can not load referenced element from instance of "' . get_class($unpacked) . '"');
        }

        return $result;
    }

    /**
     * @param int $id
     * @return ReferenceTag
     */
    protected function loadElementReference($id) {

        $tag = $this->references->getTag($id);

        $tag->increaseUseCount();

        return $tag;
    }

    /**
     * @param string $classNameLength
     * @return ObjectValue
     */
    protected function loadObjectByClassName($classNameLength) {

        $className = new StringValue($this->unpackAndTakeString($classNameLength));

        $this->strings->add($className);

        return $this->loadObject($className);
    }

    /**
     * @param int $classNameId
     * @return ObjectValue
     */
    protected function loadObjectByClassNameId($classNameId) {

        $classTag = $this->strings->getTag($classNameId);

        return $this->loadObject($classTag->getStringValue());
    }

    /**
     * @param StringValue $className
     * @return ObjectValue
     */
    protected function loadObject(StringValue $className) {

        $this->stats->addObject();

        $object = new ObjectValue();
        $tag = $this->references->add($object);

        $this->unpackObject($className, $object);

        return $tag;
    }

    /**
     * @param int $id
     * @return ReferenceTag
     */
    protected function loadObjectReference($id) {

        $tag = $this->references->getTag($id);

        $tag->increaseUseCount();

        return $tag;
    }

    /**
     * @return AbstractElement
     */
    protected function unpackElement() {

        $type = $this->unpackLong8();

        $result = null;
        switch ($type) {
            case Constants::IGBINARY_TYPE_NULL:
                $result = $this->loadNull();
                break;
            case Constants::IGBINARY_TYPE_STRING8:
                $result = $this->loadString($this->unpackLong8());
                break;
            case Constants::IGBINARY_TYPE_STRING16:
                $result = $this->loadString($this->unpackLong16());
                break;
            case Constants::IGBINARY_TYPE_STRING32:
                $result = $this->loadString($this->unpackLong32());
                break;
            case Constants::IGBINARY_TYPE_STRING_EMPTY:
                $result = $this->loadEmptyString();
                break;
            case Constants::IGBINARY_TYPE_ARRAY8:
                $result = $this->loadArray($this->unpackLong8());
                break;
            case Constants::IGBINARY_TYPE_ARRAY16:
                $result = $this->loadArray($this->unpackLong16());
                break;
            case Constants::IGBINARY_TYPE_ARRAY32:
                $result = $this->loadArray($this->unpackLong32());
                break;
            case Constants::IGBINARY_TYPE_BOOL_FALSE:
                $result = $this->loadBool(FALSE);
                break;
            case Constants::IGBINARY_TYPE_BOOL_TRUE:
                $result = $this->loadBool(TRUE);
                break;
            case Constants::IGBINARY_TYPE_LONG8P:
                $result = $this->loadLong($this->unpackLong8());
                break;
            case Constants::IGBINARY_TYPE_LONG8N:
                $result = $this->loadLong(-$this->unpackLong8());
                break;
            case Constants::IGBINARY_TYPE_LONG16P:
                $result = $this->loadLong($this->unpackLong16());
                break;
            case Constants::IGBINARY_TYPE_LONG16N:
                $result = $this->loadLong(-$this->unpackLong16());
                break;
            case Constants::IGBINARY_TYPE_LONG32P:
                $result = $this->loadLong($this->unpackLong32());
                break;
            case Constants::IGBINARY_TYPE_LONG32N:
                $result = $this->loadLong(-$this->unpackLong32());
                break;
            case Constants::IGBINARY_TYPE_LONG64P:
                $result = $this->loadLong($this->unpackLong64());
                break;
            case Constants::IGBINARY_TYPE_LONG64N:
                $result = $this->loadLong(-$this->unpackLong64());
                break;
            case Constants::IGBINARY_TYPE_DOUBLE:
                $result = $this->loadDouble($this->unpackDouble());
                break;
            case Constants::IGBINARY_TYPE_STRING_ID8:
                $result = $this->loadStringById($this->unpackLong8());
                break;
            case Constants::IGBINARY_TYPE_STRING_ID16:
                $result = $this->loadStringById($this->unpackLong16());
                break;
            case Constants::IGBINARY_TYPE_STRING_ID32:
                $result = $this->loadStringById($this->unpackLong32());
                break;
            case Constants::IGBINARY_TYPE_REF:
                $result = $this->loadReferencedElement();
                break;
            case Constants::IGBINARY_TYPE_REF8:
                $result = $this->loadElementReference($this->unpackLong8());
                break;
            case Constants::IGBINARY_TYPE_REF16:
                $result = $this->loadElementReference($this->unpackLong16());
                break;
            case Constants::IGBINARY_TYPE_REF32:
                $result = $this->loadElementReference($this->unpackLong32());
                break;
            case Constants::IGBINARY_TYPE_OBJECT8:
                $result = $this->loadObjectByClassName($this->unpackLong8());
                break;
            case Constants::IGBINARY_TYPE_OBJECT16:
                $result = $this->loadObjectByClassName($this->unpackLong16());
                break;
            case Constants::IGBINARY_TYPE_OBJECT32:
                $result = $this->loadObjectByClassName($this->unpackLong32());
                break;
            case Constants::IGBINARY_TYPE_OBJECT_ID8:
                $result = $this->loadObjectByClassNameId($this->unpackLong8());
                break;
            case Constants::IGBINARY_TYPE_OBJECT_ID16:
                $result = $this->loadObjectByClassNameId($this->unpackLong16());
                break;
            case Constants::IGBINARY_TYPE_OBJECT_ID32:
                $result = $this->loadObjectByClassNameId($this->unpackLong32());
                break;
            case Constants::IGBINARY_TYPE_OBJREF8:
                $result = $this->loadObjectReference($this->unpackLong8());
                break;
            case Constants::IGBINARY_TYPE_OBJREF16:
                $result = $this->loadObjectReference($this->unpackLong16());
                break;
            case Constants::IGBINARY_TYPE_OBJREF32:
                $result = $this->loadObjectReference($this->unpackLong32());
                break;
            default:
                throw new \RuntimeException(sprintf('Do no know how to handle type 0x%02x', $type));
        }

        return $result;
    }

    /**
     * @return int
     */
    protected function unpackLong8() {

        list($long8) = $this->unpackAndTake('C');
        return $long8;
    }

    /**
     * @return int
     */
    protected function unpackLong16() {

        list($long16) = $this->unpackAndTake('n');
        return $long16;
    }

    /**
     * @return int
     */
    protected function unpackLong32() {

        list($long32) = $this->unpackAndTake('N');
        return $long32;
    }

    /**
     * @return int
     */
    protected function unpackLong64() {

        $higher = $this->unpackAndTake('N');
        $lower = $this->unpackAndTake('N');

        $long64 = $higher << 32 | $lower;

        return $long64;
    }

    /**
     * @return int
     */
    protected function unpackDouble() {

        $packedDouble = $this->unpackAndTakeString(8);

        $unpack = unpack('d', $this->switchByteOrder ? strrev($packedDouble) : $packedDouble);

        list(, $double) = $unpack;

        return $double;
    }

    /**
     * @param int $length
     * @return string
     */
    protected function unpackAndTakeString($length) {

        return join('', array_map('chr', $this->unpackAndTake('C' . $length)));
    }

    /**
     * @param int $elementCount
     * @param ArrayValue|null $array
     * @return ArrayValue
     */
    protected function unpackArray($elementCount, ArrayValue $array = null) {

        if ($array === null) {
            $array = new ArrayValue();
        }

        for ($i = 0; $i != $elementCount; $i++) {
            $key = $this->unpackElement();
            $value = $this->unpackElement();

            if (!$key instanceof AbstractScalarValue) {
                throw new Exception('Array key element class "' . get_class($key) . '" not recognized.');
            }

            $array->add($key, $value);
        }

        return $array;
    }

    /**
     * @param StringTag|StringValue $className
     * @param ObjectValue $object
     * @return array
     */
    protected function unpackObject($className, ObjectValue $object) {

        $object->setClassName($className);

        $packingType = $this->unpackLong8();

        switch ($packingType) {
            case Constants::IGBINARY_TYPE_ARRAY8:
                $array = $this->unpackArray($this->unpackLong8());
                break;
            case Constants::IGBINARY_TYPE_ARRAY16:
                $array = $this->unpackArray($this->unpackLong16());
                break;
            case Constants::IGBINARY_TYPE_ARRAY32:
                $array = $this->unpackArray($this->unpackLong32());
                break;
//            case Constants::IGBINARY_TYPE_OBJECT_SER8:
//                $elements = $this->unpackAndTakeString($this->unpackLong8());
//                break;
//            case Constants::IGBINARY_TYPE_OBJECT_SER16:
//                $elements = $this->unpackAndTakeString($this->unpackLong16());
//                break;
//            case Constants::IGBINARY_TYPE_OBJECT_SER32:
//                $elements = $this->unpackAndTakeString($this->unpackLong32());
//                break;
            default:
                throw new \RuntimeException(sprintf('Do no know how to handle object serialization type 0x%02x', $packingType));
        }

        $object->setValuesFromArrayValue($array);
    }

    /**
     * @return bool
     */
    private function isPlatformLittleEndian() {

        $test = 0x00FF;
        $packed = pack('S', $test);
        return $test === current(unpack('v', $packed));
    }
}
