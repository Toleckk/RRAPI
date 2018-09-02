<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 2:38
 */

namespace RR\Entity;


use RR\RR;

class Collection extends Container implements \ArrayAccess, \Countable {
    protected $containerType;

    /**
     * Collection constructor.
     * @param RR|null $rr
     * @param int|null $id
     * @param string $containerType
     */
    public function __construct(RR &$rr = null, int $id = null, string $containerType = null){
        parent::__construct($rr, $id);
        $this->data = new \ArrayObject();
        $this->containerType = $containerType;
    }

    public static function fromBuilder(RR &$rr, \ArrayObject $arr, string $containerType = null){
        $instance = new static($rr, null, $containerType);
        $instance->fillData($arr);
        return $instance;
    }


    public function __debugInfo(){
        return $this->data->getArrayCopy();
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset){
        return $this->data->offsetExists($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset){
        $value = $this->data->offsetGet($offset);
        return is_object($value) && is_subclass_of($value, Container::class) ? $value->get() : $value;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value){
        $this->data->offsetSet($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset){
        $this->data->offsetUnset($offset);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count(): int{
        return $this->data->count();
    }

    /**
     * @param Model|Collection|array $new
     */
    public function append($new){
        if(is_object($new) && array_pop(explode('\\', get_class($new))) == $this->containerType)
            $this->data->append($new);
        else if(is_array($new))
            $this->appendArray($new);
        else if(is_object($new) && get_class($new) == self::class)
            $this->appendCollection($new);
    }

    private function appendArray(array $arr){
        foreach ($arr as $key => $value)
            if(is_string($key))
                $this->data[$key] = $value;
            else
                $this->data->append($value);
    }

    private function appendCollection(Collection $collection){
        $this->appendArray($collection->data->getArrayCopy());
    }
}