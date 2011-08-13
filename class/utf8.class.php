<?php

/**
  * UTF-8 friendly replacement functions - v0.2
  * Copyright (C) 2004-2006 Niels Leenheer & Andy Matsubara
  *
  * This program is free software; you can redistribute it and/or
  * modify it under the terms of the GNU General Public License
  * as published by the Free Software Foundation; either version 2
  * of the License, or (at your option) any later version.
  *
  *	Supported functions:
  * - utf8::convert()
  * - utf8::detect()
  * - utf8::strtolower()
  * - utf8::strtoupper()
  * - utf8::strlen()
  * - utf8::strwidth()
  * - utf8::substr()
  * - utf8::strimwidth()
  * - utf8::strcut()
  * - utf8::strrpos()
  * - utf8::strpos()
  * - utf8::substr_count()
  * - utf8::encode_mimeheader()
  * - utf8::send_mail()
  * - utf8::encode_javascript()
  * - utf8::encode_numericentity()
  *
  * @package core
  */

// force UTF-8 Ã?

$_zp_UTF8 = new utf8();

class utf8 {
	/**
	 * Character set translation support
	 *
	 * @return utf8
	 */
	
	var $charsets;
	var $validsets;
	var $mb_sets;
	var $iconv_sets;
	
	function utf8() {
		$this->charsets = array(	"ASMO-708" => "Arabic",
															"BIG5" => "Chinese Traditional",
															"CP1026" => "IBM EBCDIC (Turkish Latin-5)",
															"cp866" => "Cyrillic (DOS)",
															"CP870" => "IBM EBCDIC (Multilingual Latin-2)",
															"CISO2022JP" => "Japanese (JIS-Allow 1 byte Kana)",
															"DOS-720" => "Arabic (DOS)",
															"DOS-862" => "Hebrew (DOS)",
															"EBCDIC-CP-US" => "IBM EBCDIC (US-Canada)",
															"EUC-CN" => "Chinese Simplified (EUC)",
															"EUC-JP" => "Japanese (EUC)",
															"EUC-KR" => "Korean (EUC)",
															"GB2312" => "Chinese Simplified (GB2312)",
															"HZ-GB-2312" => "Chinese Simplified (HZ)",
															"IBM437" => "OEM United States",
															"IBM737" => "Greek (DOS)",
															"IBM775" => "Baltic (DOS)",
															"IBM850" => "Western European (DOS)",
															"IBM852" => "Central European (DOS)",
															"IBM857" => "Turkish (DOS)",
															"IBM861" => "Icelandic (DOS)",
															"IBM869" => "Greek, Modern (DOS)",
															"ISO-2022-JP" => "Japanese (JIS)",
															"ISO-2022-JP" => "Japanese (JIS-Allow 1 byte Kana - SO/SI)",
															"ISO-2022-KR" => "Korean (ISO)",
															"ISO-8859-1" => "Western European (ISO)",
															"ISO-8859-15" => "Latin 9 (ISO)",
															"ISO-8859-2" => "Central European (ISO)",
															"ISO-8859-3" => "Latin 3 (ISO)",
															"ISO-8859-4" => "Baltic (ISO)",
															"ISO-8859-5" => "Cyrillic (ISO)",
															"ISO-8859-6" => "Arabic (ISO)",
															"ISO-8859-7" => "Greek (ISO)",
															"ISO-8859-8" => "Hebrew (ISO-Visual)",
															"ISO-8859-8-i" => "Hebrew (ISO-Logical)",
															"ISO-8859-9" => "Turkish (ISO)",
															"JOHAB" => "Korean (Johab)",
															"KOi8-R" => "Cyrillic (KOI8-R)",
															"KOi8-U" => "Cyrillic (KOI8-U)",
															"KS_C_5601-1987" => "Korean",
															"MACINTOSH" => "Western European (MAC)",
															"SHIFT_JIS" => "Japanese (Shift-JIS)",
															"UNICODE" => "Unicode",
															"UNICODEFFFE" => "Unicode (Big-Endian)",
															"US-ASCII" => "US-ASCII",
															"UTF-7" => "Unicode (UTF-7)",
															"UTF-8" => "Unicode (UTF-8)",
															"WINDOWS-1250" => "Central European (Windows)",
															"WINDOWS-1251" => "Cyrillic (Windows)",
															"WINDOWS-1252" => "Western European (Windows)",
															"WINDOWS-1253" => "Greek (Windows)",
															"WINDOWS-1254" => "Turkish (Windows)",
															"WINDOWS-1255" => "Hebrew (Windows)",
															"WINDOWS-1256" => "Arabic (Windows)",
															"WINDOWS-1257" => "Baltic (Windows)",
															"WINDOWS-1258" => "Vietnamese (Windows)",
															"WINDOWS-874" => "Thai (Windows)",
															"X-CHINESE-CNS" => "Chinese Traditional (CNS)",
															"X-CHINESE-ETEN" => "Chinese Traditional (Eten)",
															"X-EBCDIC-Arabic" => "IBM EBCDIC (Arabic)",
															"X-EBCDIC-CP-US-EURO" => "IBM EBCDIC (US-Canada-Euro)",
															"X-EBCDIC-CYRILLICRUSSIAN" => "IBM EBCDIC (Cyrillic Russian)",
															"X-EBCDIC-CYRILLICSERBIANBULGARIAN" => "IBM EBCDIC (Cyrillic Serbian-Bulgarian)",
															"X-EBCDIC-DENMARKNORWAY" => "IBM EBCDIC (Denmark-Norway)",
															"X-EBCDIC-DENMARKNORWAY-euro" => "IBM EBCDIC (Denmark-Norway-Euro)",
															"X-EBCDIC-FINLANDSWEDEN" => "IBM EBCDIC (Finland-Sweden)",
															"X-EBCDIC-FINLANDSWEDEN-EURO" => "IBM EBCDIC (Finland-Sweden-Euro)",
															"X-EBCDIC-FINLANDSWEDEN-EURO" => "IBM EBCDIC (Finland-Sweden-Euro)",
															"X-EBCDIC-FRANCE-EURO" => "IBM EBCDIC (France-Euro)",
															"X-EBCDIC-GERMANY" => "IBM EBCDIC (Germany)",
															"X-EBCDIC-GERMANY-EURO" => "IBM EBCDIC (Germany-Euro)",
															"X-EBCDIC-GREEK" => "IBM EBCDIC (Greek)",
															"X-EBCDIC-GREEKMODERN" => "IBM EBCDIC (Greek Modern)",
															"X-EBCDIC-HEBREW" => "IBM EBCDIC (Hebrew)",
															"X-EBCDIC-ICELANDIC" => "IBM EBCDIC (Icelandic)",
															"X-EBCDIC-ICELANDIC-EURO" => "IBM EBCDIC (Icelandic-Euro)",
															"X-EBCDIC-INTERNATIONAL-EURO" => "IBM EBCDIC (International-Euro)",
															"X-EBCDIC-ITALY" => "IBM EBCDIC (Italy)",
															"X-EBCDIC-ITALY-EURO" => "IBM EBCDIC (Italy-Euro)",
															"X-EBCDIC-JAPANESEANDJAPANESELATIN" => "IBM EBCDIC (Japanese and Japanese-Latin)",
															"X-EBCDIC-JAPANESEANDKANA" => "IBM EBCDIC (Japanese and Japanese Katakana)",
															"X-EBCDIC-JAPANESEANDUSCANADA" => "IBM EBCDIC (Japanese and US-Canada)",
															"X-EBCDIC-JAPANESEKATAKANA" => "IBM EBCDIC (Japanese katakana)",
															"X-EBCDIC-KOREANANDKOREANEXTENDED" => "IBM EBCDIC (Korean and Korean EXtended)",
															"X-EBCDIC-KOREANEXTENDED" => "IBM EBCDIC (Korean EXtended)",
															"X-EBCDIC-SIMPLIFIEDCHINESE" => "IBM EBCDIC (Simplified Chinese)",
															"X-EBCDIC-SPAIN" => "IBM EBCDIC (Spain)",
															"X-ebcdic-SPAIN-EURO" => "IBM EBCDIC (Spain-Euro)",
															"X-EBCDIC-THAI" => "IBM EBCDIC (Thai)",
															"X-EBCDIC-TRADITIONALCHINESE" => "IBM EBCDIC (Traditional Chinese)",
															"X-EBCDIC-TURKISH" => "IBM EBCDIC (Turkish)",
															"X-EBCDIC-UK" => "IBM EBCDIC (UK)",
															"X-EBCDIC-UK-EURO" => "IBM EBCDIC (UK-Euro)",
															"X-EUROPA" => "Europa",
															"X-IA5" => "Western European (IA5)",
															"X-IA5-GERMAN" => "German (IA5)",
															"X-IA5-NORWEGIAN" => "Norwegian (IA5)",
															"X-IA5-SWEDISH" => "Swedish (IA5)",
															"X-ISCII-AS" => "ISCII Assamese",
															"X-ISCII-BE" => "ISCII Bengali",
															"X-ISCII-DE" => "ISCII Devanagari",
															"X-ISCII-GU" => "ISCII Gujarathi",
															"X-ISCII-KA" => "ISCII Kannada",
															"X-ISCII-MA" => "ISCII Malayalam",
															"X-ISCII-OR" => "ISCII Oriya",
															"X-ISCII-PA" => "ISCII Panjabi",
															"X-ISCII-TA" => "ISCII Tamil",
															"X-ISCII-TE" => "ISCII Telugu",
															"X-MAC-ARABIC" => "Arabic (Mac)",
															"X-MAC-CE" => "Central European (Mac)",
															"X-MAC-CHINESESIMP" => "Chinese Simplified (Mac)",
															"X-MAC-CHINESETRAD" => "Chinese Traditional (Mac)",
															"X-MAC-CYRILLIC" => "Cyrillic (Mac)",
															"X-MAC-GREEK" => "Greek (Mac)",
															"X-MAC-HEBREW" => "Hebrew (Mac)",
															"X-MAC-ICELANDIC" => "Icelandic (Mac)",
															"X-MAC-JAPANESE" => "Japanese (Mac)",
															"X-MAC-KOREAN" => "Korean (Mac)",
															"X-MAC-TURKISH" => "Turkish (Mac)"
															);
		// prune the list to supported character sets
		if (function_exists('mb_convert_encoding')) {
			$list = mb_list_encodings();
			foreach ($this->charsets as $key=>$encoding) {
				if (in_array($key, $list)) {
					$this->mb_sets[$key] = $encoding;
				}
			}
		}
		if (function_exists('iconv')) {
			foreach ($this->charsets as $key=>$encoding) {
				if (@iconv("UTF-8", $key, " ")!==false) {
					$this->iconv_sets[$key] = $encoding;
				}
			}
		}
		$this->validsets = array_merge($this->mb_sets, $this->iconv_sets);
	}

	/**
	 * Convert a foreign charset encoding from or to UTF-8
	 */
	function convert($string, $encoding = '', $destination = 'UTF-8') {
		if ($encoding == '') $encoding = utf8::detect($string);
		if ($encoding == $destination) return $string; 
		
		$encode_mb = array_key_exists($encoding, $this->mb_sets);
		$encode_iconv = array_key_exists($encoding, $this->iconv_sets);
		$dest_mb = array_key_exists($destination, $this->mb_sets);
		$dest_iconv = array_key_exists($destination, $this->iconv_sets);
		
		if ($encode_mb && $dest_mb) {
			@mb_substitute_character('none');
			return @mb_convert_encoding($string, $destination, $encoding );
		}
		if ($encode_iconv && $dest_iconv) {
			return @iconv($encoding, $destination . '//IGNORE', $string);
		}
		// must use mixed conversion
		@mb_substitute_character('none');
		if ($encode_mb) {
			$instring = @mb_convert_encoding($string, 'UTF-8', $encoding);
		} else if ($encode_iconv) {
			$instring = @iconv($encoding, 'UTF-8' . '//IGNORE', $string);
		} else  {
			$instring = $string;
		}
		if ($dest_mb) {
			$outstring = @mb_convert_encoding($string, $destination, 'UTF-8');
		} else if ($dest_iconv) {
			$outstring = @iconv('UTF-8', $destination . '//IGNORE', $string);
		} else {
			$outstring = $string;
		}
		return $outstring;
	}

	/**
	 * Detect the encoding of the string
	 */
	function detect($string) {
		if (function_exists('mb_detect_encoding')) return mb_detect_encoding($string);
		if (!ereg("[\x80-\xFF]", $string) && !ereg("\x1B", $string))
			return 'US-ASCII';

		if (!ereg("[\x80-\xFF]", $string) && ereg("\x1B", $string))
			return 'ISO-2022-JP';

		if (preg_match("/^([\x01-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF][\x80-\xBF])+$/", $string) == 1)
			return 'UTF-8';

		if (preg_match("/^([\x01-\x7F]|\x8E[\xA0-\xDF]|\x8F[xA1-\xFE][\xA1-\xFE]|[\xA1-\xFE][\xA1-\xFE])+$/", $string) == 1)
			return 'EUC-JP';

		if (preg_match("/^([\x01-\x7F]|[\xA0-\xDF]|[\x81-\xFC][\x40-\xFC])+$/", $string) == 1)
			return 'Shift_JIS';

		return 'ISO-8859-1';
	}


	/**
	 * Determine the number of characters of a string
	 * Compatible with mb_strlen(), an UTF-8 friendly replacement for strlen()
	 */
	function strlen($str) {
		return preg_match_all('/[\x01-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF][\x80-\xBF]/', $str, $arr);
	}

	/**
	 * Count the number of substring occurances
	 * Compatible with mb_substr_count(), an UTF-8 friendly replacement for substr_count()
	 */
	function substr_count($haystack, $needle) {
		return substr_count($haystack, $needle);
	}

	/**
	 * Return part of a string, length and offset in characters
	 * Compatible with mb_substr(), an UTF-8 friendly replacement for substr()
	 */
	function substr($str, $start , $length = NULL) {
		preg_match_all('/[\x01-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF][\x80-\xBF]/', $str, $arr);

		if (is_int($length))
			return implode('', array_slice($arr[0], $start, $length));
		else
			return implode('', array_slice($arr[0], $start));
	}

	/**
	 * Return part of a string, length and offset in bytes
	 * Compatible with mb_strcut()
	 */
	function strcut($str, $start, $length = NULL) {
		if ($start < 0)	$start += strlen($str);
		$original = $start;
		while ($start > 0 && intval(ord($str[$start]) & 0xC0) == 0x80)
			$start--;

		$start = max($start, 0);
		$original = max($original, 0);

		if ($start < strlen($str))
		{
			if (is_null($length)) {
				return substr($str, $start);
			}
			elseif ($length > 0) {
				$end = $start + $length;

				while ($end > 0 && intval(ord($str[$end]) & 0xC0) == 0x80)
					$end--;

				return substr($str, $start, $end - $start);
			}
			elseif ($length < 0) {
				$end = strlen($str) + $length - ($original - $start);

				while ($end > 0 && intval(ord($str[$end]) & 0xC0) == 0x80)
					$end--;

				if ($end > 0)
					return substr($str, $start, $end - $start);
			}
		}

		return '';
	}

	/**
	 * Determine the width of a string
	 * Compatible with mb_strwidth()
	 */
	function strwidth($str) {
		$double = preg_match_all('/[\xE2-\xEF][\x80-\xBF][\x80-\xBF]/', $str, $arr) - 			// U+2000 - U+FFFF = double width
				  preg_match_all('/\xEF\xBD[\xA1-\xBF]|\xEF\xBE[\x80-\x9F]/', $str, $arr);		// U+FF61 - U+FF9F = single width
		$null   = preg_match_all('/[\x00-\x19]/', $str, $arr);									// U+0000 - U+0019 = no width

		return UTF8::strlen($str) - $null + $double;
	}

	/**
	 * Get truncated string with specified width
	 * Compatible with mb_strimwidth()
	 */
	function strimwidth($str, $start, $width, $trimmarker = '') {

		$str   = UTF8::substr($str, $start);
		$width = $width - UTF8::strwidth($trimmarker);

		for ($i = 0; $i < strlen($str); $i++)
		{
			$b1 = (int)ord($str[$i]);

			if ($b1 < 0x80 || $b1 > 0xBF)
			{
				$c++;

				if ($b1 > 0xE2)
				{
					$b2 = (int)ord($str[$i + 1]);
					$b3 = (int)ord($str[$i + 2]);

					if (($b2 == 0xBD && $b3 >= 0xA1) || ($b2 == 0xBE && $b3 <= 0x9F))
						$count++;
					else
						$count = $count + 2;
				}
				else
					$count++;
			}

			if ($count > $width) {
				return UTF8::substr($str, 0, $c - 1) . $trimmarker;
			}
		}
	}

	/**
	 * Find position of last occurance of a string in another string
	 * Compatible with mb_strrpos(), an UTF-8 friendly replacement for strrpos()
	 */
	function strrpos($haystack, $needle) {
		$pos = strrpos($haystack, $needle);

		if ($pos === false)
			return false;
		else
			return UTF8::strlen(substr($haystack, 0, $pos));
	}

	/**
	 * Find position of first occurance of a string in another string
	 * Compatible with mb_strpos(), an UTF-8 friendly replacement for strpos()
	 */
	function strpos($haystack, $needle, $offset = 0) {
		$comp = 0;

		while (!isset($length) || $length < $offset)
		{
			$pos = strpos($haystack, $needle, $offset + $comp);
			if ($pos === false) return false;
			$length = utf_strlen(substr($haystack, 0, $pos));
			if ($length < $offset) $comp = $pos - $length;
		}

		return $length;
	}

	/**
	 * Convert a string to lower case
	 * Compatible with mb_strtolower(), an UTF-8 friendly replacement for strtolower()
	 */
	function strtolower($str) {
		global $UTF8_TABLES;
		return strtr($str, $UTF8_TABLES['strtolower']);
	}

	/**
	 * Convert a string to upper case
	 * Compatible with mb_strtoupper(), an UTF-8 friendly replacement for strtoupper()
	 */
	function strtoupper($str) {
		global $UTF8_TABLES;
		return strtr($str, $UTF8_TABLES['strtoupper']);
	}

	/**
	 * Encode a string for use in a MIME header
	 * Simplied replacement for mb_encode_mimeheader()
	 */
	function encode_mimeheader($str) {
		$length = 45; $pos = 0; $max = strlen($str);
    $buffer = '';
		while ($pos < $max)
		{
			if ($pos + $length < $max)
			{
				$adjust = 0;

				while (intval(ord($str[$pos + $length + $adjust]) & 0xC0) == 0x80)
					$adjust--;

				$buffer .= ($buffer == '' ? '' : "?=\n =?UTF-8?B?") . base64_encode(substr($str, $pos, $length + $adjust));
				$pos = $pos + $length + $adjust;
			}
			else
			{
				$buffer .= ($buffer == '' ? '' : "?=\n =?UTF-8?B?") . base64_encode(substr($str, $pos));
				$pos = $max;
			}
		}

		return '=?UTF-8?B?' . $buffer . '?=';
	}

	/**
	 * Send mail
	 * Replacement for mb_send_mail(), an UTF-8 friendly replacement for mail()
	 */
	function send_mail($to, $subject, $message , $additional_headers = '', $additional_parameter = '') {
		$subject = UTF8::encode_mimeheader($subject);
		$message = chunk_split(base64_encode($message));

		$additional_headers = trim($additional_headers);

		if ($additional_headers != '')
			$additional_headers .= "\n";

		$additional_headers .=
			"Mime-Version: 1.0\n" .
			"Content-Type: text/plain; charset=UTF-8\n" .
			"Content-Transfer-Encoding: base64";

		if(ini_get('safe_mode')) 
		{
			@mail($to, $subject, $message, $additional_headers); 
		}
		else
		{
			@mail($to, $subject, $message, $additional_headers, $additional_parameter);
		}
	}

	/**
	 * Prepare an UTF-8 string for use in JavaScript
	 */
	function encode_javascript($string)
	{
		$string = str_replace ('\\', '\\\\', $string);
		$string = str_replace ('"', '\\"', $string);
		$string = str_replace ("'", "\\'", $string);
		$string = str_replace ("\n", "\\n", $string);
		$string = str_replace ("\r", "\\r", $string);
		$string = str_replace ("\t", "\\t", $string);

		$len = strlen ($string);
		$pos = 0;
		$out = '';

		while ($pos < $len)
		{
			$ascii = ord (substr ($string, $pos, 1));

			if ($ascii >= 0xF0)
			{
				$byte[1] = ord(substr ($string, $pos, 1)) - 0xF0;
				$byte[2] = ord(substr ($string, $pos + 1, 1)) - 0x80;
				$byte[3] = ord(substr ($string, $pos + 2, 1)) - 0x80;
				$byte[4] = ord(substr ($string, $pos + 3, 1)) - 0x80;


				$char_code = ($byte[1] << 18) + ($byte[2] << 12) + ($byte[3] << 6) + $byte[4];
				$pos += 4;
			}
			elseif (($ascii >= 0xE0) && ($ascii < 0xF0))
			{
				$byte[1] = ord(substr ($string, $pos, 1)) - 0xE0;
				$byte[2] = ord(substr ($string, $pos + 1, 1)) - 0x80;
				$byte[3] = ord(substr ($string, $pos + 2, 1)) - 0x80;

				$char_code = ($byte[1] << 12) + ($byte[2] << 6) + $byte[3];
				$pos += 3;
			}
			elseif (($ascii >= 0xC0) && ($ascii < 0xE0))
			{
				$byte[1] = ord(substr ($string, $pos, 1)) - 0xC0;
				$byte[2] = ord(substr ($string, $pos + 1, 1)) - 0x80;

				$char_code = ($byte[1] << 6) + $byte[2];
				$pos += 2;
			}
			else
			{
				$char_code = ord(substr ($string, $pos, 1));
				$pos += 1;
			}

			if ($char_code < 0x80)
				$out .= chr($char_code);
			else
				$out .=  '\\u'. str_pad(dechex($char_code), 4, '0', STR_PAD_LEFT);
		}

		return $out;
	}

	/**
	 * Encode an UTF-8 string with numeric entities
	 * Simplied replacement for mb_encode_numericentity()
	 */
	function encode_numericentity($string)
	{
		$len = strlen ($string);
		$pos = 0;
		$out = '';

		while ($pos < $len)
		{
			$ascii = ord (substr ($string, $pos, 1));

			if ($ascii >= 0xF0)
			{
				$byte[1] = ord(substr ($string, $pos, 1)) - 0xF0;
				$byte[2] = ord(substr ($string, $pos + 1, 1)) - 0x80;
				$byte[3] = ord(substr ($string, $pos + 2, 1)) - 0x80;
				$byte[4] = ord(substr ($string, $pos + 3, 1)) - 0x80;

				$char_code = ($byte[1] << 18) + ($byte[2] << 12) + ($byte[3] << 6) + $byte[4];
				$pos += 4;
			}
			elseif (($ascii >= 0xE0) && ($ascii < 0xF0))
			{
				$byte[1] = ord(substr ($string, $pos, 1)) - 0xE0;
				$byte[2] = ord(substr ($string, $pos + 1, 1)) - 0x80;
				$byte[3] = ord(substr ($string, $pos + 2, 1)) - 0x80;

				$char_code = ($byte[1] << 12) + ($byte[2] << 6) + $byte[3];
				$pos += 3;
			}
			elseif (($ascii >= 0xC0) && ($ascii < 0xE0))
			{
				$byte[1] = ord(substr ($string, $pos, 1)) - 0xC0;
				$byte[2] = ord(substr ($string, $pos + 1, 1)) - 0x80;

				$char_code = ($byte[1] << 6) + $byte[2];
				$pos += 2;
			}
			else
			{
				$char_code = ord(substr ($string, $pos, 1));
				$pos += 1;
			}

			if ($char_code < 0x80)
				$out .= chr($char_code);
			else
				$out .=  '&#'. str_pad($char_code, 5, '0', STR_PAD_LEFT) . ';';
		}

		return $out;
	}
}

/*******************************************************************************************************/

global $UTF8_TABLES;

$UTF8_TABLES['strtolower'] = array(
"ï¼º"=>"ï½?","ï¼¹"=>"ï½?","ï¼¸"=>"ï½?","ï¼·"=>"ï½?","ï¼¶"=>"ï½?","ï¼µ"=>"ï½?",
"ï¼´"=>"ï½?","ï¼³"=>"ï½?","ï¼²"=>"ï½?","ï¼±"=>"ï½?","ï¼°"=>"ï½","ï¼¯"=>"ï½",
"ï¼®"=>"ï½?","ï¼­"=>"ï½","ï¼¬"=>"ï½?","ï¼«"=>"ï½?","ï¼ª"=>"ï½?","ï¼©"=>"ï½?",
"ï¼¨"=>"ï½?","ï¼§"=>"ï½?","ï¼¦"=>"ï½?","ï¼¥"=>"ï½?","ï¼¤"=>"ï½?","ï¼£"=>"ï½?",
"ï¼¢"=>"ï½?","ï¼¡"=>"ï½","â?«"=>"Ã¥","â?ª"=>"k","â?¦"=>"Ï?","á¿»"=>"á½½",
"á¿º"=>"á½¼","á¿¹"=>"á½¹","á¿¸"=>"á½¸","á¿¬"=>"á¿¥","á¿«"=>"á½»","á¿ª"=>"á½º",
"á¿©"=>"á¿¡","á¿¨"=>"ï¿½ ","á¿?"=>"á½·","á¿?"=>"á½¶","á¿?"=>"á¿?","á¿?"=>"á¿",
"á¿?"=>"á½µ","á¿?"=>"á½´","á¿?"=>"á½³","á¿?"=>"á½²","á¾»"=>"á½±","á¾º"=>"á½°",
"á¾¹"=>"á¾±","á¾¸"=>"á¾°","á½¯"=>"á½§","á½®"=>"á½¦","á½­"=>"á½¥","á½¬"=>"á½¤",
"á½«"=>"á½£","á½ª"=>"á½¢","á½©"=>"á½¡","á½¨"=>"ï¿½ ","á½?"=>"á½?","á½"=>"á½?",
"á½?"=>"á½?","á½?"=>"á½?","á½"=>"á½?","á½?"=>"á½?","á½?"=>"á½?","á½?"=>"á½?",
"á½?"=>"á½","á½?"=>"á½?","á¼¿"=>"á¼·","á¼¾"=>"á¼¶","á¼½"=>"á¼µ","á¼¼"=>"á¼´",
"á¼»"=>"á¼³","á¼º"=>"á¼²","á¼¹"=>"á¼±","á¼¸"=>"á¼°","á¼¯"=>"á¼§","á¼®"=>"á¼¦",
"á¼­"=>"á¼¥","á¼¬"=>"á¼¤","á¼«"=>"á¼£","á¼ª"=>"á¼¢","á¼©"=>"á¼¡","á¼¨"=>"ï¿½ ",
"á¼"=>"á¼?","á¼?"=>"á¼?","á¼?"=>"á¼?","á¼?"=>"á¼?","á¼?"=>"á¼?","á¼?"=>"á¼",
"á¼"=>"á¼?","á¼?"=>"á¼?","á¼"=>"á¼?","á¼?"=>"á¼?","á¼?"=>"á¼?","á¼?"=>"á¼?",
"á¼?"=>"á¼","á¼?"=>"á¼?","á»¸"=>"á»¹","á»¶"=>"á»·","á»´"=>"á»µ","á»²"=>"á»³",
"á»°"=>"á»±","á»®"=>"á»¯","á»¬"=>"á»­","á»ª"=>"á»«","á»¨"=>"á»©","á»¦"=>"á»§",
"á»¤"=>"á»¥","á»¢"=>"á»£","ï¿½ "=>"á»¡","á»?"=>"á»?","á»?"=>"á»","á»?"=>"á»?",
"á»?"=>"á»?","á»?"=>"á»?","á»?"=>"á»?","á»?"=>"á»?","á»"=>"á»?","á»?"=>"á»",
"á»?"=>"á»","á»?"=>"á»?","á»?"=>"á»?","á»?"=>"á»?","á»?"=>"á»?","á»?"=>"á»?",
"á»?"=>"á»","áº¾"=>"áº¿","áº¼"=>"áº½","áºº"=>"áº»","áº¸"=>"áº¹","áº¶"=>"áº·",
"áº´"=>"áºµ","áº²"=>"áº³","áº°"=>"áº±","áº®"=>"áº¯","áº¬"=>"áº­","áºª"=>"áº«",
"áº¨"=>"áº©","áº¦"=>"áº§","áº¤"=>"áº¥","áº¢"=>"áº£","ï¿½ "=>"áº¡","áº?"=>"áº?",
"áº?"=>"áº?","áº"=>"áº?","áº?"=>"áº","áº?"=>"áº","áº?"=>"áº?","áº?"=>"áº?",
"áº?"=>"áº?","áº?"=>"áº?","áº?"=>"áº?","áº?"=>"áº","á¹¾"=>"á¹¿","á¹¼"=>"á¹½",
"á¹º"=>"á¹»","á¹¸"=>"á¹¹","á¹¶"=>"á¹·","á¹´"=>"á¹µ","á¹²"=>"á¹³","á¹°"=>"á¹±",
"á¹®"=>"á¹¯","á¹¬"=>"á¹­","á¹ª"=>"á¹«","á¹¨"=>"á¹©","á¹¦"=>"á¹§","á¹¤"=>"á¹¥",
"á¹¢"=>"á¹£","ï¿½ "=>"á¹¡","á¹?"=>"á¹?","á¹?"=>"á¹","á¹?"=>"á¹?","á¹?"=>"á¹?",
"á¹?"=>"á¹?","á¹?"=>"á¹?","á¹?"=>"á¹?","á¹"=>"á¹?","á¹?"=>"á¹","á¹?"=>"á¹",
"á¹?"=>"á¹?","á¹?"=>"á¹?","á¹?"=>"á¹?","á¹?"=>"á¹?","á¹?"=>"á¹?","á¹?"=>"á¹",
"á¸¾"=>"á¸¿","á¸¼"=>"á¸½","á¸º"=>"á¸»","á¸¸"=>"á¸¹","á¸¶"=>"á¸·","á¸´"=>"á¸µ",
"á¸²"=>"á¸³","á¸°"=>"á¸±","á¸®"=>"á¸¯","á¸¬"=>"á¸­","á¸ª"=>"á¸«","á¸¨"=>"á¸©",
"á¸¦"=>"á¸§","á¸¤"=>"á¸¥","á¸¢"=>"á¸£","ï¿½ "=>"á¸¡","á¸?"=>"á¸?","á¸?"=>"á¸",
"á¸?"=>"á¸?","á¸?"=>"á¸?","á¸?"=>"á¸?","á¸?"=>"á¸?","á¸?"=>"á¸?","á¸"=>"á¸?",
"á¸?"=>"á¸","á¸?"=>"á¸","á¸?"=>"á¸?","á¸?"=>"á¸?","á¸?"=>"á¸?","á¸?"=>"á¸?",
"á¸?"=>"á¸?","á¸?"=>"á¸","Õ?"=>"Ö?","Õ?"=>"Ö?","Õ?"=>"Ö?","Õ?"=>"Ö?",
"Õ?"=>"Ö?","Õ?"=>"Ö","Õ"=>"Ö?","Õ"=>"Õ¿","Õ?"=>"Õ¾","Õ"=>"Õ½",
"Õ?"=>"Õ¼","Õ?"=>"Õ»","Õ?"=>"Õº","Õ?"=>"Õ¹","Õ?"=>"Õ¸","Õ?"=>"Õ·",
"Õ?"=>"Õ¶","Õ?"=>"Õµ","Õ?"=>"Õ´","Õ?"=>"Õ³","Õ?"=>"Õ²","Õ"=>"Õ±",
"Õ?"=>"Õ°","Ô¿"=>"Õ¯","Ô¾"=>"Õ®","Ô½"=>"Õ­","Ô¼"=>"Õ¬","Ô»"=>"Õ«",
"Ôº"=>"Õª","Ô¹"=>"Õ©","Ô¸"=>"Õ¨","Ô·"=>"Õ§","Ô¶"=>"Õ¦","Ôµ"=>"Õ¥",
"Ô´"=>"Õ¤","Ô³"=>"Õ£","Ô²"=>"Õ¢","Ô±"=>"Õ¡","Ô?"=>"Ô","Ô?"=>"Ô",
"Ô?"=>"Ô?","Ô?"=>"Ô?","Ô?"=>"Ô?","Ô?"=>"Ô?","Ô?"=>"Ô?","Ô?"=>"Ô",
"Ó¸"=>"Ó¹","Ó´"=>"Óµ","Ó²"=>"Ó³","Ó°"=>"Ó±","Ó®"=>"Ó¯","Ó¬"=>"Ó­",
"Óª"=>"Ó«","Ó¨"=>"Ó©","Ó¦"=>"Ó§","Ó¤"=>"Ó¥","Ó¢"=>"Ó£","ï¿½ "=>"Ó¡",
"Ó?"=>"Ó?","Ó?"=>"Ó","Ó?"=>"Ó?","Ó?"=>"Ó?","Ó?"=>"Ó?","Ó?"=>"Ó?",
"Ó?"=>"Ó?","Ó"=>"Ó?","Ó"=>"Ó?","Ó?"=>"Ó?","Ó?"=>"Ó?","Ó?"=>"Ó?",
"Ó?"=>"Ó?","Ó?"=>"Ó?","Ó"=>"Ó?","Ò¾"=>"Ò¿","Ò¼"=>"Ò½","Òº"=>"Ò»",
"Ò¸"=>"Ò¹","Ò¶"=>"Ò·","Ò´"=>"Òµ","Ò²"=>"Ò³","Ò°"=>"Ò±","Ò®"=>"Ò¯",
"Ò¬"=>"Ò­","Òª"=>"Ò«","Ò¨"=>"Ò©","Ò¦"=>"Ò§","Ò¤"=>"Ò¥","Ò¢"=>"Ò£",
"ï¿½ "=>"Ò¡","Ò?"=>"Ò?","Ò?"=>"Ò","Ò?"=>"Ò?","Ò?"=>"Ò?","Ò?"=>"Ò?",
"Ò?"=>"Ò?","Ò?"=>"Ò?","Ò"=>"Ò?","Ò?"=>"Ò","Ò?"=>"Ò","Ò?"=>"Ò?",
"Ò?"=>"Ò","Ñ¾"=>"Ñ¿","Ñ¼"=>"Ñ½","Ñº"=>"Ñ»","Ñ¸"=>"Ñ¹","Ñ¶"=>"Ñ·",
"Ñ´"=>"Ñµ","Ñ²"=>"Ñ³","Ñ°"=>"Ñ±","Ñ®"=>"Ñ¯","Ñ¬"=>"Ñ­","Ñª"=>"Ñ«",
"Ñ¨"=>"Ñ©","Ñ¦"=>"Ñ§","Ñ¤"=>"Ñ¥","Ñ¢"=>"Ñ£","ï¿½ "=>"Ñ¡","Ð¯"=>"Ñ",
"Ð®"=>"Ñ?","Ð­"=>"Ñ","Ð¬"=>"Ñ?","Ð«"=>"Ñ?","Ðª"=>"Ñ?","Ð©"=>"Ñ?",
"Ð¨"=>"Ñ?","Ð§"=>"Ñ?","Ð¦"=>"Ñ?","Ð¥"=>"Ñ?","Ð¤"=>"Ñ?","Ð£"=>"Ñ?",
"Ð¢"=>"Ñ?","Ð¡"=>"Ñ","ï¿½ "=>"Ñ?","Ð?"=>"Ð¿","Ð?"=>"Ð¾","Ð"=>"Ð½",
"Ð?"=>"Ð¼","Ð?"=>"Ð»","Ð?"=>"Ðº","Ð?"=>"Ð¹","Ð?"=>"Ð¸","Ð?"=>"Ð·",
"Ð?"=>"Ð¶","Ð?"=>"Ðµ","Ð?"=>"Ð´","Ð?"=>"Ð³","Ð?"=>"Ð²","Ð?"=>"Ð±",
"Ð"=>"Ð°","Ð"=>"Ñ?","Ð?"=>"Ñ?","Ð"=>"Ñ","Ð?"=>"Ñ?","Ð?"=>"Ñ?",
"Ð?"=>"Ñ?","Ð?"=>"Ñ?","Ð?"=>"Ñ?","Ð?"=>"Ñ?","Ð?"=>"Ñ?","Ð?"=>"Ñ?",
"Ð?"=>"Ñ?","Ð?"=>"Ñ?","Ð?"=>"Ñ?","Ð"=>"Ñ?","Ð?"=>"Ñ","Ï´"=>"Î¸",
"Ï®"=>"Ï¯","Ï¬"=>"Ï­","Ïª"=>"Ï«","Ï¨"=>"Ï©","Ï¦"=>"Ï§","Ï¤"=>"Ï¥",
"Ï¢"=>"Ï£","ï¿½ "=>"Ï¡","Ï?"=>"Ï?","Ï?"=>"Ï","Ï?"=>"Ï?","Ï?"=>"Ï?",
"Î«"=>"Ï?","Îª"=>"Ï?","Î©"=>"Ï?","Î¨"=>"Ï?","Î§"=>"Ï?","Î¦"=>"Ï?",
"Î¥"=>"Ï?","Î¤"=>"Ï?","Î£"=>"Ï?","Î¡"=>"Ï","ï¿½ "=>"Ï?","Î?"=>"Î¿",
"Î?"=>"Î¾","Î"=>"Î½","Î?"=>"Î¼","Î?"=>"Î»","Î?"=>"Îº","Î?"=>"Î¹",
"Î?"=>"Î¸","Î?"=>"Î·","Î?"=>"Î¶","Î?"=>"Îµ","Î?"=>"Î´","Î?"=>"Î³",
"Î?"=>"Î²","Î?"=>"Î±","Î"=>"Ï?","Î?"=>"Ï","Î?"=>"Ï?","Î?"=>"Î¯",
"Î?"=>"Î®","Î?"=>"Î­","Î?"=>"Î¬","È²"=>"È³","È°"=>"È±","È®"=>"È¯",
"È¬"=>"È­","Èª"=>"È«","È¨"=>"È©","È¦"=>"È§","È¤"=>"È¥","È¢"=>"È£",
"ï¿½ "=>"Æ?","È?"=>"È?","È?"=>"È","È?"=>"È?","È?"=>"È?","È?"=>"È?",
"È?"=>"È?","È?"=>"È?","È"=>"È?","È?"=>"È","È?"=>"È","È?"=>"È?",
"È?"=>"È?","È?"=>"È?","È?"=>"È?","È?"=>"È?","È?"=>"È","Ç¾"=>"Ç¿",
"Ç¼"=>"Ç½","Çº"=>"Ç»","Ç¸"=>"Ç¹","Ç·"=>"Æ¿","Ç¶"=>"Æ?","Ç´"=>"Çµ",
"Ç±"=>"Ç³","Ç®"=>"Ç¯","Ç¬"=>"Ç­","Çª"=>"Ç«","Ç¨"=>"Ç©","Ç¦"=>"Ç§",
"Ç¤"=>"Ç¥","Ç¢"=>"Ç£","ï¿½ "=>"Ç¡","Ç?"=>"Ç?","Ç?"=>"Ç?","Ç?"=>"Ç?",
"Ç?"=>"Ç?","Ç?"=>"Ç?","Ç?"=>"Ç?","Ç?"=>"Ç?","Ç"=>"Ç","Ç"=>"Ç?",
"Ç?"=>"Ç?","Ç?"=>"Ç?","Ç?"=>"Ç?","Æ¼"=>"Æ½","Æ¸"=>"Æ¹","Æ·"=>"Ê?",
"Æµ"=>"Æ¶","Æ³"=>"Æ´","Æ²"=>"Ê?","Æ±"=>"Ê?","Æ¯"=>"Æ°","Æ®"=>"Ê?",
"Æ¬"=>"Æ­","Æ©"=>"Ê?","Æ§"=>"Æ¨","Æ¦"=>"Ê?","Æ¤"=>"Æ¥","Æ¢"=>"Æ£",
"ï¿½ "=>"Æ¡","Æ?"=>"Éµ","Æ"=>"É²","Æ?"=>"É¯","Æ?"=>"Æ?","Æ?"=>"É¨",
"Æ?"=>"É©","Æ?"=>"É£","Æ?"=>"ï¿½ ","Æ?"=>"Æ?","Æ"=>"É?","Æ"=>"É?",
"Æ?"=>"Ç","Æ?"=>"Æ?","Æ?"=>"É?","Æ?"=>"É?","Æ?"=>"Æ?","Æ?"=>"É?",
"Æ?"=>"Æ?","Æ?"=>"Æ?","Æ"=>"É?","Å½"=>"Å¾","Å»"=>"Å¼","Å¹"=>"Åº",
"Å¸"=>"Ã¿","Å¶"=>"Å·","Å´"=>"Åµ","Å²"=>"Å³","Å°"=>"Å±","Å®"=>"Å¯",
"Å¬"=>"Å­","Åª"=>"Å«","Å¨"=>"Å©","Å¦"=>"Å§","Å¤"=>"Å¥","Å¢"=>"Å£",
"ï¿½ "=>"Å¡","Å?"=>"Å?","Å?"=>"Å","Å?"=>"Å?","Å?"=>"Å?","Å?"=>"Å?",
"Å?"=>"Å?","Å?"=>"Å?","Å"=>"Å?","Å?"=>"Å","Å?"=>"Å","Å?"=>"Å?",
"Å?"=>"Å?","Å?"=>"Å?","Å?"=>"Å?","Å"=>"Å?","Ä¿"=>"Å?","Ä½"=>"Ä¾",
"Ä»"=>"Ä¼","Ä¹"=>"Äº","Ä¶"=>"Ä·","Ä´"=>"Äµ","Ä²"=>"Ä³","Ä°"=>"i",
"Ä®"=>"Ä¯","Ä¬"=>"Ä­","Äª"=>"Ä«","Ä¨"=>"Ä©","Ä¦"=>"Ä§","Ä¤"=>"Ä¥",
"Ä¢"=>"Ä£","ï¿½ "=>"Ä¡","Ä?"=>"Ä?","Ä?"=>"Ä","Ä?"=>"Ä?","Ä?"=>"Ä?",
"Ä?"=>"Ä?","Ä?"=>"Ä?","Ä?"=>"Ä?","Ä"=>"Ä?","Ä?"=>"Ä","Ä?"=>"Ä",
"Ä?"=>"Ä?","Ä?"=>"Ä?","Ä?"=>"Ä?","Ä?"=>"Ä?","Ä?"=>"Ä?","Ä?"=>"Ä",
"Ã?"=>"Ã¾","Ã"=>"Ã½","Ã?"=>"Ã¼","Ã?"=>"Ã»","Ã?"=>"Ãº","Ã?"=>"Ã¹",
"Ã?"=>"Ã¸","Ã?"=>"Ã¶","Ã?"=>"Ãµ","Ã?"=>"Ã´","Ã?"=>"Ã³","Ã?"=>"Ã²",
"Ã?"=>"Ã±","Ã"=>"Ã°","Ã"=>"Ã¯","Ã?"=>"Ã®","Ã"=>"Ã­","Ã?"=>"Ã¬",
"Ã?"=>"Ã«","Ã?"=>"Ãª","Ã?"=>"Ã©","Ã?"=>"Ã¨","Ã?"=>"Ã§","Ã?"=>"Ã¦",
"Ã?"=>"Ã¥","Ã?"=>"Ã¤","Ã?"=>"Ã£","Ã?"=>"Ã¢","Ã"=>"Ã¡","Ã?"=>"ï¿½ ",
"Z"=>"z","Y"=>"y","X"=>"x","W"=>"w","V"=>"v","U"=>"u",
"T"=>"t","S"=>"s","R"=>"r","Q"=>"q","P"=>"p","O"=>"o",
"N"=>"n","M"=>"m","L"=>"l","K"=>"k","J"=>"j","I"=>"i",
"H"=>"h","G"=>"g","F"=>"f","E"=>"e","D"=>"d","C"=>"c",
"B"=>"b","A"=>"a"
);


$UTF8_TABLES['strtoupper'] = array(
"ï½?"=>"ï¼º","ï½?"=>"ï¼¹","ï½?"=>"ï¼¸","ï½?"=>"ï¼·","ï½?"=>"ï¼¶","ï½?"=>"ï¼µ",
"ï½?"=>"ï¼´","ï½?"=>"ï¼³","ï½?"=>"ï¼²","ï½?"=>"ï¼±","ï½"=>"ï¼°","ï½"=>"ï¼¯",
"ï½?"=>"ï¼®","ï½"=>"ï¼­","ï½?"=>"ï¼¬","ï½?"=>"ï¼«","ï½?"=>"ï¼ª","ï½?"=>"ï¼©",
"ï½?"=>"ï¼¨","ï½?"=>"ï¼§","ï½?"=>"ï¼¦","ï½?"=>"ï¼¥","ï½?"=>"ï¼¤","ï½?"=>"ï¼£",
"ï½?"=>"ï¼¢","ï½"=>"ï¼¡","á¿³"=>"á¿¼","á¿¥"=>"á¿¬","á¿¡"=>"á¿©","ï¿½ "=>"á¿¨",
"á¿?"=>"á¿?","á¿"=>"á¿?","á¿?"=>"á¿?","á¾¾"=>"Î?","á¾³"=>"á¾¼","á¾±"=>"á¾¹",
"á¾°"=>"á¾¸","á¾§"=>"á¾¯","á¾¦"=>"á¾®","á¾¥"=>"á¾­","á¾¤"=>"á¾¬","á¾£"=>"á¾«",
"á¾¢"=>"á¾ª","á¾¡"=>"á¾©","ï¿½ "=>"á¾¨","á¾?"=>"á¾?","á¾?"=>"á¾?","á¾?"=>"á¾",
"á¾?"=>"á¾?","á¾?"=>"á¾?","á¾?"=>"á¾?","á¾?"=>"á¾?","á¾"=>"á¾?","á¾?"=>"á¾",
"á¾?"=>"á¾?","á¾?"=>"á¾","á¾?"=>"á¾?","á¾?"=>"á¾?","á¾?"=>"á¾?","á¾"=>"á¾?",
"á¾?"=>"á¾?","á½½"=>"á¿»","á½¼"=>"á¿º","á½»"=>"á¿«","á½º"=>"á¿ª","á½¹"=>"á¿¹",
"á½¸"=>"á¿¸","á½·"=>"á¿?","á½¶"=>"á¿?","á½µ"=>"á¿?","á½´"=>"á¿?","á½³"=>"á¿?",
"á½²"=>"á¿?","á½±"=>"á¾»","á½°"=>"á¾º","á½§"=>"á½¯","á½¦"=>"á½®","á½¥"=>"á½­",
"á½¤"=>"á½¬","á½£"=>"á½«","á½¢"=>"á½ª","á½¡"=>"á½©","ï¿½ "=>"á½¨","á½?"=>"á½?",
"á½?"=>"á½","á½?"=>"á½?","á½?"=>"á½?","á½?"=>"á½","á½?"=>"á½?","á½?"=>"á½?",
"á½?"=>"á½?","á½"=>"á½?","á½?"=>"á½?","á¼·"=>"á¼¿","á¼¶"=>"á¼¾","á¼µ"=>"á¼½",
"á¼´"=>"á¼¼","á¼³"=>"á¼»","á¼²"=>"á¼º","á¼±"=>"á¼¹","á¼°"=>"á¼¸","á¼§"=>"á¼¯",
"á¼¦"=>"á¼®","á¼¥"=>"á¼­","á¼¤"=>"á¼¬","á¼£"=>"á¼«","á¼¢"=>"á¼ª","á¼¡"=>"á¼©",
"ï¿½ "=>"á¼¨","á¼?"=>"á¼","á¼?"=>"á¼?","á¼?"=>"á¼?","á¼?"=>"á¼?","á¼?"=>"á¼?",
"á¼"=>"á¼?","á¼?"=>"á¼","á¼?"=>"á¼?","á¼?"=>"á¼","á¼?"=>"á¼?","á¼?"=>"á¼?",
"á¼?"=>"á¼?","á¼"=>"á¼?","á¼?"=>"á¼?","á»¹"=>"á»¸","á»·"=>"á»¶","á»µ"=>"á»´",
"á»³"=>"á»²","á»±"=>"á»°","á»¯"=>"á»®","á»­"=>"á»¬","á»«"=>"á»ª","á»©"=>"á»¨",
"á»§"=>"á»¦","á»¥"=>"á»¤","á»£"=>"á»¢","á»¡"=>"ï¿½ ","á»?"=>"á»?","á»"=>"á»?",
"á»?"=>"á»?","á»?"=>"á»?","á»?"=>"á»?","á»?"=>"á»?","á»?"=>"á»?","á»?"=>"á»",
"á»"=>"á»?","á»"=>"á»?","á»?"=>"á»?","á»?"=>"á»?","á»?"=>"á»?","á»?"=>"á»?",
"á»?"=>"á»?","á»"=>"á»?","áº¿"=>"áº¾","áº½"=>"áº¼","áº»"=>"áºº","áº¹"=>"áº¸",
"áº·"=>"áº¶","áºµ"=>"áº´","áº³"=>"áº²","áº±"=>"áº°","áº¯"=>"áº®","áº­"=>"áº¬",
"áº«"=>"áºª","áº©"=>"áº¨","áº§"=>"áº¦","áº¥"=>"áº¤","áº£"=>"áº¢","áº¡"=>"ï¿½ ",
"áº?"=>"ï¿½ ","áº?"=>"áº?","áº?"=>"áº?","áº?"=>"áº","áº"=>"áº?","áº"=>"áº?",
"áº?"=>"áº?","áº?"=>"áº?","áº?"=>"áº?","áº?"=>"áº?","áº?"=>"áº?","áº"=>"áº?",
"á¹¿"=>"á¹¾","á¹½"=>"á¹¼","á¹»"=>"á¹º","á¹¹"=>"á¹¸","á¹·"=>"á¹¶","á¹µ"=>"á¹´",
"á¹³"=>"á¹²","á¹±"=>"á¹°","á¹¯"=>"á¹®","á¹­"=>"á¹¬","á¹«"=>"á¹ª","á¹©"=>"á¹¨",
"á¹§"=>"á¹¦","á¹¥"=>"á¹¤","á¹£"=>"á¹¢","á¹¡"=>"ï¿½ ","á¹?"=>"á¹?","á¹"=>"á¹?",
"á¹?"=>"á¹?","á¹?"=>"á¹?","á¹?"=>"á¹?","á¹?"=>"á¹?","á¹?"=>"á¹?","á¹?"=>"á¹",
"á¹"=>"á¹?","á¹"=>"á¹?","á¹?"=>"á¹?","á¹?"=>"á¹?","á¹?"=>"á¹?","á¹?"=>"á¹?",
"á¹?"=>"á¹?","á¹"=>"á¹?","á¸¿"=>"á¸¾","á¸½"=>"á¸¼","á¸»"=>"á¸º","á¸¹"=>"á¸¸",
"á¸·"=>"á¸¶","á¸µ"=>"á¸´","á¸³"=>"á¸²","á¸±"=>"á¸°","á¸¯"=>"á¸®","á¸­"=>"á¸¬",
"á¸«"=>"á¸ª","á¸©"=>"á¸¨","á¸§"=>"á¸¦","á¸¥"=>"á¸¤","á¸£"=>"á¸¢","á¸¡"=>"ï¿½ ",
"á¸?"=>"á¸?","á¸"=>"á¸?","á¸?"=>"á¸?","á¸?"=>"á¸?","á¸?"=>"á¸?","á¸?"=>"á¸?",
"á¸?"=>"á¸?","á¸?"=>"á¸","á¸"=>"á¸?","á¸"=>"á¸?","á¸?"=>"á¸?","á¸?"=>"á¸?",
"á¸?"=>"á¸?","á¸?"=>"á¸?","á¸?"=>"á¸?","á¸"=>"á¸?","Ö?"=>"Õ?","Ö?"=>"Õ?",
"Ö?"=>"Õ?","Ö?"=>"Õ?","Ö?"=>"Õ?","Ö"=>"Õ?","Ö?"=>"Õ","Õ¿"=>"Õ",
"Õ¾"=>"Õ?","Õ½"=>"Õ","Õ¼"=>"Õ?","Õ»"=>"Õ?","Õº"=>"Õ?","Õ¹"=>"Õ?",
"Õ¸"=>"Õ?","Õ·"=>"Õ?","Õ¶"=>"Õ?","Õµ"=>"Õ?","Õ´"=>"Õ?","Õ³"=>"Õ?",
"Õ²"=>"Õ?","Õ±"=>"Õ","Õ°"=>"Õ?","Õ¯"=>"Ô¿","Õ®"=>"Ô¾","Õ­"=>"Ô½",
"Õ¬"=>"Ô¼","Õ«"=>"Ô»","Õª"=>"Ôº","Õ©"=>"Ô¹","Õ¨"=>"Ô¸","Õ§"=>"Ô·",
"Õ¦"=>"Ô¶","Õ¥"=>"Ôµ","Õ¤"=>"Ô´","Õ£"=>"Ô³","Õ¢"=>"Ô²","Õ¡"=>"Ô±",
"Ô"=>"Ô?","Ô"=>"Ô?","Ô?"=>"Ô?","Ô?"=>"Ô?","Ô?"=>"Ô?","Ô?"=>"Ô?",
"Ô?"=>"Ô?","Ô"=>"Ô?","Ó¹"=>"Ó¸","Óµ"=>"Ó´","Ó³"=>"Ó²","Ó±"=>"Ó°",
"Ó¯"=>"Ó®","Ó­"=>"Ó¬","Ó«"=>"Óª","Ó©"=>"Ó¨","Ó§"=>"Ó¦","Ó¥"=>"Ó¤",
"Ó£"=>"Ó¢","Ó¡"=>"ï¿½ ","Ó?"=>"Ó?","Ó"=>"Ó?","Ó?"=>"Ó?","Ó?"=>"Ó?",
"Ó?"=>"Ó?","Ó?"=>"Ó?","Ó?"=>"Ó?","Ó?"=>"Ó","Ó?"=>"Ó","Ó?"=>"Ó?",
"Ó?"=>"Ó?","Ó?"=>"Ó?","Ó?"=>"Ó?","Ó?"=>"Ó?","Ó?"=>"Ó","Ò¿"=>"Ò¾",
"Ò½"=>"Ò¼","Ò»"=>"Òº","Ò¹"=>"Ò¸","Ò·"=>"Ò¶","Òµ"=>"Ò´","Ò³"=>"Ò²",
"Ò±"=>"Ò°","Ò¯"=>"Ò®","Ò­"=>"Ò¬","Ò«"=>"Òª","Ò©"=>"Ò¨","Ò§"=>"Ò¦",
"Ò¥"=>"Ò¤","Ò£"=>"Ò¢","Ò¡"=>"ï¿½ ","Ò?"=>"Ò?","Ò"=>"Ò?","Ò?"=>"Ò?",
"Ò?"=>"Ò?","Ò?"=>"Ò?","Ò?"=>"Ò?","Ò?"=>"Ò?","Ò?"=>"Ò","Ò"=>"Ò?",
"Ò"=>"Ò?","Ò?"=>"Ò?","Ò"=>"Ò?","Ñ¿"=>"Ñ¾","Ñ½"=>"Ñ¼","Ñ»"=>"Ñº",
"Ñ¹"=>"Ñ¸","Ñ·"=>"Ñ¶","Ñµ"=>"Ñ´","Ñ³"=>"Ñ²","Ñ±"=>"Ñ°","Ñ¯"=>"Ñ®",
"Ñ­"=>"Ñ¬","Ñ«"=>"Ñª","Ñ©"=>"Ñ¨","Ñ§"=>"Ñ¦","Ñ¥"=>"Ñ¤","Ñ£"=>"Ñ¢",
"Ñ¡"=>"ï¿½ ","Ñ?"=>"Ð","Ñ?"=>"Ð?","Ñ"=>"Ð","Ñ?"=>"Ð?","Ñ?"=>"Ð?",
"Ñ?"=>"Ð?","Ñ?"=>"Ð?","Ñ?"=>"Ð?","Ñ?"=>"Ð?","Ñ?"=>"Ð?","Ñ?"=>"Ð?",
"Ñ?"=>"Ð?","Ñ?"=>"Ð?","Ñ?"=>"Ð?","Ñ?"=>"Ð","Ñ"=>"Ð?","Ñ"=>"Ð¯",
"Ñ?"=>"Ð®","Ñ"=>"Ð­","Ñ?"=>"Ð¬","Ñ?"=>"Ð«","Ñ?"=>"Ðª","Ñ?"=>"Ð©",
"Ñ?"=>"Ð¨","Ñ?"=>"Ð§","Ñ?"=>"Ð¦","Ñ?"=>"Ð¥","Ñ?"=>"Ð¤","Ñ?"=>"Ð£",
"Ñ?"=>"Ð¢","Ñ"=>"Ð¡","Ñ?"=>"ï¿½ ","Ð¿"=>"Ð?","Ð¾"=>"Ð?","Ð½"=>"Ð",
"Ð¼"=>"Ð?","Ð»"=>"Ð?","Ðº"=>"Ð?","Ð¹"=>"Ð?","Ð¸"=>"Ð?","Ð·"=>"Ð?",
"Ð¶"=>"Ð?","Ðµ"=>"Ð?","Ð´"=>"Ð?","Ð³"=>"Ð?","Ð²"=>"Ð?","Ð±"=>"Ð?",
"Ð°"=>"Ð","Ïµ"=>"Î?","Ï²"=>"Î£","Ï±"=>"Î¡","Ï°"=>"Î?","Ï¯"=>"Ï®",
"Ï­"=>"Ï¬","Ï«"=>"Ïª","Ï©"=>"Ï¨","Ï§"=>"Ï¦","Ï¥"=>"Ï¤","Ï£"=>"Ï¢",
"Ï¡"=>"ï¿½ ","Ï?"=>"Ï?","Ï"=>"Ï?","Ï?"=>"Ï?","Ï?"=>"Ï?","Ï?"=>"ï¿½ ",
"Ï?"=>"Î¦","Ï?"=>"Î?","Ï"=>"Î?","Ï?"=>"Î","Ï"=>"Î?","Ï?"=>"Î?",
"Ï?"=>"Î«","Ï?"=>"Îª","Ï?"=>"Î©","Ï?"=>"Î¨","Ï?"=>"Î§","Ï?"=>"Î¦",
"Ï?"=>"Î¥","Ï?"=>"Î¤","Ï?"=>"Î£","Ï?"=>"Î£","Ï"=>"Î¡","Ï?"=>"ï¿½ ",
"Î¿"=>"Î?","Î¾"=>"Î?","Î½"=>"Î","Î¼"=>"Î?","Î»"=>"Î?","Îº"=>"Î?",
"Î¹"=>"Î?","Î¸"=>"Î?","Î·"=>"Î?","Î¶"=>"Î?","Îµ"=>"Î?","Î´"=>"Î?",
"Î³"=>"Î?","Î²"=>"Î?","Î±"=>"Î?","Î¯"=>"Î?","Î®"=>"Î?","Î­"=>"Î?",
"Î¬"=>"Î?","Ê?"=>"Æ·","Ê?"=>"Æ²","Ê?"=>"Æ±","Ê?"=>"Æ®","Ê?"=>"Æ©",
"Ê?"=>"Æ¦","Éµ"=>"Æ?","É²"=>"Æ","É¯"=>"Æ?","É©"=>"Æ?","É¨"=>"Æ?",
"É£"=>"Æ?","ï¿½ "=>"Æ?","É?"=>"Æ","É?"=>"Æ","É?"=>"Æ?","É?"=>"Æ?",
"É?"=>"Æ?","É?"=>"Æ","È³"=>"È²","È±"=>"È°","È¯"=>"È®","È­"=>"È¬",
"È«"=>"Èª","È©"=>"È¨","È§"=>"È¦","È¥"=>"È¤","È£"=>"È¢","È?"=>"È?",
"È"=>"È?","È?"=>"È?","È?"=>"È?","È?"=>"È?","È?"=>"È?","È?"=>"È?",
"È?"=>"È","È"=>"È?","È"=>"È?","È?"=>"È?","È?"=>"È?","È?"=>"È?",
"È?"=>"È?","È?"=>"È?","È"=>"È?","Ç¿"=>"Ç¾","Ç½"=>"Ç¼","Ç»"=>"Çº",
"Ç¹"=>"Ç¸","Çµ"=>"Ç´","Ç³"=>"Ç²","Ç¯"=>"Ç®","Ç­"=>"Ç¬","Ç«"=>"Çª",
"Ç©"=>"Ç¨","Ç§"=>"Ç¦","Ç¥"=>"Ç¤","Ç£"=>"Ç¢","Ç¡"=>"ï¿½ ","Ç?"=>"Ç?",
"Ç"=>"Æ?","Ç?"=>"Ç?","Ç?"=>"Ç?","Ç?"=>"Ç?","Ç?"=>"Ç?","Ç?"=>"Ç?",
"Ç?"=>"Ç?","Ç"=>"Ç","Ç?"=>"Ç","Ç?"=>"Ç?","Ç?"=>"Ç?","Ç?"=>"Ç?",
"Æ¿"=>"Ç·","Æ½"=>"Æ¼","Æ¹"=>"Æ¸","Æ¶"=>"Æµ","Æ´"=>"Æ³","Æ°"=>"Æ¯",
"Æ­"=>"Æ¬","Æ¨"=>"Æ§","Æ¥"=>"Æ¤","Æ£"=>"Æ¢","Æ¡"=>"ï¿½ ","Æ?"=>"ï¿½ ",
"Æ?"=>"Æ?","Æ?"=>"Ç¶","Æ?"=>"Æ?","Æ?"=>"Æ?","Æ?"=>"Æ?","Æ?"=>"Æ?",
"Æ?"=>"Æ?","Å¿"=>"S","Å¾"=>"Å½","Å¼"=>"Å»","Åº"=>"Å¹","Å·"=>"Å¶",
"Åµ"=>"Å´","Å³"=>"Å²","Å±"=>"Å°","Å¯"=>"Å®","Å­"=>"Å¬","Å«"=>"Åª",
"Å©"=>"Å¨","Å§"=>"Å¦","Å¥"=>"Å¤","Å£"=>"Å¢","Å¡"=>"ï¿½ ","Å?"=>"Å?",
"Å"=>"Å?","Å?"=>"Å?","Å?"=>"Å?","Å?"=>"Å?","Å?"=>"Å?","Å?"=>"Å?",
"Å?"=>"Å","Å"=>"Å?","Å"=>"Å?","Å?"=>"Å?","Å?"=>"Å?","Å?"=>"Å?",
"Å?"=>"Å?","Å?"=>"Å","Å?"=>"Ä¿","Ä¾"=>"Ä½","Ä¼"=>"Ä»","Äº"=>"Ä¹",
"Ä·"=>"Ä¶","Äµ"=>"Ä´","Ä³"=>"Ä²","Ä±"=>"I","Ä¯"=>"Ä®","Ä­"=>"Ä¬",
"Ä«"=>"Äª","Ä©"=>"Ä¨","Ä§"=>"Ä¦","Ä¥"=>"Ä¤","Ä£"=>"Ä¢","Ä¡"=>"ï¿½ ",
"Ä?"=>"Ä?","Ä"=>"Ä?","Ä?"=>"Ä?","Ä?"=>"Ä?","Ä?"=>"Ä?","Ä?"=>"Ä?",
"Ä?"=>"Ä?","Ä?"=>"Ä","Ä"=>"Ä?","Ä"=>"Ä?","Ä?"=>"Ä?","Ä?"=>"Ä?",
"Ä?"=>"Ä?","Ä?"=>"Ä?","Ä?"=>"Ä?","Ä"=>"Ä?","Ã¿"=>"Å¸","Ã¾"=>"Ã?",
"Ã½"=>"Ã","Ã¼"=>"Ã?","Ã»"=>"Ã?","Ãº"=>"Ã?","Ã¹"=>"Ã?","Ã¸"=>"Ã?",
"Ã¶"=>"Ã?","Ãµ"=>"Ã?","Ã´"=>"Ã?","Ã³"=>"Ã?","Ã²"=>"Ã?","Ã±"=>"Ã?",
"Ã°"=>"Ã","Ã¯"=>"Ã","Ã®"=>"Ã?","Ã­"=>"Ã","Ã¬"=>"Ã?","Ã«"=>"Ã?",
"Ãª"=>"Ã?","Ã©"=>"Ã?","Ã¨"=>"Ã?","Ã§"=>"Ã?","Ã¦"=>"Ã?","Ã¥"=>"Ã?",
"Ã¤"=>"Ã?","Ã£"=>"Ã?","Ã¢"=>"Ã?","Ã¡"=>"Ã","ï¿½ "=>"Ã?","Âµ"=>"Î?",
"z"=>"Z","y"=>"Y","x"=>"X","w"=>"W","v"=>"V","u"=>"U",
"t"=>"T","s"=>"S","r"=>"R","q"=>"Q","p"=>"P","o"=>"O",
"n"=>"N","m"=>"M","l"=>"L","k"=>"K","j"=>"J","i"=>"I",
"h"=>"H","g"=>"G","f"=>"F","e"=>"E","d"=>"D","c"=>"C",
"b"=>"B","a"=>"A"
);

?>
