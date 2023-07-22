<?php
declare(strict_types = 1);

namespace PhpBinaryReader;

use PhpBinaryReader\Exception\InvalidDataException;
use PhpBinaryReader\Type\Bit;
use PhpBinaryReader\Type\Byte;
use PhpBinaryReader\Type\Int8;
use PhpBinaryReader\Type\Int16;
use PhpBinaryReader\Type\Int32;
use PhpBinaryReader\Type\Int64;
use PhpBinaryReader\Type\Single;
use PhpBinaryReader\Type\Str;

class BinaryReader
{
    private int $machineByteOrder = Endian::LITTLE;
    private int $currentBit;

    private $inputHandle;
    private $nextByte;

    private int $position;
    private int $eofPosition;
    private int $endian;

    public Byte $byteReader;
    public Bit $bitReader;
    public Str $stringReader;
    public Single $singleReader;
    public Int8 $int8Reader;
    public Int16 $int16Reader;
    public Int32 $int32Reader;
    public Int64 $int64Reader;

    /**
     * @param  string|resource           $input
     * @param  int|string                $endian
     * @throws \InvalidArgumentException
     */
    public function __construct($input, $endian = Endian::LITTLE)
    {
        if (!is_resource($input)) {
            $this->setInputString($input);
        } else {
            $this->setInputHandle($input);
        }

        $this->eofPosition = fstat($this->getInputHandle())['size'];

        $this->setEndian($endian);
        $this->setNextByte(false);
        $this->setCurrentBit(0);
        $this->setPosition(0);

        $this->bitReader = new Bit();
        $this->stringReader = new Str();
        $this->byteReader = new Byte();
        $this->int8Reader = new Int8();
        $this->int16Reader = new Int16();
        $this->int32Reader = new Int32();
        $this->int64Reader = new Int64();
        $this->singleReader = new Single();
    }

    public function isEof(): bool
    {
        return $this->position >= $this->eofPosition;
    }

    public function canReadBytes(float $length = 0): bool
    {
        return $this->position + $length <= $this->eofPosition;
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

    public function readInt64(): string
    {
        return $this->int64Reader->readSigned($this);
    }

    public function readUInt64(): string
    {
        return $this->int64Reader->read($this);
    }

    public function readSingle(): float
    {
        return $this->singleReader->read($this);
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

    public function setInputHandle($inputHandle)
    {
        $this->inputHandle = $inputHandle;

        return $this;
    }

    public function getInputHandle()
    {
        return $this->inputHandle;
    }

    public function setInputString(string $inputString): self
    {
        $handle = fopen('php://memory', 'br+');
        fwrite($handle, $inputString);
        rewind($handle);
        $this->inputHandle = $handle;

        return $this;
    }

    public function getInputString(): string
    {
        $handle = $this->getInputHandle();
        $str = stream_get_contents($handle);
        rewind($handle);

        return $str;
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
        fseek($this->getInputHandle(), $position);

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

    public function readFromHandle(int $length): string
    {
        $this->position += $length;
        return fread($this->inputHandle, $length);
    }

    public function advance(int $length): self
    {
        $this->readBytes($length);

        return $this;
    }
}
