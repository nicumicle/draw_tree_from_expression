<?php

require_once("Object.class.php");

/**
 * Transform a mathematical expression into array and draws a tree from it
 * @author Nicu Micle
 */
class Expression {

	private
	$tree_array = array(),
	$parent = 0,
	$id = 0,
	$parent_index = 0,
	$parents_arr = array(0),
	$symbols_arr = array("(", ")", ","),
	$function_symbols_arr = array(),
	$expression_string,
	$expression_string_initial,
	$assoc_arr = array(),
	$positions_array = array(),
	$errors = array();

	/**
	 * Init Data
	 * @param string $expression_string The expression we want to analyze
	 * @param[optional] array $function_symbold_arr Array of accepted symbols and number 
	 * of arguments for the expression. It affects only the expression validation
	 */
	public function __construct($expression_string, $function_symbold_arr = array())
	{
		$this->expression_string = $expression_string;
		$this->expression_string_initial = $expression_string;
		$this->function_symbols_arr = $function_symbold_arr;
		$this->parser();
	}

	/*
	 * Parses the expression and transforms it to array
	 * The expression is set from the constructor
	 * example: new Expression('f(x,y,z)');
	 */
	function parser()
	{
		$c1 = $this->get_first_char($this->expression_string, $this->expression_string);
		if ($c1 == "")
			return;
		if (!in_array($c1, $this->symbols_arr))
		{
			$this->id++;
			$this->tree_array[] = new Object($this->id, $c1, $this->parents_arr[$this->parent]);
		}
		if ($c1 == "(")
		{
			$this->parent_index ++;
			$this->parents_arr[$this->parent_index] = $this->id;
			$this->parent++;
		}
		elseif ($c1 == ")")
		{
			$this->parent_index --;
			$this->parent = $this->parents_arr[$this->parent_index];
		}

		if ($c1 != "")
			$this->parser();
		else
			return;
	}

	/**
	 * Find the first char from a string 
	 * @param String Initial text
	 * @param String $val We return the String without the first character
	 * @return First char of the string
	 */
	function get_first_char($ex, &$val)
	{
		$val = substr($ex, 1);
		return substr($ex, 0, 1);
	}

	/**
	 * Validate the expression
	 * Also, sets the error messages in case the expression it is not valid
	 * @return boolean 
	 */
	function validate_expression()
	{
		$function_args_arr = array();
		if (is_array($this->tree_array))
			foreach ($this->tree_array as $key => $object)
			{
				foreach ($this->tree_array as $key2 => $object2)
				{
					if ($key2 < $key)
						continue;
					if ($object2->parent == $object->id)
					{
						if (isset($function_args_arr[$object->id][$object->value]))
							$function_args_arr[$object->id][$object->value] ++;
						else
							$function_args_arr[$object->id][$object->value] = 1;
					}
				}
			}

		$is_valid = true;
		if (is_array($function_args_arr))
		{
			foreach ($function_args_arr as $key => $arr)
			{
				foreach ($arr as $key => $val)
					if (isset($this->function_symbols_arr[$key]))
						if ($this->function_symbols_arr[$key] != $val)
						{
							$is_valid = false;
							$this->set_error("Expression validation error :  <b>$key</b>  should have " . $this->function_symbols_arr[$key] . " arguments, but it has  " . $val . "");
						}
			}
		}
		return $is_valid;
	}

	/**
	 * Set the position of an element in the tree
	 * @param int $item_id ID of the element we want to set the position
	 * @param int $pos1 : x position of the element
	 * @param int $pos2 : y position of the element
	 * @return type null
	 */
	function set_item_position($item_id, $pos1, $pos2)
	{
		if (is_array($this->tree_array))
		{
			foreach ($this->tree_array as $key => $object)
			{
				if ($object->id == $item_id)
				{
					$this->tree_array[$key]->pos1 = $pos1;
					$this->tree_array[$key]->pos2 = $pos2;
					return;
				}
				else
					continue;
			}
		}
	}

	/**
	 * Init the array to be sent for the drawing function
	 * @param  array Array of the initial tree
	 * @return type Array
	 */
	function createTree(Array $tree_array)
	{
		$indexedItems = array();
		//index elements by id
		foreach ($tree_array as $key => $item)
		{
			$item->subs = array();
			$indexedItems[$item->id] = $item;
		}

		//assign to parent
		$topLevel = array();
		foreach ($indexedItems as $key => $item)
		{
			if ($item->parent == 0)
			{
				$topLevel[] = $item;
			}
			else
			{
				$indexedItems[$item->parent]->subs[] = $item;
			}
		}

		return $topLevel;
	}

	/**
	 * Reccursive drawing of the tree
	 * @param array $items Array with the root element of the tree
	 * @param int $pos Default x position of the element (by default we should put 0)
	 * @return String : Tree HTML code
	 */
	function renderTree($items, $pos)
	{
		$newline = "\n";
		$tab = "\t";
		$render = '<ul >' . $newline;
		if ($pos == 0)
			$pos2 = 0;
		else
			$pos2 = 1;
		foreach ($items as $item)
		{
			$pos2 = $this->check_position($pos, $pos2);
			$this->set_item_position($item->id, $pos, $pos2);
			$render .= $tab . '<li>' . $newline . $tab . '<span class="value"><b>' . $item->value . '</b><i>(' . $pos . ',' . $pos2 . ')</i></span>' . $newline;
			if (!empty($item->subs))
			{
				$render .= $this->renderTree($item->subs, $pos + 1);
			}
			$render .= '</li>' . $newline;
			$pos2++;
		}

		return $render . '</ul>' . $newline;
	}

	/**
	 * Checks that there are no nodes with same positions
	 * @param int $pos1 Index x from array
	 * @param int $pos2 Index y from array
	 * @return int new value for y position
	 */
	function check_position($pos1, $pos2)
	{
		if (isset($this->positions_array[$pos1][$pos2]))
		{
			$pos2++;
			return $this->check_position($pos1, $pos2);
		}
		else
		{
			$this->positions_array[$pos1][$pos2] = true;
			return $pos2;
		}
	}

	/**
	 * Aux function where do we init and render the tree
	 */
	function show_tree()
	{
		$topLevel = $this->createTree($this->tree_array);
		return $this->renderTree($topLevel, 0);
	}

	/**
	 * Find the node at the specified position
	 * @param int $pos1  x position
	 * @param int $pos2  y position
	 * @return type
	 *  'key' => node key from the array
	 *  'id' => object id 
	 *  'object' => Contains all the informations about the node(also contains the subnodes)
	 */
	function find_elem_at_pos($pos1, $pos2)
	{
		foreach ($this->tree_array as $key => $object)
			if (isset($object->pos1) && isset($object->pos2))
				if ($object->pos1 == $pos1 && $object->pos2 == $pos2)
					return array('key' => $key, 'id' => $object->id, 'object' => $object);
	}

	function replace_tree_array($index, $object)
	{
		if (isset($this->tree_array[$index]))
			$this->tree_array[$index] = $object;
	}

	/*
	 * Getter for the tree array
	 */
	function get_tree_array()
	{
		return $this->tree_array;
	}

	/**
	 * Init the array for drawing the tree from a specific position
	 * @param array $obj_arr  initial array
	 * @param int $old_id it of the old id
	 * @param array $final_arr return the final array
	 */
	function make_arr_from_subs($obj_arr, $old_id, &$final_arr)
	{
		foreach ($obj_arr as $val)
		{
			if ($val->parent == $old_id)
				$val->parent = 1;
			$final_arr[] = $val;
			if (isset($val->subs) && !empty($val->subs))
				$this->make_arr_from_subs($val->subs, $old_id, $final_arr);
		}
	}

	/**
	 * Reset all positions to make sure that the new positions are correct
	 */
	function reset_leafs_positions()
	{
		$this->positions_array = array();
	}

	/**
	 * Draws the Tree from a specific position, instead of drawing the full Tree.
	 * @param int $pos1
	 * @param int $pos2
	 * return string html of the tree
	 */
	function get_tree_from_position($pos1, $pos2)
	{
		$this->reset_leafs_positions();
		$pos11 = $this->find_elem_at_pos($pos1, $pos2);
		if (isset($pos11['object']->id))
		{
			$old_id = $pos11['object']->id;
			$pos11['object']->id = 1;
			$pos11['object']->parent = 0;
			$finalarr[] = $pos11['object'];
			$this->make_arr_from_subs($pos11['object']->subs, $old_id, $finalarr);
			$finalArr = $this->createTree($finalarr);
			return $this->renderTree($finalArr, 0);
		}
	}

	/**
	 * Set the errors for expression validation
	 * @param String $error_message The error message to be stored in the array
	 */
	public function set_error($error_message)
	{
		$this->errors[] = $error_message;
	}

	/**
	 * Gets the errors for the expression validation
	 * @return array of error messages or false
	 */
	public function get_errors()
	{
		if (!empty($this->errors))
			return $this->errors;
		else
			return false;
	}

}
