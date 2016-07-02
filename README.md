# DRAW TREE FROM MATHEMATICAL EXPRESSION
## Short info
This App Draws a tree from any mathematical expressions.
For example, you you have a mathematical expression like f(x,y,i(z)) you will get the drawing of the tree like

* f 
  * x
  * y
  * i
    * z
  


## Usage example
Draw a simple tree from the f(x,y,z) expression:
```php
$Tree2 = new Expression("f(x,y,z)", array('f' => 3));

$is_valid_expression = $Tree2->validate_expression();

$errors_array = $Tree2->get_errors();

echo $Tree2->show_tree();
```

You can also draw the subtree from a specific position
```php
	echo $Tree2->get_tree_from_position(1,2);
```