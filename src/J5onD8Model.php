<?php

abstract class J5onD8Model
{
	static function load()
	{
		$class = get_called_class();
		$json_file = (new $class)->getJsonFile();
		if(file_exists($json_file))
		{
			$model_info_std_class = json_decode(file_get_contents($json_file));
			if(!$model_info_std_class)
				return new J5onD8Collection($class, $json_file);
			$collection = new J5onD8Collection($class, $json_file);
			foreach($model_info_std_class as $id => $item)
				$collection[$id] = self::convertStdClassToObject($class, $item);
			return $collection;
		}
		return new J5onD8Collection($class, $json_file);
	}

	static function getJsonFile()
	{
		$class_name = get_called_class();
		$class_name_undescore = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $class_name));
		if(property_exists($class_name, 'is_private') && $class_name::$is_private)
			return J5onD8::$private_dir.$class_name_undescore.'.json';
		else
			return J5onD8::$public_dir.$class_name_undescore.'.json';
	}

	static function convertStdClassToObject($class, $stdObject)
	{
		return unserialize(sprintf(
			'O:%d:"%s"%s', strlen($class), $class, strstr(serialize((array) $stdObject), ':')
    	));
	}
}