<?php

namespace Achse\ShapeShiftIo;

use ArrayAccess;
use Countable;
use Nette\SmartObject;
use OutOfBoundsException;
use stdClass;

class MarketInfo implements ArrayAccess, Countable
{

    use SmartObject;

    /**
     * @var stdClass[]
     */
    private $data = [];

    /**
     * @param stdClass[] $result
     */
    public function __construct(array $result)
    {
        foreach ($result as $pairInfo) {
            $this->data[$pairInfo->pair] = $pairInfo;
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new OutOfBoundsException(sprintf('Offset %s is not valid.', $offset));
        }

        return $this->data[$offset];
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        throw new ImmutableException('MarketInfo is immutable object');
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        throw new ImmutableException('MarketInfo is immutable object');
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->data);
    }

}
