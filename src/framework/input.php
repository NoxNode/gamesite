<?php
class Input {
	public static function isValidInput($value, $min, $max, $format) {
		if ($value && strlen ($value) >= $min && strlen ($value) <= $max && $format) {
			return true;
		}
		return false;
	}
}
?>
