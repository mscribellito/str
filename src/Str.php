<?php

/**
 * String
 *
 * This content is released under the The MIT License (MIT)
 *
 * Copyright (c) 2015 Michael Scribellito
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Str;

use ArrayAccess,
    Exception;

/**
 * String
 *
 * String is a PHP class that wraps the native string functions in an
 * effort to simplify string handling and manipulation. String provides methods
 * for examining individual characters of the string, for comparing strings, for
 * searching strings, for extracting substrings, and for creating a copy of a
 * string with all characters translated to uppercase or to lowercase. A String
 * is immutable (constant) and its value cannot be changed after creation.
 *
 * @author Michael Scribellito <mscribellito@gmail.com>
 * @copyright (c) 2015, Michael Scribellito
 * @link https://github.com/mscribellito/String
 */
class Str implements ArrayAccess {

    /**
     * Length of the string
     *
     * @var int
     */
    protected $length = 0;

    /**
     * Value of the string
     *
     * @var string
     */
    protected $value = "";

    /**
     * Initializes a newly created, empty String object.
     *
     * @param mixed $original a string
     * @param int $offset the initial offset
     * @param int $length the length
     * @throws StrIndexOutOfBoundsException
     */
    public function __construct($original = "", $offset = null, $length = null) {

        $value = (string) $original;

        if ($offset !== null && $length !== null) {
            if ($offset < 0) {
                throw new StrIndexOutOfBoundsException($offset);
            }
            if ($length < 0) {
                throw new StrIndexOutOfBoundsException($length);
            }
            if ($offset > strlen($value) - $length) {
                throw new StrIndexOutOfBoundsException($offset + $length);
            }
            $value = substr($value, $offset, $length);
        }

        $this->value = $value;

        $this->length = strlen($this->value);

    }

    /**
     * The value of this string is returned.
     *
     * @return string the string itself.
     */
    public function __toString() {

        return $this->valueOf();

    }

    /**
     * Returns the character at the specified index.
     *
     * @param int $index the index of the character
     * @return string the character at the specified index of this string.
     * @throws StrIndexOutOfBoundsException
     */
    public function charAt($index) {

        if ($index < 0 || $index >= $this->length()) {
            throw new StrIndexOutOfBoundsException($index);
        }

        return $this->value[$index];

    }

    /**
     * Returns the character ASCII value at the specified index.
     *
     * @param int $index the index to the character
     * @return int the ASCII value of the character at the index.
     * @throws StrIndexOutOfBoundsException
     */
    public function charCodeAt($index) {

        return ord($this->charAt($index));

    }

    /**
     * Compares two strings lexicographically. $str will be cast to a string.
     *
     * @param string|\Str $str the string to be compared
     * @param bool $ignoreCase if true, ignore case
     * @return int a negative integer, zero, or a positive integer as the specified
     * string is greater than, equal to, or less than this string.
     */
    public function compareTo($str, $ignoreCase = false) {

        $str = (string) $str;

        if ($ignoreCase === false) {
            return strcmp($this->value, $str);
        } else {
            return strcasecmp($this->value, $str);
        }

    }

    /**
     * Compares two strings lexicographically, ignoring case differences. $str 
     * will be cast to a string.
     *
     * @param string|\Str $str the string to be compared
     * @return int a negative integer, zero, or a positive integer as the specified
     * string is greater than, equal to, or less than this string.
     */
    public function compareToIgnoreCase($str) {

        return $this->compareTo($str, true);

    }

    /**
     * Concatenates the specified string(s) to the end of this string.
     *
     * @return \Str a string that represents the concatenation of this string
     * followed by the string argument(s).
     */
    public function concat() {

        $value = $this->value;

        for ($i = 0; $i < func_num_args(); $i++) {
            $value .= (string) func_get_arg($i);
        }

        return new static($value);

    }

    /**
     * Returns true if and only if this string contains the specified string.
     *
     * @param string $str the string to search for
     * @return bool true if this string contains str, false otherwise.
     */
    public function contains($str) {

        return $this->indexOf($str) >= 0;

    }

    /**
     * Tests if this string ends with the specified suffix.
     *
     * @param string $suffix the suffix
     * @param bool $ignoreCase if true, ignore case
     * @return bool true if the string ends with the specified suffix.
     */
    public function endsWith($suffix, $ignoreCase = false) {

        $pattern = "/" . preg_quote($suffix) . "$/";

        if ($ignoreCase === true) {
            $pattern .= "i";
        }

        return $this->matches($pattern);

    }

    /**
     * Compares this string to the specified string. $str will be cast to a string. 
     *
     * @param string|\Str $str the string to compare this string against
     * @param bool $ignoreCase if true, ignore case
     * @return bool true if the specified string is equivalent to this string,
     * false otherwise.
     */
    public function equals($str, $ignoreCase = false) {

        if ($ignoreCase === false) {
            return $this->compareTo($str) === 0;
        } else {
            return $this->compareToIgnoreCase($str) === 0;
        }

    }

    /**
     * Compares this string to the specified string, ignoring case considerations. 
     * $str will be cast to a string.
     *
     * @param string|\Str $str the string to compare this string against
     * @return bool true if the specified string is equivalent to this string,
     * false otherwise.
     */
    public function equalsIgnoreCase($str) {

        return $this->equals($str, true);

    }

    /**
     * Returns a formatted string using the specified format string and arguments.
     *
     * @param string $format a format string
     * @param mixed $args arguments referenced by the format specifiers in the format string.
     * @return \Str a formatted string.
     */
    public static function format($format) {

        if (func_num_args() === 1) {
            return new static($format);
        }

        return new static(call_user_func_array("sprintf", func_get_args()));

    }

    /**
     * Returns a string created by using the specified sequence of ASCII values.
     * 
     * @param int[] $code ASCII code(s).
     * @return \Str the string.
     */
    public static function fromCharCode($code) {

        $str = "";

        $args = is_array($code) ? $code : func_get_args();

        foreach ($args as $arg) {
            $str .= chr($arg);
        }

        return new static($str);

    }

    /**
     * Returns the index within this string of the first occurrence of the specified
     * string, optionally starting the search at the specified index.
     *
     * @param string $str a string
     * @param int $fromIndex the index to start the search from
     * @param bool $ignoreCase if true, ignore case
     * @return int the index of the first occurrence of the string, or -1 if the
     * string does not occur.
     */
    public function indexOf($str, $fromIndex = 0, $ignoreCase = false) {

        if ($fromIndex < 0) {
            $fromIndex = 0;
        } else if ($fromIndex >= $this->length()) {
            return -1;
        }

        if ($ignoreCase === false) {
            $index = strpos($this->value, $str, $fromIndex);
        } else {
            $index = stripos($this->value, $str, $fromIndex);
        }

        return $index === false ? -1 : $index;

    }

    /**
     * Returns the index within this string of the first occurrence of the specified
     * string, ignoring case considerations and optionally starting the search at
     * the specified index.
     *
     * @param string $str a string
     * @param int $fromIndex the index to start the search from
     * @return int the index of the first occurrence of the string, or -1 if the
     * string does not occur.
     */
    public function indexOfIgnoreCase($str, $fromIndex = 0) {

        return $this->indexOf($str, $fromIndex, true);

    }

    /**
     * Returns true if and only if length() is 0.
     *
     * @return bool true if length() is 0, otherwise false.
     */
    public function isEmpty() {

        return $this->length() === 0;

    }

    /**
     * Returns a new string composed of array elements joined together with the
     * specified delimiter.
     *
     * @param string $delimiter the delimiter that separates each element
     * @param string[] $elements the elements to join together
     * @return \Str a new string that is composed of the elements separated
     * by the delimiter.
     */
    public static function join($delimiter, $elements) {

        return new static(implode($delimiter, $elements));

    }

    /**
     * Returns the index within this string of the last occurrence of the specified
     * character, optionally starting the search at the specified index.
     *
     * @param string $str a string
     * @param int $fromIndex the index to start the search from
     * @param bool $ignoreCase if true, ignore case
     * @return int the index of the last occurrence of the string, or -1 if the
     * string does not occur.
     */
    public function lastIndexOf($str, $fromIndex = 0, $ignoreCase = false) {

        if ($fromIndex < 0) {
            $fromIndex = 0;
        } else if ($fromIndex >= $this->length()) {
            return -1;
        }

        if ($ignoreCase === false) {
            $index = strrpos($this->value, $str, $fromIndex);
        } else {
            $index = strripos($this->value, $str, $fromIndex);
        }

        return $index === false ? -1 : $index;

    }

    /**
     * Returns the index within this string of the last occurrence of the specified
     * character, ignoring case considerations and optionally starting the search
     * at the specified index.
     *
     * @param string $str a string
     * @param int $fromIndex the index to start the search from
     * @return int the index of the last occurrence of the string, or -1 if the
     * string does not occur.
     */
    public function lastIndexOfIgnoreCase($str, $fromIndex = 0) {

        return $this->lastIndexOf($str, $fromIndex, true);

    }

    /**
     * Returns the length of this string.
     *
     * @return int the length of the string.
     */
    public function length() {

        return $this->length;

    }

    /**
     * Tells whether or not this string matches the given regular expression.
     *
     * @param string $regex the regular expression to which this string is to be matched
     * @param \Str[] $matches array to be filled with results of search.
     * @return bool true if and only if this string matches the given regular expression.
     */
    public function matches($regex, & $matches = null) {

        $match = preg_match($regex, $this->value, $matches);

        for ($i = 0, $l = count($matches); $i < $l; $i++) {
            $matches[$i] = new static($matches[$i]);
        }

        return $match === 1;

    }

    /**
     * Returns whether or not a character exists at the specified index.
     * Implements part of the ArrayAccess interface.
     *
     * @param int $offset the index
     * @return boolean true if character exists at the specified index.
     */
    public function offsetExists($offset) {

        return $offset >= 0 && $this->length() > $offset;

    }

    /**
     * Returns the character at the specified index. Implements part of the
     * ArrayAccess interface.
     *
     * @param int $offset the index
     * @return string the character at the specified index.
     * @throws StrIndexOutOfBoundsException
     */
    public function offsetGet($offset) {

        return $this->charAt($offset);

    }

    /**
     * Implements part of the ArrayAccess interface. Throws an exception because
     * Strings are immutable.
     *
     * @param int $offset n/a
     * @param string $value n/a
     * @throws Exception
     */
    public function offsetSet($offset, $value) {

        throw new Exception("Strings are immutable");

    }

    /**
     * Implements part of the ArrayAccess interface. Throws an exception because
     * Strings are immutable.
     *
     * @param int $offset n/a
     * @throws Exception
     */
    public function offsetUnset($offset) {

        throw new Exception("Strings are immutable");

    }

    # start:comments

    /**
     * Pads the left side of this string to a specified length with another string.
     *
     * @param int $length the length of the padded string
     * @param string $str the string to pad with
     * @return \Str the left padded string.
     */
    public function padLeft($length, $str) {

        return new static(str_pad($this->value, $length, $str, STR_PAD_LEFT));

    }

    /**
     * Pads the right side of this string to a specified length with another string.
     *
     * @param int $length the length of the padded string
     * @param string $str the string to pad with
     * @return \Str the right padded string.
     */
    public function padRight($length, $str) {

        return new static(str_pad($this->value, $length, $str, STR_PAD_RIGHT));

    }

    /**
     * Compares two string regions lexicographically.
     *
     * @param int $toffset the starting offset of the subregion in this string
     * @param string $str the string argument
     * @param int $ooffset the starting offset of the subregion in the string argument
     * @param int $length the number of characters to compare
     * @param bool $ignoreCase if true, ignore case
     * @return int a negative integer, zero, or a positive integer as the specified
     * string is greater than, equal to, or less than this string.
     * @throws StrIndexOutOfBoundsException
     */
    public function regionCompare($toffset, $str, $ooffset, $length, $ignoreCase = false) {

        $other = new static($str);

        if ($ignoreCase === false) {
            return strncmp($this->substring($toffset), $other->substring($ooffset), $length);
        } else {
            return strncasecmp($this->substring($toffset), $other->substring($ooffset), $length);
        }

    }

    /**
     * Compares two string regions lexicographically, ignoring case differences.
     *
     * @param int $toffset the starting offset of the subregion in this string
     * @param string $str the string argument
     * @param int $ooffset the starting offset of the subregion in the string argument
     * @param int $length the number of characters to compare
     * @return int a negative integer, zero, or a positive integer as the specified
     * string is greater than, equal to, or less than this string.
     * @throws StrIndexOutOfBoundsException
     */
    public function regionCompareIgnoreCase($toffset, $str, $ooffset, $length) {

        return $this->regionCompare($toffset, $str, $ooffset, $length, true);

    }

    /**
     * Tests if two string regions are equal.
     *
     * @param int $toffset the starting offset of the subregion in this string
     * @param string $str the string argument
     * @param int $ooffset the starting offset of the subregion in the string argument
     * @param int $length the number of characters to compare
     * @return bool true if the specified subregion of this string matches the
     * specified subregion of the string argument; false otherwise.
     * @throws StrIndexOutOfBoundsException
     */
    public function regionMatches($toffset, $str, $ooffset, $length) {

        return $this->regionCompare($toffset, $str, $ooffset, $length) === 0;

    }

    /**
     * Tests if two string regions are equal, ignoring case differences.
     *
     * @param int $toffset the starting offset of the subregion in this string
     * @param string $str the string argument
     * @param int $ooffset the starting offset of the subregion in the string argument
     * @param int $length the number of characters to compare
     * @return bool true if the specified subregion of this string matches the
     * specified subregion of the string argument; false otherwise.
     * @throws StrIndexOutOfBoundsException
     */
    public function regionMatchesIgnoreCase($toffset, $str, $ooffset, $length) {

        return $this->regionCompareIgnoreCase($toffset, $str, $ooffset, $length) === 0;

    }

    /**
     * Returns a string resulting from replacing all occurrences of old in this
     * string with new.
     *
     * @param string $old the string to be replaced
     * @param string $new the replacement string
     * @param int $count this will be set to the number of replacements performed
     * @return \Str the resulting string.
     */
    public function replace($old, $new, & $count = 0) {

        return new static(str_replace($old, $new, $this->value, $count));

    }

    /**
     * Replaces each substring of this string that matches the given regular
     * expression with the given replacement.
     *
     * @param string $regex the regular expression to which this string is to be matched
     * @param string $replacement the string to be substituted for each match
     * @param int $limit the maximum possible replacements for each pattern
     * @param int $count this will be set to the number of replacements performed
     * @return \Str the resulting string.
     */
    public function replaceAll($regex, $replacement, $limit = null, & $count = 0) {

        if ($limit === null) {
            $limit = -1;
        }

        return new static(preg_replace($regex, $replacement, $this->value, $limit, $count));

    }

    /**
     * Replaces the first substring of this string that matches the given regular
     * expression with the given replacement.
     *
     * @param string $regex the regular expression to which this string is to be matched
     * @param string $replacement the string to be substituted for each match
     * @return \Str the resulting string.
     */
    public function replaceFirst($regex, $replacement) {

        return $this->replaceAll($regex, $replacement, 1);

    }

    /**
     * Returns a string resulting from replacing all occurrences of old in this
     * string with new, ignoring case differences.
     *
     * @param string $old the string to be replaced
     * @param string $new the replacement string
     * @param int $count this will be set to the number of replacements performed
     * @return \Str the resulting string.
     */
    public function replaceIgnoreCase($old, $new, & $count = 0) {

        return new static(str_ireplace($old, $new, $this->value, $count));

    }

    /**
     * Reverses this string.
     *
     * @return \Str the reversed string.
     */
    public function reverse() {

        return new static(strrev($this->value));

    }

    /**
     * Splits this string around matches of the given regular expression.
     *
     * @param string $regex the delimiting regular expression
     * @param int $limit the result threshold
     * @return \Str[] the array of strings computed by splitting this string around
     * matches of the given regular expression.
     */
    public function split($regex, $limit = -1) {

        $parts = preg_split($regex, $this->value, $limit);

        for ($i = 0, $l = count($parts); $i < $l; $i++) {
            $parts[$i] = new static($parts[$i]);
        }

        return $parts;

    }

    /**
     * Tests if this string starts with the specified prefix.
     *
     * @param string $prefix the prefix
     * @param int $fromIndex the index to start the search from
     * @param bool $ignoreCase if true, ignore case
     * @return bool true if the string starts with the specified prefix.
     * @throws StrIndexOutOfBoundsException
     */
    public function startsWith($prefix, $fromIndex = 0, $ignoreCase = false) {

        $pattern = "/^" . preg_quote($prefix) . "/";

        if ($ignoreCase === true) {
            $pattern .= "i";
        }

        return $this->substring($fromIndex)->matches($pattern);

    }

    /**
     * Returns a string that is a substring of this string.
     *
     * @param int $beginIndex the beginning index, inclusive
     * @param int $endIndex the ending index, exclusive
     * @return \Str the specified substring.
     * @throws StrIndexOutOfBoundsException
     */
    public function substring($beginIndex, $endIndex = null) {

        if ($beginIndex < 0) {
            throw new StrIndexOutOfBoundsException($beginIndex);
        } else if ($beginIndex === $this->length()) {
            return new static("");
        }

        if ($endIndex === null) {
            $length = $this->length() - $beginIndex;
            if ($length < 0) {
                throw new StrIndexOutOfBoundsException($length);
            }
            if ($beginIndex === 0) {
                return $this;
            } else {
                return new static($this->value, $beginIndex, $length);
            }
        } else {
            if ($endIndex > $this->length()) {
                throw new StrIndexOutOfBoundsException($endIndex);
            }
            $length = $endIndex - $beginIndex;
            if ($length < 0) {
                throw new StrIndexOutOfBoundsException($length);
            }
            if ($beginIndex === 0 && $endIndex === $this->length()) {
                return $this;
            } else {
                return new static($this->value, $beginIndex, $length);
            }
        }

    }

    /**
     * Converts this string to a new character array.
     *
     * @return string[] a character array whose length is the length of this string
     * and whose contents are initialized to contain the character sequence
     * represented by this string.
     */
    public function toCharArray() {

        return str_split($this->value, 1);

    }

    /**
     * Converts all of the characters in this string to lower case.
     *
     * @return \Str the string, converted to lowercase.
     */
    public function toLowerCase() {

        return new static(strtolower($this->value));

    }

    /**
     * Converts all of the characters in this string to upper case.
     *
     * @return \Str the string, converted to uppercase.
     */
    public function toUpperCase() {

        return new static(strtoupper($this->value));

    }

    /**
     * Returns a string whose value is this string, with any leading and trailing
     * whitespace removed.
     *
     * @param string $characterMask characters to strip
     * @return \Str a string whose value is this string, with any leading and
     * trailing white space removed, or this string if it has no leading or trailing
     * white space.
     */
    public function trim($characterMask = " \t\n\r\0\x0B") {

        return new static(trim($this->value, $characterMask));

    }

    /**
     * Returns a string whose value is this string, with any leading whitespace removed.
     *
     * @param string $characterMask characters to strip
     * @return \Str a string whose value is this string, with any leading white
     * space removed, or this string if it has no leading white space.
     */
    public function trimLeft($characterMask = " \t\n\r\0\x0B") {

        return new static(ltrim($this->value, $characterMask));

    }

    /**
     * Returns a string whose value is this string, with any trailing whitespace
     * removed.
     *
     * @param string $characterMask characters to strip
     * @return \Str a string whose value is this string, with any trailing white
     * space removed, or this string if it has no trailing white space.
     */
    public function trimRight($characterMask = " \t\n\r\0\x0B") {

        return new static(rtrim($this->value, $characterMask));

    }

    /**
     * The value of this string is returned.
     *
     * @return string the string itself.
     */
    public function valueOf() {

        return $this->value;

    }

}

/**
 * Thrown by String methods to indicate that an index is either negative or
 * greater than the size of the string.
 */
class StrIndexOutOfBoundsException extends Exception {

    /**
     * Constructs a new StrIndexOutOfBoundsException class with an argument
     * indicating the illegal index.
     *
     * @param int $index the illegal index
     */
    public function __construct($index) {

        parent::__construct("String index out of range: " . $index, 0, null);

    }

}
