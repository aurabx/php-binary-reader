<?php

namespace PhpBinaryReader\Type;

use PhpBinaryReader\BinaryReader;
use PhpBinaryReader\Exception\InvalidDataException;

class Str implements TypeInterface
{
    /**
     * @param  \PhpBinaryReader\BinaryReader $br
     * @param  int                           $length
     * @return Str
     * @throws \OutOfBoundsException
     * @throws InvalidDataException
     */
    public function read(BinaryReader &$br, $length)
    {
        if (!is_int($length)) {
            throw new InvalidDataException('The length parameter must be an integer');
        }

        if (($length + $br->getPosition()) > $br->getEofPosition()) {
            throw new \OutOfBoundsException('Cannot read string, it exceeds the boundary of the file');
        }

        $str = substr($br->getInputString(), $br->getPosition(), $length);
        $br->setPosition($br->getPosition() + $length);

        return $str;
    }

    /**
     * @param  \PhpBinaryReader\BinaryReader $br
     * @param  int                           $length
     * @return Str
     */
    public function readAligned(BinaryReader &$br, $length)
    {
        $br->align();

        return $this->read($br, $length);
    }
}
