<?php
require_once("Expression.class.php");

$show_find_position = false;
$symbols_arr = array(0 => '');
$nr_args_arr = array(0 => '');

$expression = "";
$expression_args = array();

$position_x = "";
$position_y = "";

if (isset($_REQUEST['expression']))
{
	$expression = $_REQUEST['expression'];
	$show_find_position = true;
}

if (isset($_REQUEST['position_x']))
	$position_x = $_REQUEST['position_x'];
if (isset($_REQUEST['position_y']))
	$position_y = $_REQUEST['position_y'];

if (isset($_REQUEST['symbol']) && isset($_REQUEST['nr_args']))
{
	$symbols_arr = $_REQUEST['symbol'];
	$nr_args_arr = $_REQUEST['nr_args'];
	foreach ($symbols_arr as $key => $val)
	{
		if ($val != "" && $nr_args_arr[$key] != "")
			$expression_args[$val] = $nr_args_arr[$key];
	}

}

$Tree2 = new Expression($expression, $expression_args);
$is_valid_expression = $Tree2->validate_expression();
$errors_array = $Tree2->get_errors();
?>
<!DOCTYPE html>
<html>
	<head>
		<script type='text/javascript' src='https://code.jquery.com/jquery-3.0.0.min.js'></script>
		<script type='text/javascript' src='assets/main.js'></script>
		<link rel="stylesheet" type="text/css" href="assets/bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="assets/bootstrap/css/bootstrap-theme.css" />
		<link rel="stylesheet" type="text/css" href="assets/main.css" />
	</head>
	<body>
		<div class='container'>
			<h3>Term Rewriting</h3>
			<form method ="POST" action="">
				<!-- ACTIONS CALL -->
				<div class="panel panel-default">
					<div class="panel-body">
						<div class='form-group'>
							<button type="submit" class="btn btn-primary">CALCULATE</button>
							<a href='javascript:void(0)' onclick="redirect(window.location.href)" class="btn btn-default">RESET </a>
						</div>
					</div>
				</div>

				<!-- EXPRESSION -->
				<div class='panel  panel-default'>
					<div class="panel-body">
						<div class="form-group <?= ($is_valid_expression == false ? 'has-error' : ''); ?>">
							<label for="expression">Expression</label>
							<input type="text" name="expression" class="form-control" id="expression" value="<?= $expression; ?>" placeholder="Please enter an expression ex: f(x)"/>
							<?php
							if ($is_valid_expression == false)
							{
								foreach ($errors_array as $error)
								{
									?>
									<div class='error'><?= $error; ?></div>
									<?php
								}
							}
							?>
						</div>
					</div><!--end of panel body-->
				</div><!--end of panel-->

				<!--FUNCTION PARAMETERS -->
				<div class = 'panel  panel-default col-md-6'>
					<div class = "panel-body">
						<table id = "function_args_table" class = "table">
							<tr>
								<th class = "text-center">Symbol</th>
								<th class = "text-center">Number of args</th>
							</tr>
							<?php
							$i = 0;
							foreach ($symbols_arr as $key => $val)
							{
								?>
								<tr id="line<?= $i; ?>">
									<td>
										<input type="text" name="symbol[]" class="form-control" placeholder="Function symbol" value="<?= $symbols_arr[$key]; ?>"/>
									</td>
									<td>
										<input type="text" name="nr_args[]" class="form-control" placeholder="Number of arguments" value="<?= $nr_args_arr[$key]; ?>"/>
									</td>
								</tr>
								<?php
								$i++;
							}
							?>
						</table>

						<div class="form-group">
							<input type="button" class="btn btn-success" value="+ Line" id="add_new_line" onclick="add_line_click()"/>
						</div>

					</div><!--end of panel body-->
				</div><!--end of panel-->

				<?php
				if ($show_find_position)
				{
					?>

					<!--FIND TREE POSITION -->
					<div class="panel panel-default col-md-6">
						<div class="panel-body">
							<h4>Find a position from the tree</h4>
							<div class="form-group">
								<table id="position_table" class="table">
									<tr>
										<td>Position X</td>
										<td><input type="text" name="position_x" class="form-control" value="<?= $position_x; ?>" /></td>
									</tr>

									<tr>
										<td>Position Y</td>
										<td><input type="text" name="position_y" class="form-control" value="<?= $position_y; ?>" /></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<!--end of find position -->
					<?php
				}
				?>
			</form>
			<div class='row'></div>

			<?php
			if ($expression != "")
			{
				?>	
				<div class="panel panel-default col-md-6">
					<div class="panel-body">
						<label class='title'>
							Draw the Tree for <span class='highlight'><?= $expression; ?></span>
						</label>
						<?php
						echo $Tree2->show_tree();
						?>
					</div>
				</div>
				<?php
			}
			?>


			<?php
			if ($show_find_position == true && ($position_x != "") && ($position_y != ""))
			{
				?>
				<div class="panel panel-default col-md-6">
					<div class="panel-body">
						<label class='title'>Tree at position (<span class='highlight'><?= $position_x . "," . $position_y; ?></span>)</label>
						<?php
						echo $Tree2->get_tree_from_position($position_x, $position_y);
						?>
					</div>
				</div>
				<?php
			}
			?>

		</div>

		<!-- This is used to draw the new line -->
		<div style="display:none;">
			<table>
				<tr id="first_line">
					<td>
						<input type="text" name="symbol[]" class="form-control" placeholder="Function symbol"/>
					</td>
					<td>
						<input type="text" name="nr_args[]" class="form-control" placeholder="Number of arguments"/>
					</td>
				</tr>
			</table>
		</div>
		<!-- endof -->
	</body>
</html>