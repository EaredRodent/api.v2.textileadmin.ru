<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 16.08.2018
 * Time: 15:45
 */

namespace app\objects;


use app\modules\v1\models\sls\SlsClient;
use app\modules\v1\models\sls\SlsMoney;
use app\modules\v1\models\sls\SlsOrder;

/**
 * Обортно-сальдовая ведомость
 * Входные данные период и тип оплаты
 *
 *
 * Class MoneyReport
 * @package app\modules\sls\objects
 */
class MoneyReport
{
	// Список всех клиентов в алфавитном порядке


	public $clients = [];

	public $itogo = [
		'prevDebet'  => 0,
		'prevKredit' => 0,
		'turnDebet'  => 0,
		'turnKredit' => 0,
		'endDebet'   => 0,
		'endKredit'  => 0,
	];

	function __construct($dateStart, $dateEnd, $payType = null)
	{
		// Формирование списка клиентов
		$clients = SlsClient::readAllSort();
		foreach ($clients as $client) {
			$this->clients[$client->id] = [
				'name' => $client->short_name,

				'_prevSummOrder' => 0,
				'_prevSummPay'   => 0,

				'prevDebet'  => 0,
				'prevKredit' => 0,
				'turnDebet'  => 0,
				'turnKredit' => 0,
				'endDebet'   => 0,
				'endKredit'  => 0,

				'_controlSumm' => 0,
			];
		}

		/// Расчет на начало периода

		// Заказы по безналу по клиентам на начало перода
		if ($payType !== SlsOrder::payCash) {
			$prevOrdersBank = SlsOrder::readOrdersBankDiapason($dateStart);
			foreach ($prevOrdersBank as $prevOrder) {
				$this->clients[$prevOrder->client_fk]['_prevSummOrder'] += $prevOrder->summ_order;
			}
		}

		// Заказы за нал по клиентам на начало перода
		if ($payType !== SlsOrder::payBank) {
			$prevOrdersCash = SlsOrder::readOrdersCashDiapason($dateStart);
			foreach ($prevOrdersCash as $prevOrder) {
				$this->clients[$prevOrder->client_fk]['_prevSummOrder'] += $prevOrder->summ_order;
			}
		}

		// Оплата по клиентам на начало периода
		$prevPays = SlsMoney::readForReport($dateStart, null, $payType);
		foreach ($prevPays as $prevPay) {
			$this->clients[$prevPay->orderFk->client_fk]['_prevSummPay'] += $prevPay->summ;
		}

		// Возврат по заказам
		$prevReturns = SlsMoney::readForReportReturns($dateStart, null, $payType);
		foreach ($prevReturns as $prevReturn) {
			$this->clients[$prevReturn->client_fk]['_prevSummPay'] -= abs($prevReturn->summ);
		}


		/// Расчет на период

		// Заказы по безналу по клиентам на перод
		if ($payType !== SlsOrder::payCash) {
			$turnOrdersBank = SlsOrder::readOrdersBankDiapason($dateStart, $dateEnd);
			foreach ($turnOrdersBank as $turnOrder) {
				$this->clients[$turnOrder->client_fk]['turnDebet'] += $turnOrder->summ_order;
			}
		}

		// Заказы за нал по клиентам перода
		if ($payType !== SlsOrder::payBank) {
			$turnOrdersCash = SlsOrder::readOrdersCashDiapason($dateStart, $dateEnd);
			foreach ($turnOrdersCash as $turnOrder) {
				$this->clients[$turnOrder->client_fk]['turnDebet'] += $turnOrder->summ_order;
			}
		}

		// Оплата по клиентам за период
		$turnPays = SlsMoney::readForReport($dateStart, $dateEnd, $payType);
		foreach ($turnPays as $turnPay) {
			$this->clients[$turnPay->orderFk->client_fk]['turnKredit'] += $turnPay->summ;
		}

		// Возврат по заказам
		$turnReturns = SlsMoney::readForReportReturns($dateStart, $dateEnd, $payType);
		foreach ($turnReturns as $turnReturn) {
			$this->clients[$turnReturn->client_fk]['turnKredit'] -= abs($turnReturn->summ);
		}


		/// Посчитать вычисляемые поля
		foreach ($this->clients as $clientId => $clientData) {

			// Сальдо на начало периода
			$summPrev = $clientData['_prevSummOrder'] - $clientData['_prevSummPay'];
			if ($summPrev > 0) {
				$this->clients[$clientId]['prevDebet'] = $summPrev;
			} else {
				$this->clients[$clientId]['prevKredit'] = abs($summPrev);
			}

			// Сальдо на конец периода
			$summEnd = $clientData['_prevSummOrder'] - $clientData['_prevSummPay'] +
				$clientData['turnDebet'] - $clientData['turnKredit'];
			if ($summEnd > 0) {
				$this->clients[$clientId]['endDebet'] = $summEnd;
			} else {
				$this->clients[$clientId]['endKredit'] = abs($summEnd);
			}

			$this->clients[$clientId]['_controlSumm'] =
				abs($this->clients[$clientId]['prevDebet']) +
				abs($this->clients[$clientId]['prevKredit']) +
				abs($this->clients[$clientId]['turnDebet']) +
				abs($this->clients[$clientId]['turnKredit']) +
				abs($this->clients[$clientId]['endDebet']) +
				abs($this->clients[$clientId]['endKredit']);

			// Суммы по столбцам
			$this->itogo['prevDebet'] += $this->clients[$clientId]['prevDebet'];
			$this->itogo['prevKredit'] += $this->clients[$clientId]['prevKredit'];
			$this->itogo['turnDebet'] += $this->clients[$clientId]['turnDebet'];
			$this->itogo['turnKredit'] += $this->clients[$clientId]['turnKredit'];
			$this->itogo['endDebet'] += $this->clients[$clientId]['endDebet'];
			$this->itogo['endKredit'] += $this->clients[$clientId]['endKredit'];
		}

	}

	public static function hMoney($summ)
	{

		if ($summ != 0) {
			$str = number_format($summ, 2, ',', ' ');
			return "<span class='s12-td_link'>{$str}</span>";

		} else {
			return '-';

		}


	}

}