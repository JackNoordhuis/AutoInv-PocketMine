<?php

/**
 * ColorUtils.php – AutoInv
 *
 * Copyright (C) 2015-2017 Jack Noordhuis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Jack Noordhuiss
 *
 * Last modified on 16/10/2017 at 7:58 PM
 *
 */

namespace jacknoordhuis\autoinv\util;

class ColorUtils {

	/**
	 * Apply minecraft color codes to a string from our custom ones
	 *
	 * @param string $string
	 * @param string $symbol
	 *
	 * @return string
	 */
	public static function translateColors(string $string, string $symbol = "&") : string {
		return preg_replace("/{$symbol}([0123456789abcdefklmnor])/i", "§$1", $string);
	}

	/**
	 * Removes all minecraft color codes from a string
	 *
	 * @param string $string
	 * @param string $symbol
	 *
	 * @return string
	 */
	public static function cleanString(string $string, string $symbol = "&") : string {
		return preg_replace("/(?:{$symbol}|§)([0123456789abcdefklmnor])/i", "", $string);
	}

}