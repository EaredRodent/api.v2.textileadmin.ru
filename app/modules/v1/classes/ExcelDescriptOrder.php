<?php

namespace app\modules\v1\classes;


use app\controllers\ApiController;
use app\extension\Sizes;
use app\modules\AppMod;
use app\modules\v1\models\ref\RefEan;
use app\modules\v1\models\sls\SlsItem;
use app\objects\Prices;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Формирует эксель файл с писанием заказа
 * Class ExcelDescriptOrder
 * @package app\modules\sls\objects
 */
class ExcelDescriptOrder
{


    private $objExcel;

    function __construct($orderId, $preorderId)
    {

        $this->objExcel = new Spreadsheet();
        $this->objExcel->setActiveSheetIndex(0);

        $this->objExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);

        $this->objExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->objExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $this->objExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
        $this->objExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
        $this->objExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
        $this->objExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
        $this->objExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
        $this->objExcel->getActiveSheet()->getColumnDimension('I')->setWidth(17);
        $this->objExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->objExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);


        $this->objExcel->getActiveSheet()->getStyle('A1:K1')->getFont()->setBold(true);

        //
        $this->objExcel->getActiveSheet()->setTitle('articles')
            ->setCellValue('A1', 'Номер')
            ->setCellValue('B1', 'Артикул')
            ->setCellValue('C1', 'Наименование')
            ->setCellValue('D1', 'Декор')
            ->setCellValue('E1', 'Размер')
            ->setCellValue('F1', 'Кол-во')
            ->setCellValue('G1', 'Цена')
            ->setCellValue('H1', 'Сумма')
            ->setCellValue('I1', 'Штрихкод')
            ->setCellValue('J1', 'Базовая цена')
            ->setCellValue('K1', "Рекомендуемая розничная цена");

        //$sheet->getCell("G{$pos}")->getHyperlink()->setUrl('https://textileadmin.ru' . ApiController::urlGetPhoto . '?name=' . $name);


        $activeSheet = $this->objExcel->getActiveSheet();
        $row = 2;

        /** @var $items SlsItem[] */
        if ($orderId > 0) {
            $items = SlsItem::find()
                ->where(['order_fk' => $orderId])
                ->orderBy('blank_fk, print_fk, pack_fk')
                ->all();
        }
        if ($preorderId > 0) {
            $items = SlsPreorderItem::readItemsSort($preorderId);
        }


        $summTotal = 0;
        $countTotal = 0;

        $prices = new Prices();

        foreach ($items as $item) {

            foreach (Sizes::fields as $size) {

                $count = $item->$size;


                if ($count > 0) {


                    $art = $item->blankFk->hClientArt($item->print_fk);
                    $name = $item->blankFk->modelFk->hModelTitleShort6();
                    $theme = $item->blankFk->themeFk->title;
                    $print = ($item->print_fk > 1) ? "/{$item->printFk->title}" : '';

                    $sizeStr = $item->blankFk->modelFk->hSizeStr($size);

                    $fPrice = Sizes::prices[$size];
                    $price = $item->$fPrice;
                    $summ = $count * $price;
                    $eanObj = RefEan::find()
                        ->where(['blank_fk' => $item->blank_fk])
                        ->andWhere(['print_fk' => $item->print_fk])
                        ->andWhere(['pack_fk' => $item->pack_fk])
                        ->andWhere(['size' => $sizeStr])
                        ->one();
                    if ($eanObj) {
                        $ean13 = $eanObj->hEan13();
                    } else {
                        $ean13 = 'не назначен';
                    }
                    $basePrice = $prices->getPrice($item->blank_fk, $item->print_fk, $size);
                    $recommendedPrice = $basePrice * 2;


                    $activeSheet->setCellValue('A' . $row, $row - 1);
                    $activeSheet->setCellValue('B' . $row, $art);
                    $activeSheet->setCellValue('C' . $row, $name);
                    $activeSheet->setCellValue('D' . $row, $theme . $print);
                    $activeSheet->setCellValue('E' . $row, $sizeStr);
                    $activeSheet->setCellValue('F' . $row, $count);
                    $activeSheet->setCellValue('G' . $row, $price);
                    $activeSheet->setCellValue('H' . $row, $summ);
                    $activeSheet->setCellValue('I' . $row, $ean13);
                    $activeSheet->setCellValue('J' . $row, $basePrice);
                    $activeSheet->setCellValue('K' . $row, $recommendedPrice);

                    $arr = ['L' => 1, 'M' => 2, 'N' => 3, 'O' => 4];

                    //#ref - ссылки в экселе
                    foreach ($arr as $letter => $num) {
                        $url = '';

                        if ($item->print_fk == 1) {
                            $path = realpath(\Yii::getAlias(AppMod::filesRout[AppMod::filesImageBaseProds]));
                            $fileName = str_pad($item->blank_fk, 4, '0', STR_PAD_LEFT) . '_' . $num . '.jpg';
                            $fullPath = $path . '/' . $fileName;

                            if (file_exists($fullPath)) {
                                $url = AppMod::B2BAPIDomain . '/v1/files/public/' . AppMod::filesImageBaseProds . '/' . $fileName;
                            }
                        } else {
                            $path = realpath(\Yii::getAlias(AppMod::filesRout[AppMod::filesImageProdsPrints]));
                            $fileName = str_pad($item->blank_fk, 4, '0', STR_PAD_LEFT) . '-' .
                                str_pad($item->print_fk, 3, '0', STR_PAD_LEFT) . '_' . $num . '.jpg';
                            $fullPath = $path . '/' . $fileName;
                            if (file_exists($fullPath)) {
                                $url = AppMod::B2BAPIDomain . '/v1/files/public/' . AppMod::filesImageProdsPrints . '/' . $fileName;
                            }
                        }

                        if ($url) {
                            $activeSheet->setCellValue($letter . $row, "Фото {$num}");
                            $cellPhoto = $activeSheet->getCell("{$letter}{$row}");
                            $cellPhoto->getStyle()->getFont()->setUnderline(true);
                            $cellPhoto->getStyle()->getFont()->getColor()->setRGB('0077ff');
                            $cellPhoto->getHyperlink()->setUrl($url);
                        } else {
                            break;
                        }
                    }

                    $summTotal += $summ;
                    $countTotal += $count;
                    $row++;

                }
            }
        }

        $activeSheet->setCellValue('E' . $row, 'Итог:');
        $activeSheet->setCellValue('F' . $row, $countTotal);
        $activeSheet->setCellValue('G' . $row, '');
        $activeSheet->setCellValue('H' . $row, $summTotal);
        $this->objExcel->getActiveSheet()->getStyle("A{$row}:K{$row}")->getFont()->setBold(true);

    }

    public function send()
    {
        $objWriter = IOFactory::createWriter($this->objExcel, 'Xlsx');

        ob_end_clean();
        ob_start();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filename = "description.xlsx";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        //$objWriter->save(APP_ROOT .'/aaa2.xlsx');
    }

}