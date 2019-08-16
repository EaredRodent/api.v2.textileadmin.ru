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
}
