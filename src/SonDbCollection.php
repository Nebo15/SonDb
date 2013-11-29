<?php

class SonDbCollection implements ArrayAccess, Countable
{
	public $model_class;
	public $json_file;
	protected $items = [];

	function __construct($model_class, $json_file)
	{
		$this->model_class = $model_class;
		$this->json_file = $json_file;
	}

	function save()
	{
		return file_put_contents($this->json_file, json_encode($this->items, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	}

	function offsetSet($offset, $value)
	{
        if (is_null($offset))
            throw new Exception('Where is my ID, Lebowsky?');
        else
            $this->items[$offset] = $value;
        return $this;
    }

    function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    function count()
    {
    	return count($this->items);
    }
}