<?php

use KristsK\PhpIgbinary\Elements\Tags\ReferenceTag;
use KristsK\PhpIgbinary\Reader;
use KristsK\PhpIgbinary\Elements\Values;

/**
 * Class ReaderTest
 * @function string igbinary_serialize($phpValue)
 */
class ReaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider scalarValuesProvider
     *
     * @param $scalarValue
     */
    public function testScalarValues($className, $scalarValue) {

        /** @noinspection PhpUndefinedFunctionInspection */
        $serialized = igbinary_serialize($scalarValue);

        $reader = new Reader($serialized);

        $rootElement = $reader->getRootElement();

        $this->assertInstanceOf($className, $rootElement);

        /* @var $rootElement Values\Scalar\AbstractScalarValue */
        $this->assertEquals($scalarValue, $rootElement->getPhpValue());
    }

    /**
     * @return array
     */
    public function scalarValuesProvider() {

        return [
            [Values\Scalar\BooleanValue::class, true],
            [Values\Scalar\BooleanValue::class, false],
            [Values\Scalar\NullValue::class, null],
            [Values\Scalar\AbstractNumericValue::class, 0],
            [Values\Scalar\AbstractNumericValue::class, 1],
            [Values\Scalar\AbstractNumericValue::class, 100],
            [Values\Scalar\AbstractNumericValue::class, 0xAAAA],
            [Values\Scalar\AbstractNumericValue::class, 0xAAAAAAAA],
            [Values\Scalar\AbstractNumericValue::class, 0xAAAAAAAAAAAAAAAA],
            [Values\Scalar\AbstractNumericValue::class, -1],
            [Values\Scalar\EmptyStringValue::class, ''],
            [Values\Scalar\StringValue::class, 'wat'],
            [Values\Scalar\AbstractNumericValue::class, 0.0],
            [Values\Scalar\AbstractNumericValue::class, 100.100],
            [Values\Scalar\StringValue::class, 'glāžšķūņrūķīši']
        ];
    }

    /**
     * @dataProvider arrayValuesProvider
     * @param array $array
     */
    public function testArrayValues($array) {

        /** @noinspection PhpUndefinedFunctionInspection */
        $serialized = igbinary_serialize($array);

        $reader = new Reader($serialized);

        $rootElement = $reader->getRootElement();

        $this->assertInstanceOf(ReferenceTag::class, $rootElement);

        /* @var $rootElement ReferenceTag */
        $arrayValue = $rootElement->getValue();

        $this->assertInstanceOf(Values\Compound\ArrayValue::class, $arrayValue);
        /* @var $arrayValue Values\Compound\ArrayValue */

        $compoundValues = $arrayValue->getValues();
        foreach ($array as $expectedKey => $expectedValue) {

            $this->assertArrayHasKey($expectedKey, $compoundValues);

            $value = $compoundValues[$expectedKey]->getValue();

            $this->assertInstanceOf(Values\Scalar\AbstractScalarValue::class, $value);
            /* @var $value Values\Scalar\AbstractScalarValue */

            $this->assertEquals($expectedValue, $value->getPhpValue());
        }
    }

    /**
     * @return array
     */
    public function arrayValuesProvider() {

        return [
            [[]],
            [[1, 2, 3, 4]],
            [['wat1', 'wat2', 'wat3']],
            [['key1' => 123, 555, null => 'watwat']],
        ];
    }

    /**
     * @dataProvider objectValuesProvider
     *
     */
    public function testObjectValues($object, $properties) {

        /** @noinspection PhpUndefinedFunctionInspection */
        $serialized = igbinary_serialize($object);

        $reader = new Reader($serialized);

        $rootElement = $reader->getRootElement();

        $this->assertInstanceOf(ReferenceTag::class, $rootElement);

        /* @var $rootElement ReferenceTag */
        $objectValue = $rootElement->getValue();

        $this->assertInstanceOf(Values\Compound\ObjectValue::class, $objectValue);
        /* @var $objectValue Values\Compound\ObjectValue */

        $compoundValues = $objectValue->getValues();
        foreach ($properties as $expectedKey => $expectedValue) {

            $this->assertArrayHasKey($expectedKey, $compoundValues);

            $value = $compoundValues[$expectedKey]->getValue();

            $this->assertInstanceOf(Values\Scalar\AbstractScalarValue::class, $value);
            /* @var $value Values\Scalar\AbstractScalarValue */

            $this->assertEquals($expectedValue, $value->getPhpValue());
        }
    }

    /**
     * @return array
     */
    public function objectValuesProvider() {

        return [
            [
                new \DateTime('2010-02-03 11:22:33.000000'),
                ['date' => '2010-02-03 11:22:33.000000']
            ],
            [
                new DateInterval('P2Y3M4DT5H6M7S'),
                [
                    'y' => 2,
                    'm' => 3,
                    'd' => 4,
                    'h' => 5,
                    'i' => 6,
                    's' => 7
                ]
            ]
        ];
    }
}
