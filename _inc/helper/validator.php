<?php
function valdateMobilePhone($number) 
{
    return preg_match('/^([0-9]*)$/', $number);
}

// PHP 5.2 and above. built-in function by PHP provides a much more powerful sanitize capability.
function validateString($str)
{
    return filter_var($str, FILTER_SANITIZE_STRIPPED); # only 'String' is allowed eg. '<br>HELLO</br>' => 'HELLO'
}

// PHP 5.2 and above
function validateEmail($email)
{
  return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// PHP 5.2 and above.
function validateInteger($value)
{
    return filter_var($value, FILTER_VALIDATE_INT); # int
}

// PHP 5.2 and above.
function validateFloat($value)
{
    return filter_var($value, FILTER_VALIDATE_FLOAT); // float
}

function validateAlphanumeric($string)
{
    return ctype_alnum($string);
}

function SanitizeAlphanumeric($string)
{
    return preg_replace('/[^a-zA-Z0-9]/', '', $string);
}

function validateExpireDate($date) 
{
    return time() < strtotime($date);
}

function isItValidDate($date) 
{
    if(preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches)) {
        if(checkdate($matches[2], $matches[3], $matches[1])) { 
            return true;
        }
    }
} 

function isItValidTime($input) 
{
    return preg_match("/^([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $input);
} 

// $input is valid HH:MM AM/PM format
function isItValidTime12($input) 
{
    return preg_match("/^(?:0[1-9]|1[0-2]):[0-5][0-9] (am|pm|AM|PM)$/", $input);
}

function compareFloatNumbers($float1, $float2, $operator='=')
{
    // Check numbers to 5 digits of precision
    $epsilon = 0.00001;
    
    $float1 = (float)$float1;
    $float2 = (float)$float2;
    
    switch ($operator)
    {
        // equal
        case "=":
        case "eq":
        {
            if (abs($float1 - $float2) < $epsilon) {
                return true;
            }
            break;  
        }
        // less than
        case "<":
        case "lt":
        {
            if (abs($float1 - $float2) < $epsilon) {
                return false;
            }
            else
            {
                if ($float1 < $float2) {
                    return true;
                }
            }
            break;  
        }
        // less than or equal
        case "<=":
        case "lte":
        {
            if (compareFloatNumbers($float1, $float2, '<') || compareFloatNumbers($float1, $float2, '=')) {
                return true;
            }
            break;  
        }
        // greater than
        case ">":
        case "gt":
        {
            if (abs($float1 - $float2) < $epsilon) {
                return false;
            }
            else
            {
                if ($float1 > $float2) {
                    return true;
                }
            }
            break;  
        }
        // greater than or equal
        case ">=":
        case "gte":
        {
            if (compareFloatNumbers($float1, $float2, '>') || compareFloatNumbers($float1, $float2, '=')) {
                return true;
            }
            break;  
        }
        case "<>":
        case "!=":
        case "ne":
        {
            if (abs($float1 - $float2) > $epsilon) {
                return true;
            }
            break;  
        }
        default:
        {
            die("Unknown operator '".$operator."' in compareFloatNumbers()");   
        }
    }
    
    return false;
}

function url_exist($url)
{
    $url = @parse_url($url);
 
    if (!$url)
    {
        return false;
    }
 
    $url = array_map('trim', $url);
    $url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
    $path = (isset($url['path'])) ? $url['path'] : '';
 
    if ($path == '')
    {
        $path = '/';
    }
 
    $path .= (isset($url['query'])) ? '?$url[query]' : '';
 
    if (isset($url['host']) AND $url['host'] != @gethostbyname($url['host']))
    {
        if (PHP_VERSION >= 5)
        {
            $headers = @get_headers('$url[scheme]://$url[host]:$url[port]$path');
        }
        else
        {
            $fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);
 
            if (!$fp)
            {
                return false;
            }
            fputs($fp, 'HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n');
            $headers = fread($fp, 4096);
            fclose($fp);
        }
        $headers = (is_array($headers)) ? implode('\n', $headers) : $headers;
        return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
    }
    return false;
}

// PHP 5.2 and above.
function validateUrl($url)
{
  return filter_var($url, FILTER_VALIDATE_URL);
}

// Validate Proxy
// This function will let us detect proxy visitors even those that are behind anonymous proxy.
function validateProxy() 
{
    if ($_SERVER['HTTP_X_FORWARDED_FOR']
       || $_SERVER['HTTP_X_FORWARDED']
       || $_SERVER['HTTP_FORWARDED_FOR']
       || $_SERVER['HTTP_VIA']
       || in_array($_SERVER['REMOTE_PORT'], array(8080,80,6588,8000,3128,553,554))
       || @fsockopen($_SERVER['REMOTE_ADDR'], 80, $errno, $errstr, 30))
    {
        exit('Proxy detected');
    }
}

function validatePassword($password) 
{
    #must contain 8 characters, 1 uppercase, 1 lowercase and 1 number
    return preg_match('/^(?=^.{8,}$)((?=.*[A-Za-z0-9])(?=.*[A-Z])(?=.*[a-z]))^.*$/', $password);
}
