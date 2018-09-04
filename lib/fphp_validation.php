<?php

// --------------------------------------------------------------------

/**
 * Required
 *
 * @param	string
 * @return	bool
 */
function required($str)
{
	return is_array($str)
		? (empty($str) === FALSE)
		: (trim($str) !== '');
}

// --------------------------------------------------------------------

/**
 * Performs a Regular Expression match test.
 *
 * @param	string
 * @param	string	regex
 * @return	bool
 */
function regex_match($str, $regex)
{
	return (bool) preg_match($regex, $str);
}

// --------------------------------------------------------------------

/**
 * Minimum Length
 *
 * @param	string
 * @param	string
 * @return	bool
 */
function min_length($str, $val)
{
	if ( ! is_numeric($val))
	{
		return FALSE;
	}

	return ($val <= mb_strlen($str));
}

// --------------------------------------------------------------------

/**
 * Max Length
 *
 * @param	string
 * @param	string
 * @return	bool
 */
function max_length($str, $val)
{
	if ( ! is_numeric($val))
	{
		return FALSE;
	}

	return ($val >= mb_strlen($str));
}

// --------------------------------------------------------------------

/**
 * Exact Length
 *
 * @param	string
 * @param	string
 * @return	bool
 */
function exact_length($str, $val)
{
	if ( ! is_numeric($val))
	{
		return FALSE;
	}

	return (mb_strlen($str) === (int) $val);
}

// --------------------------------------------------------------------

/**
 * Valid URL
 *
 * @param	string	$str
 * @return	bool
 */
function valid_url($str)
{
	if (empty($str))
	{
		return FALSE;
	}
	elseif (preg_match('/^(?:([^:]*)\:)?\/\/(.+)$/', $str, $matches))
	{
		if (empty($matches[2]))
		{
			return FALSE;
		}
		elseif ( ! in_array(strtolower($matches[1]), array('http', 'https'), TRUE))
		{
			return FALSE;
		}

		$str = $matches[2];
	}

	// PHP 7 accepts IPv6 addresses within square brackets as hostnames,
	// but it appears that the PR that came in with https://bugs.php.net/bug.php?id=68039
	// was never merged into a PHP 5 branch ... https://3v4l.org/8PsSN
	if (preg_match('/^\[([^\]]+)\]/', $str, $matches) && ! is_php('7') && filter_var($matches[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== FALSE)
	{
		$str = 'ipv6.host'.substr($str, strlen($matches[1]) + 2);
	}

	return (filter_var('http://'.$str, FILTER_VALIDATE_URL) !== FALSE);
}

// --------------------------------------------------------------------

/**
 * Valid Email
 *
 * @param	string
 * @return	bool
 */
function valid_email($str)
{
	if (function_exists('idn_to_ascii') && preg_match('#\A([^@]+)@(.+)\z#', $str, $matches))
	{
		$str = $matches[1].'@'.idn_to_ascii($matches[2]);
	}

	return (bool) filter_var($str, FILTER_VALIDATE_EMAIL);
}

// --------------------------------------------------------------------

/**
 * Valid Emails
 *
 * @param	string
 * @return	bool
 */
function valid_emails($str)
{
	if (strpos($str, ',') === FALSE)
	{
		return $this->valid_email(trim($str));
	}

	foreach (explode(',', $str) as $email)
	{
		if (trim($email) !== '' && $this->valid_email(trim($email)) === FALSE)
		{
			return FALSE;
		}
	}

	return TRUE;
}

// --------------------------------------------------------------------

/**
 * Validate IP Address
 *
 * @param	string
 * @param	string	'ipv4' or 'ipv6' to validate a specific IP format
 * @return	bool
 */
function valid_ip($ip, $which = '')
{
	return $this->CI->input->valid_ip($ip, $which);
}

// --------------------------------------------------------------------

/**
 * Alpha
 *
 * @param	string
 * @return	bool
 */
function alpha($str)
{
	return ctype_alpha($str);
}

// --------------------------------------------------------------------

/**
 * Alpha-numeric
 *
 * @param	string
 * @return	bool
 */
function alpha_numeric($str)
{
	return ctype_alnum((string) $str);
}

// --------------------------------------------------------------------

/**
 * Alpha-numeric w/ spaces
 *
 * @param	string
 * @return	bool
 */
function alpha_numeric_spaces($str)
{
	return (bool) preg_match('/^[A-Z0-9 ]+$/i', $str);
}

// --------------------------------------------------------------------

/**
 * Alpha-numeric with underscores and dashes
 *
 * @param	string
 * @return	bool
 */
function alpha_dash($str)
{
	return (bool) preg_match('/^[a-z0-9_-]+$/i', $str);
}

// --------------------------------------------------------------------

/**
 * Numeric
 *
 * @param	string
 * @return	bool
 */
function numeric($str)
{
	return (bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $str);

}

// --------------------------------------------------------------------

/**
 * Integer
 *
 * @param	string
 * @return	bool
 */
function integer($str)
{
	return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
}

// --------------------------------------------------------------------

/**
 * Decimal number
 *
 * @param	string
 * @return	bool
 */
function decimal($str)
{
	return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
}

// --------------------------------------------------------------------

/**
 * Greater than
 *
 * @param	string
 * @param	int
 * @return	bool
 */
function greater_than($str, $min)
{
	return is_numeric($str) ? ($str > $min) : FALSE;
}

// --------------------------------------------------------------------

/**
 * Equal to or Greater than
 *
 * @param	string
 * @param	int
 * @return	bool
 */
function greater_than_equal_to($str, $min)
{
	return is_numeric($str) ? ($str >= $min) : FALSE;
}

// --------------------------------------------------------------------

/**
 * Less than
 *
 * @param	string
 * @param	int
 * @return	bool
 */
function less_than($str, $max)
{
	return is_numeric($str) ? ($str < $max) : FALSE;
}

// --------------------------------------------------------------------

/**
 * Equal to or Less than
 *
 * @param	string
 * @param	int
 * @return	bool
 */
function less_than_equal_to($str, $max)
{
	return is_numeric($str) ? ($str <= $max) : FALSE;
}

// --------------------------------------------------------------------

/**
 * Value should be within an array of values
 *
 * @param	string
 * @param	string
 * @return	bool
 */
function in_list($value, $list)
{
	return in_array($value, explode(',', $list), TRUE);
}

// --------------------------------------------------------------------

/**
 * Is a Natural number  (0,1,2,3, etc.)
 *
 * @param	string
 * @return	bool
 */
function is_natural($str)
{
	return ctype_digit((string) $str);
}

// --------------------------------------------------------------------

/**
 * Is a Natural number, but not a zero  (1,2,3, etc.)
 *
 * @param	string
 * @return	bool
 */
function is_natural_no_zero($str)
{
	return ($str != 0 && ctype_digit((string) $str));
}

// --------------------------------------------------------------------

/**
 * Is Unique
 *
 * Check if the input value doesn't already exist
 * in the specified database field.
 *
 * @param	string	$table
 * @param	string	$field
 * @param	string	$value
 * @param	string	$field_id
 * @param	string	$value_id
 * @return	bool
 */
function is_unique($table, $field, $value, $field_id = NULL, $value_id = NULL)
{
	$filtro = '';
	if (!empty($field_id)) $filtro .= " AND $field_id <> '$value_id'"; 

	$sql = "SELECT * FROM $table WHERE $field = '$value' $filtro";
	$rows = getRecords($sql);

	return (isset($table) && isset($field) && isset($value))
		? (count($rows) === 0)
		: FALSE;
}

/**
 * Valid Rif
 *
 * Valida el formato de un rif
 *
 * @param   string
 * @return  bool
 */
function valid_rif($str)
{
    if (trim($str) == '')
    {
        return TRUE;
    }

    return (bool) preg_match('/^[VEPGJC]{1}[0-9]{8,9}$/', $str);
}

/**
 * Valid Rif
 *
 * Valida el formato de un rif
 *
 * @param   string
 * @return  bool
 */
function valid_rif_natural($str)
{
    if (trim($str) == '')
    {
        return TRUE;
    }

    return (bool) preg_match('/^[VEP]{1}[0-9]{8,9}$/', $str);
}

/**
 * Valid Rif
 *
 * Valida el formato de un rif
 *
 * @param   string
 * @return  bool
 */
function valid_rif_juridico($str)
{
    if (trim($str) == '')
    {
        return TRUE;
    }

    return (bool) preg_match('/^[GJC]{1}[0-9]{8,9}$/', $str);
}
