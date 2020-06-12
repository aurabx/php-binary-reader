<?php
declare(strict_types = 1);

namespace PhpBinaryReader;

use PhpBinaryReader\Exception\InvalidDataException;
use PhpBinaryReader\Type\Bit;
use PhpBinaryReader\Type\Byte;
use PhpBinaryReader\Type\Int8;
use PhpBinaryReader\Type\Int16;
use PhpBinaryReader\Type\Int32;
use PhpBinaryReader\Type\Str;

class BinaryReader
{
    private int $machineByteOrder = Endian::LITTLE;
    private string $inputString;
    private int $currentBit;

    private $nextByte;

    private int $position;
    private int $eofPosition;
    private int $endian;

    public Byte $byteReader;
    public Bit $bitReader;
    public Str $stringReader;
    public Int8 $int8Reader;
    public Int16 $int16Reader;
    public Int32 $int32Reader;

    public function __construct(string $str, $endian = Endian::LITTLE)
    {
        $this->bitReader = new Bit();
        $this->byteReader = new Byte();
        $this->stringReader = new Str();
        $this->int8Reader = new Int8();
        $this->int16Reader = new Int16();
        $this->int32Reader = new Int32();

        $this->eofPosition = strlen($str);

        $this->setEndian($endian);
        $this->setInputString($str);
        $this->setNextByte(false);
        $this->setCurrentBit(0);
        $this->setPosition(0);
    }

    public function isEof(): bool
    {
        if ($this->getPosition() >= $this->getEofPosition()) {
            return true;
        }
        return false;
    }

    public function align(): void
    {
        $this->setCurrentBit(0);
        $this->setNextByte(false);
    }

    public function readBits(int $count): int
    {
        return $this->bitReader->readSigned($this, $count);
    }

    public function readUBits(int $count): int
    {
        return $this->bitReader->read($this, $count);
    }

    public function readBytes(int $count): string
    {
        return $this->byteReader->read($this, $count);
    }

    public function readInt8(): int
    {
        return $this->int8Reader->readSigned($this);
    }

    public function readUInt8(): int
    {
        return $this->int8Reader->read($this);
    }

    public function readInt16(): int
    {
        return $this->int16Reader->readSigned($this);
    }

    public function readUInt16(): int
    {
        return $this->int16Reader->read($this);
    }

    public function readInt32(): int
    {
        return $this->int32Reader->readSigned($this);
    }

    public function readUInt32(): int
    {
        return $this->int32Reader->read($this);
    }

    public function readString(int $length): string
    {
        return $this->stringReader->read($this, $length);
    }

    public function readAlignedString(int $length): string
    {
        return $this->stringReader->readAligned($this, $length);
    }

    public function setMachineByteOrder(int $machineByteOrder): self
    {
        $this->machineByteOrder = $machineByteOrder;

        return $this;
    }

    public function getMachineByteOrder(): int
    {
        return $this->machineByteOrder;
    }

    public function setInputString(string $inputString): self
    {
        $this->inputString = $inputString;

        return $this;
    }

    public function getInputString(): string
    {
        return $this->inputString;
    }

    public function setNextByte($nextByte): self
    {
        $this->nextByte = $nextByte;

        return $this;
    }

    public function getNextByte()
    {
        return $this->nextByte;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getEofPosition(): int
    {
        return $this->eofPosition;
    }

    public function setEndian(int $endian): self
    {
        if ($endian == Endian::BIG) {
            $this->endian = Endian::BIG;
        } elseif ($endian == Endian::LITTLE) {
            $this->endian = Endian::LITTLE;
        } else {
            throw new InvalidDataException('Endian must be set as big or little');
        }

        return $this;
    }

    public function getEndian(): int
    {
        return $this->endian;
    }

    public function setCurrentBit(int $currentBit): self
    {
        $this->currentBit = $currentBit;

        return $this;
    }

    public function getCurrentBit(): int
    {
        return $this->currentBit;
    }
}
