<?php
/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/


if (!function_exists('json_encode')){  function json_encode($a=false)  {    if (is_null($a)) return 'null';    if ($a === false) return 'false';    if ($a === true) return 'true';    if (is_scalar($a))    {      if (is_float($a))      {   
     // Always use "." for floats.  
	       return floatval(str_replace(",", ".", strval($a)));      }      if (is_string($a))      {        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';      }      else        return $a;    }    $isList = true;    for ($i = 0, reset($a); $i < count($a); $i++, next($a))    {      if (key($a) !== $i)      {        $isList = false;        break;      }    }    $result = array();    if ($isList)    {      foreach ($a as $v) $result[] = json_encode($v);      return '[' . join(',', $result) . ']';    }    else    {      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);      return '{' . join(',', $result) . '}';    }  }}


$fileArray = array();
foreach ($_POST as $key => $value) {
	if ($key != 'folder') {
//		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $_POST['folder'] . '/' . $value)) {
		if (file_exists('../../images/cms/noticias/' . $value )) {
			$fileArray[$key] = $value;
		}
	}
}
echo json_encode($fileArray);

?>