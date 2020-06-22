<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\extension\Sizes;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefProductPrint;
use app\modules\v1\models\sls\SlsItem;
use app\modules\v1\models\sls\SlsOrder;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
    {
        /** @var SlsOrder[] $slsOrders */
        $slsOrders = SlsOrder::find()
            ->where(['user_fk' => 21])
            ->andWhere(['>=', 'ts_send', '2020.00.00 00:00:00'])
            ->andWhere(['status' => 's7_send'])
            ->andWhere(['flag_return' => 0])
            ->all();

        $slsOrdersIDs = [];

        foreach ($slsOrders as $slsOrder) {
            $slsOrdersIDs[] = $slsOrder->id;
        }

        /** @var SlsItem[] $slsItems */
        $slsItems = SlsItem::find()
            ->where(['order_fk' => $slsOrdersIDs])
            ->all();


        $itemsCountAss = 0;
        $itemsPriceAss = 0;
        $itemsCountDis = 0;
        $itemsPriceDis = 0;

        foreach ($slsItems as $slsItem) {
            if($slsItem->print_fk === 1) {
                /** @var RefArtBlank $prod */
                $prod = RefArtBlank::find()->where(['id' => $slsItem->blank_fk])->one();
            } else {
                /** @var RefProductPrint $prod */
                $prod = RefProductPrint::find()->where(['blank_fk' => $slsItem->blank_fk, 'print_fk' => $slsItem->print_fk])->one();
            }

            $isAssortment = !!$prod->collection_fk;

            foreach (Sizes::prices as $size => $price) {
                if($slsItem->$size) {
                    if($isAssortment) {
                        $itemsCountAss += $slsItem->$size;
                        $itemsPriceAss += $slsItem->$size * $slsItem->$price;
                    } else {
                        $itemsCountDis += $slsItem->$size;
                        $itemsPriceDis += $slsItem->$size * $slsItem->$price;
                    }
                }
            }
        }

        echo "Count ass: {$itemsCountAss}\nPrice ass: {$itemsPriceAss}\nCount dis: {$itemsCountDis}\nPrice dis: {$itemsPriceDis}\n";

        return ExitCode::OK;
    }
}
