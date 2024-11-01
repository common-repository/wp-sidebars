<?php
/**
 * A simple form validation class
 * 
 * @package    AI Sidebars
 * @author     Daniel Hong <Amagine, Inc.>
 * @copyright  (c) 2011 Amagine, Inc.
 */
class AI_Validation {
	public $errors;
	private $_rules;
	private $_lang = array(
		'max_length'	=> '<em>%s</em> field cannot exceed %d characters.',
		'min_length'	=> '<em>%s</em> field must be at least %d characters in length.',
		'required'		=> '<em>%s</em> field is required.',
		'valid_email'	=> '<em>%s</em> field does not contain a valid email address.',
		'alpha_numeric'	=> '<em>%s</em> field must only contain alpha-numeric characters.'
	);

	function __construct() 
    {
		$this->_rules = array();
		$this->errors = '';
	}

	function add_error($str)
    {
		$this->errors .= '<p>' . $str . '</p>';
	}

	function add_field($field_name, $field_text, $validators)
    {
		$this->_rules[] = array(
			'field_name'	=> $field_name,
			'field_text'	=> $field_text,
			'validators'	=> $validators
		);
	}

	function validate()
    {
		$retval = TRUE;

		foreach ($this->_rules as $rule) {
			extract($rule);
			$validators = explode('|', $validators);

			foreach ($validators as $validator) {
				$vparam = '';

				// If validator is passing param, ex: max_length[20]
				if (preg_match("/\[(.*)\]/i", $validator, $matches)) {
					$validator = preg_replace("/\[(.*)\]/i", '', $validator);
					$vparam = $matches[1];
				}

				if (method_exists('AI_Validators', $validator)) {
					$params = array($this->post($field_name));

					// Add extra param passed by the validator
					if (! empty($vparam)) {
						$params[] = $vparam;
					}

					if (! call_user_func_array(array('AI_Validators', $validator), $params)) {
						$retval = FALSE;
						$this->errors .= '<p><strong>' . sprintf($this->_lang[$validator], $field_text, $vparam) . "</strong></p>\n";
						break;
					}
				}
			}
		}

		return $retval;
	}

    private function post($key)
    {
        return (isset($_POST[$key])) ? $_POST[$key] : '';
    }
}

/**
 * The validators
 */
class AI_Validators {
	public static function max_length($str, $len)
    {
		if (is_array($str)) {
			return (count($str) <= $len);
		}
		return (strlen($str) <= $len);
	}

	public static function min_length($str, $len)
    {
		if (is_array($str)) {
			return (count($str) >= $len);
		}
		return (strlen($str) >= $len);
	}

	public static function required($str)
    {
		if (! is_array($str)) {
			$str = trim($str);
		}
		return !empty($str);
	}

	public static function valid_email($str)
    {
		return (! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}

    public static function alpha_numeric($str, $exceptions = '')
    {
        return (! preg_match("/^[A-Za-z][A-Za-z0-9]*(?:[A-Za-z0-9{$exceptions}]+)*$/", $str)) ? FALSE : TRUE;
    }
}