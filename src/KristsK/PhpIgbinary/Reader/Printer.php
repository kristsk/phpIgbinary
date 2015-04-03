<?php

namespace KristsK\PhpIgbinary\Reader;

use KristsK\PhpIgbinary\Elements\AbstractElement;
use KristsK\PhpIgbinary\Elements\Tags\ReferenceTag;
use KristsK\PhpIgbinary\Elements\Tags\StringTag;
use KristsK\PhpIgbinary\Elements\Values\Compound\AbstractCompoundValue;
use KristsK\PhpIgbinary\Elements\Values\Compound\ArrayValue;
use KristsK\PhpIgbinary\Elements\Values\Compound\ObjectValue;
use KristsK\PhpIgbinary\Elements\Values\Scalar\AbstractScalarValue;
use KristsK\PhpIgbinary\Exception;
use KristsK\PhpIgbinary\Reader;

/**
 * Class Printer
 * @package KristsK\PhpIgbinary\Reader
 */
class Printer {

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var int
     */
    protected $indent = 0;

    /**
     * @var callable
     */
    protected $messagePrinter;

    /**
     * @var callable
     */
    protected $newlinePrinter;

    /**
     * @param $reader
     * @param callable $messagePrinter
     * @param callable $newlinePrinter
     */
    public function __construct($reader, callable $messagePrinter, callable $newlinePrinter) {

        $this->reader = $reader;

        $this->messagePrinter = $messagePrinter;
        $this->newlinePrinter = $newlinePrinter;
    }

    /**
     * @param string $message
     */
    protected function printMessageWithNewline($message) {

        $this->printMessage($message);

        $newlinePrinter = $this->newlinePrinter;
        $newlinePrinter();
    }

    /**
     * @param string $message
     */
    protected function printMessage($message) {

        $printer = $this->messagePrinter;
        $printer($message);
    }

    /**
     * @param AbstractElement $value
     */
    public function prettyPrint(AbstractElement $value) {

        if ($value instanceof ArrayValue) {
            $this->printArrayValue($value);
        }
        elseif ($value instanceof ObjectValue) {
            $this->printObjectValue($value);
        }
        elseif ($value instanceof AbstractScalarValue) {
            $this->printScalarValue($value);
        }
        elseif ($value instanceof ReferenceTag) {
            $this->printReferenceTag($value);
        }
        else {
            throw new Exception('Do not know how to print class "' . get_class($value) . '"');
        }
    }

    /**
     * @param AbstractScalarValue $scalarValue
     */
    protected function printScalarValue(AbstractScalarValue $scalarValue) {

        $this->printMessage($scalarValue->__toString());
    }

    /**
     * @param ArrayValue $arrayValue
     */
    protected function printArrayValue(ArrayValue $arrayValue) {

        $this->printCompoundValue($arrayValue);
    }

    /**
     * @param ObjectValue $objectValue
     */
    protected function printObjectValue(ObjectValue $objectValue) {

        $this->printMessageWithNewline('{ ' . $objectValue->getClassName() . ' ');

        $this->indent++;
        $this->printCompoundValue($objectValue);
        $this->indent--;
        $this->printMessage('}');
    }

    /**
     * @param AbstractCompoundValue $compoundValue
     */
    protected function printCompoundValue(AbstractCompoundValue $compoundValue) {

        if (!$compoundValue->getValues()) {
            $this->printMessageWithNewline('[]');
        }
        else {
            $this->printMessage('[');

            $compoundValueElements = $compoundValue->getValues();
            foreach ($compoundValueElements as $n => $compoundValueElement) {
                $key = $compoundValueElement->getKey();
                $value = $compoundValueElement->getValue();

                $this->printMessage(str_repeat(' ', ($this->indent + 1) * 2));

                if ($key instanceof AbstractScalarValue) {
                    $this->printScalarValue($key);
                }
                else {
                    throw new Exception('Do not know how to print compound key class "' . get_class($key) . '"');
                }

                $this->printMessage(' => ');

                $this->indent++;
                $this->prettyPrint($value);
                $this->indent--;

                if ($n != count($compoundValueElements) - 1) {
                    $this->printMessageWithNewline(',');
                }
                else {
                    $this->printMessageWithNewline('');
                }
            }
            $this->printMessage(str_repeat(' ', $this->indent * 2) . ']');
        }
    }

    /**
     * @param StringTag $tag
     */
    protected function printStringTag(StringTag $tag) {

        $this->printMessage($tag->getStringValue());
    }

    /**
     * @param ReferenceTag $tag
     */
    protected function printReferenceTag(ReferenceTag $tag) {

        if ($tag->getUseCount() === 1) {
            $this->prettyPrint($tag->getValue());
        }
        else {
            $this->printMessage($tag->__toString());
        }
    }
}