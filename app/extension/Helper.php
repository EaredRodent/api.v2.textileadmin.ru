<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 03.10.2018
 * Time: 14:55
 */

namespace app\extension;


class Helper
{
	public static function strTranslitFileName($str)
	{
		$str = self::_cleanStr($str);
		$str = self::_translit($str);
		$str = preg_replace("/[^0-9a-z-_ \\(\\)\\[\\]\\.]/i", "", $str); // очищаем строку от недопустимых символов
		$str = str_replace(" ", "_", $str); // заменяем пробелы знаком минус
		return $str;
	}

    /**
     * Вернуть ean13 по префиксу и id товара
     * @param $prefix
     * @param $idProd
     * @return string
     */
	public static function getEan13($prefix, $idProd)
    {
        $id = str_pad($idProd, 5, '0', STR_PAD_LEFT);
        $ean12 = $prefix . $id;
        return self::eanCheckDigit($ean12);
    }

	private static function _cleanStr($str)
	{
		$s = (string)$str; // преобразуем в строковое значение
		$s = strip_tags($s); // убираем HTML-теги
		$s = str_replace(["\n", "\r"], " ", $s); // убираем перевод каретки
		$s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
		$s = trim($s); // убираем пробелы в начале и конце строки
		return $s;
	}

	private static function _translit($string)
	{
		$table = array(
			'А' => 'A',
			'Б' => 'B',
			'В' => 'V',
			'Г' => 'G',
			'Д' => 'D',
			'Е' => 'E',
			'Ё' => 'Yo',
			'Ж' => 'Zh',
			'З' => 'Z',
			'И' => 'I',
			'Й' => 'J',
			'К' => 'K',
			'Л' => 'L',
			'М' => 'M',
			'H' => 'N',
			'О' => 'O',
			'П' => 'P',
			'Р' => 'R',
			'С' => 'S',
			'Т' => 'T',
			'У' => 'U',
			'Ф' => 'F',
			'Х' => 'H',
			'Ц' => 'C',
			'Ч' => 'Ch',
			'Ш' => 'Sh',
			'Щ' => 'Csh',
			'Ь' => '',
			'Ы' => 'Y',
			'Ъ' => '',
			'Э' => 'E',
			'Ю' => 'Yu',
			'Я' => 'Ya',

			'а' => 'a',
			'б' => 'b',
			'в' => 'v',
			'г' => 'g',
			'д' => 'd',
			'е' => 'e',
			'ё' => 'yo',
			'ж' => 'zh',
			'з' => 'z',
			'и' => 'i',
			'й' => 'j',
			'к' => 'k',
			'л' => 'l',
			'м' => 'm',
			'н' => 'n',
			'о' => 'o',
			'п' => 'p',
			'р' => 'r',
			'с' => 's',
			'т' => 't',
			'у' => 'u',
			'ф' => 'f',
			'х' => 'h',
			'ц' => 'c',
			'ч' => 'ch',
			'ш' => 'sh',
			'щ' => 'csh',
			'ь' => '',
			'ы' => 'y',
			'ъ' => '',
			'э' => 'e',
			'ю' => 'yu',
			'я' => 'ya',
		);
		$output = str_replace(
			array_keys($table),
			array_values($table), $string
		);
		return $output;
	}

    private static function eanCheckDigit($digits)
    {
        //first change digits to a string so that we can access individual numbers
        $digits = (string)$digits;
        // 1. Add the values of the digits in the even-numbered positions: 2, 4, 6, etc.
        $even_sum = $digits{1} + $digits{3} + $digits{5} + $digits{7} + $digits{9} + $digits{11};
        // 2. Multiply this result by 3.
        $even_sum_three = $even_sum * 3;
        // 3. Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
        $odd_sum = $digits{0} + $digits{2} + $digits{4} + $digits{6} + $digits{8} + $digits{10};
        // 4. Sum the results of steps 2 and 3.
        $total_sum = $even_sum_three + $odd_sum;
        // 5. The check character is the smallest number which, when added to the result in step 4,  produces a multiple of 10.
        $next_ten = (ceil($total_sum / 10)) * 10;
        $check_digit = $next_ten - $total_sum;
        return $digits . $check_digit;
    }
}
