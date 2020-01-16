<?php

namespace app\extension;


class Sizes
{
	const fields = [
		'size_5xs',
		'size_4xs',
		'size_3xs',
		'size_2xs',
		'size_xs',
		'size_s',
		'size_m',
		'size_l',
		'size_xl',
		'size_2xl',
		'size_3xl',
		'size_4xl',
	];

	const fields2 = [
		'size_2xs',
		'size_xs',
		'size_s',
		'size_m',
		'size_l',
		'size_xl',
		'size_2xl',
		'size_3xl',
		'size_4xl',
	];

	const fieldsRangeAdult = [
        'size_xs'  => 'XS',
        'size_s'   => 'S',
        'size_m'   => 'M',
        'size_l'   => 'L',
        'size_xl'  => 'XL',
        'size_2xl' => 'XXL',
        'size_3xl' => '3XL',
	];

	const fieldsRangeKids = [
        'size_2xs' => '116',
        'size_xs'  => '122',
        'size_s'   => '128',
        'size_m'   => '134',
        'size_l'   => '140',
        'size_xl'  => '146',
        'size_2xl' => '152',
        'size_3xl' => '158',
        'size_4xl' => '164',
	];


	const prices = [
		'size_5xs' => 'price_5xs',
		'size_4xs' => 'price_4xs',
		'size_3xs' => 'price_3xs',
		'size_2xs' => 'price_2xs',
		'size_xs'  => 'price_xs',
		'size_s'   => 'price_s',
		'size_m'   => 'price_m',
		'size_l'   => 'price_l',
		'size_xl'  => 'price_xl',
		'size_2xl' => 'price_2xl',
		'size_3xl' => 'price_3xl',
		'size_4xl' => 'price_4xl',
	];

	const adults = [
		'size_5xs' => '5XS',
		'size_4xs' => '4XS',
		'size_3xs' => '3XS',
		'size_2xs' => 'XXS',
		'size_xs'  => 'XS',
		'size_s'   => 'S',
		'size_m'   => 'M',
		'size_l'   => 'L',
		'size_xl'  => 'XL',
		'size_2xl' => 'XXL',
		'size_3xl' => '3XL',
		'size_4xl' => '4XL',
	];

	const kids = [
		'size_5xs' => '98',
		'size_4xs' => '104',
		'size_3xs' => '110',
		'size_2xs' => '116',
		'size_xs'  => '122',
		'size_s'   => '128',
		'size_m'   => '134',
		'size_l'   => '140',
		'size_xl'  => '146',
		'size_2xl' => '152',
		'size_3xl' => '158',
		'size_4xl' => '164',
	];

	const typeCompare = [
	  'adults' => self::adults,
	  'kids' => self::kids,
    ];

	// ENUM('5XS','4XS','3XS','XXS','XS','S','M','L','XL','XXL','3XL','4XL', '98','104','110','116','122','128','134','140','146','152','158','164')

	const revers = [
		'5XS' => 'size_5xs',
		'4XS' => 'size_4xs',
		'3XS' => 'size_3xs',
		'XXS' => 'size_2xs',
		'XS'  => 'size_xs',
		'S'   => 'size_s',
		'M'   => 'size_m',
		'L'   => 'size_l',
		'XL'  => 'size_xl',
		'XXL' => 'size_2xl',
		'3XL' => 'size_3xl',
		'4XL' => 'size_4xl',

		'98'  => 'size_5xs',
		'104' => 'size_4xs',
		'110' => 'size_3xs',
		'116' => 'size_2xs',
		'122' => 'size_xs',
		'128' => 'size_s',
		'134' => 'size_m',
		'140' => 'size_l',
		'146' => 'size_xl',
		'152' => 'size_2xl',
		'158' => 'size_3xl',
		'164' => 'size_4xl',
	];

	const selectSum = [
		'sum(size_5xs) AS size_5xs',
		'sum(size_4xs) AS size_4xs',
		'sum(size_3xs) AS size_3xs',
		'sum(size_2xs) AS size_2xs',
		'sum(size_xs) AS size_xs',
		'sum(size_s) AS size_s',
		'sum(size_m) AS size_m',
		'sum(size_l) AS size_l',
		'sum(size_xl) AS size_xl',
		'sum(size_2xl) AS size_2xl',
		'sum(size_3xl) AS size_3xl',
		'sum(size_4xl) AS size_4xl',
	];

	const selectSum2 = [
		'sum(size_5xs) AS size_5xs',
		'sum(size_4xs) AS size_4xs',
		'sum(size_3xs) AS size_3xs',
		'sum(size_2xs) AS size_2xs',
		'sum(size_xs) AS size_xs',
		'sum(size_s) AS size_s',
		'sum(size_m) AS size_m',
		'sum(size_l) AS size_l',
		'sum(size_xl) AS size_xl',
		'sum(size_2xl) AS size_2xl',
		'sum(size_3xl) AS size_3xl',
		'sum(size_4xl) AS size_4xl',
		'(' .
		'IFNULL(SUM(size_5xs), 0) + ' .
		'IFNULL(SUM(size_4xs), 0) + ' .
		'IFNULL(SUM(size_3xs), 0) + ' .
		'IFNULL(SUM(size_2xs), 0) + ' .
		'IFNULL(SUM(size_xs), 0) + ' .
		'IFNULL(SUM(size_s), 0) + ' .
		'IFNULL(SUM(size_m), 0) + ' .
		'IFNULL(SUM(size_l), 0) + ' .
		'IFNULL(SUM(size_xl), 0) + ' .
		'IFNULL(SUM(size_2xl), 0) + ' .
		'IFNULL(SUM(size_3xl), 0) + ' .
		'IFNULL(SUM(size_4xl), 0)' .
		') AS totalSum',
	];

	const selectSumAbs = [
		'sum(size_5xs) AS size_5xs',
		'sum(size_4xs) AS size_4xs',
		'sum(size_3xs) AS size_3xs',
		'sum(size_2xs) AS size_2xs',
		'sum(size_xs) AS size_xs',
		'sum(size_s) AS size_s',
		'sum(size_m) AS size_m',
		'sum(size_l) AS size_l',
		'sum(size_xl) AS size_xl',
		'sum(size_2xl) AS size_2xl',
		'sum(size_3xl) AS size_3xl',
		'sum(size_4xl) AS size_4xl',
		'(' .
		'ABS(IFNULL(SUM(size_5xs), 0)) + ' .
		'ABS(IFNULL(SUM(size_4xs), 0)) + ' .
		'ABS(IFNULL(SUM(size_3xs), 0)) + ' .
		'ABS(IFNULL(SUM(size_2xs), 0)) + ' .
		'ABS(IFNULL(SUM(size_xs), 0)) + ' .
		'ABS(IFNULL(SUM(size_s), 0)) + ' .
		'ABS(IFNULL(SUM(size_m), 0)) + ' .
		'ABS(IFNULL(SUM(size_l), 0)) + ' .
		'ABS(IFNULL(SUM(size_xl), 0)) + ' .
		'ABS(IFNULL(SUM(size_2xl), 0)) + ' .
		'ABS(IFNULL(SUM(size_3xl), 0)) + ' .
		'ABS(IFNULL(SUM(size_4xl), 0))' .
		') AS totalSum',
	];

    /**
     * Преобразовать переданный размер в формат self::fields
     * @param $sizeStr
     * @return mixed
     * @throws \Exception
     */
	static function getFieldSize($sizeStr) {

        if (in_array($sizeStr, self::fields)) {
            return $sizeStr;
        } else {
            if (isset(self::revers[$sizeStr])) {
                return self::revers[$sizeStr];
            } else {
                throw new \Exception("Странный размер {$sizeStr}");
            }
        }
    }

}