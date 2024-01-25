<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Contracts\Types;

use InvalidArgumentException;
use Web3\Utils;
use Web3\Contracts\Types\BaseArray;
use Web3\Formatters\IntegerFormatter;

class DynamicArray extends BaseArray
{
    /**
     * construct
     * 
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * isType
     * 
     * @param string $name
     * @return bool
     */
    public function isType($name)
    {
        return (preg_match('/(\[\])/', $name) === 1);
    }

    /**
     * isDynamicType
     * 
     * @return bool
     */
    public function isDynamicType()
    {
        return true;
    }

    /**
     * inputFormat
     * 
     * @param mixed $value
     * @param string $name
     * @return string
     */
    public function inputFormat($value, $name)
    {
        $results = $this->encodeElements($value, $name);
        $encodeSize = IntegerFormatter::format(count($value));
        return $encodeSize . implode('', $results);
    }

    /**
     * outputFormat
     * 
     * @param string $value
     * @param array $abiType
     * @return array
     */
    public function outputFormat($value, $abiType)
    {
        if (!is_array($abiType)) {
            throw new InvalidArgumentException('Invalid abiType to decode sized array, expected: array');
        }
        $lengthHex = mb_substr($value, 0, 64);
        $length = (int) Utils::hexToNumber($lengthHex);
        $offset = 64;
        $results = [];
        $decoder = $abiType['coders'];
        for ($i = 0; $i < $length; $i++) {
            $results[] = $decoder['solidityType']->decode($value, $offset, $decoder);
            $offset += 64;
        }
        return $results;
    }
}