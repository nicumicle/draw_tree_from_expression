<?php

class SimpleObject{
	var $id;
	var $value;
	var $parent;
	
	public function __construct($id,$val,$parent)
	{
		$this->id = $id;
		$this->value= $val;
		$this->parent = $parent;
	}
}
