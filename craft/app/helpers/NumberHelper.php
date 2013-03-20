<?php
namespace Craft;

/**
 * Craft by Pixel & Tonic
 *
 * @package   Craft
 * @author    Pixel & Tonic, Inc.
 * @copyright Copyright (c) 2013, Pixel & Tonic, Inc.
 * @license   http://buildwithcraft.com/license1.0.html Craft License
 * @link      http://buildwithcraft.com
 */

/**
 *
 */
class NumberHelper
{
	/**
	 * Returns the "word" version of a number
	 *
	 * @param int $num The number
	 * @return string The number word, or the original number if it's >= 10
	 */
	public static function word($num)
	{
		$numberWordMap = array(
				1 => Craft::t('one'),
				2 => Craft::t('two'),
				3 => Craft::t('three'),
				4 => Craft::t('four'),
				5 => Craft::t('five'),
				6 => Craft::t('six'),
				7 => Craft::t('seven'),
				8 => Craft::t('eight'),
				9 => Craft::t('nine')
			);

		if (isset($numberWordMap[$num]))
		{
			return $numberWordMap[$num];
		}

		return (string)$num;
	}


	/**
	 * Returns the uppercase alphabetic version of a number
	 *
	 * @param int $num The number
	 * @return string The alphabetic version of the number
	 */
	function upperAlpha($num)
	{
		$num--;
		$alpha = '';

		while ($num >= 0)
		{
			$ascii = ($num % 26) + 65;
			$alpha = chr($ascii).$alpha;

			$num = intval($num / 26) - 1;
		}

		return $alpha;
	}

	/**
	 * Returns the lowercase alphabetic version of a number
	 *
	 * @param int $num The number
	 * @return string The alphabetic version of the number
	 */
	function lowerAlpha($num)
	{
		$alpha = static::upperAlpha($num);
		return strtolower($alpha);
	}

	/**
	 * Returns the uppercase roman numeral version of a number
	 *
	 * @param int $num The number
	 * @return string The roman numeral version of the number
	 */
	function upperRoman($num)
	{
		$roman = '';

		$map = array(
			'M'  => 1000,
			'CM' => 900,
			'D'  => 500,
			'CD' => 400,
			'C'  => 100,
			'XC' => 90,
			'L'  => 50,
			'XL' => 40,
			'X'  => 10,
			'IX' => 9,
			'V'  => 5,
			'IV' => 4,
			'I'  => 1
		);

		foreach ($map as $k => $v)
		{
			while ($num >= $v)
			{
				$roman .= $k;
				$num -= $v;
			}
		}

		return $roman;
	}

	/**
	 * Returns the lowercase roman numeral version of a number
	 *
	 * @param int $num The number
	 * @return string The roman numeral version of the number
	 */
	function lowerRoman($num)
	{
		$roman = static::upperRoman($num);
		return strtolower($roman);
	}

}
