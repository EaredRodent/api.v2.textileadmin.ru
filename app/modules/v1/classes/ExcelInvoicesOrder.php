<?php

namespace app\modules\v1\classes;

use app\extension\Sizes;
use app\modules\v1\models\ref\RefEan;
use app\modules\v1\models\sls\SlsItem;
use app\modules\v1\models\sls\SlsOrder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExcelInvoicesOrder
{


    private $objExcel;

    function __construct($orderId, $preorderId)
    {

        $this->objExcel = new Spreadsheet();
        $this->objExcel->setActiveSheetIndex(0);

        $this->objExcel->getActiveSheet()->getColumnDimension('A')->setWidth(11);

        $this->objExcel->getActiveSheet()->getColumnDimension('B')->setWidth(7);
        $this->objExcel->getActiveSheet()->getColumnDimension('C')->setWidth(80);
        $this->objExcel->getActiveSheet()->getColumnDimension('D')->setWidth(11);
        $this->objExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $this->objExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->objExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $this->objExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
        $this->objExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $this->objExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);

        $this->objExcel->getActiveSheet()->getStyle('A1:K1')->getFont()->setBold(true);

        //
        $this->objExcel->getActiveSheet()->setTitle('articles')
            ->setCellValue('A1', 'ИНН')
            ->setCellValue('B1', 'Номер')
            ->setCellValue('C1', 'Наименование')
            ->setCellValue('D1', 'Количество')
            ->setCellValue('E1', 'Цена')
            ->setCellValue('F1', 'Штрихкод')
            ->setCellValue('G1', 'Внутренний артикул')
            ->setCellValue('H1', 'Производственный артикул')
            ->setCellValue('I1', 'Группа')
            ->setCellValue('J1', 'Id группы');

        $activeSheet = $this->objExcel->getActiveSheet();
        $row = 2;

        /** @var $items SlsItem[] */
        if ($orderId > 0) {
            $items = SlsItem::find()
                ->where(['order_fk' => $orderId])
                ->joinWith('blankFk.modelFk.classFk.groupFk')
                ->joinWith('blankFk.fabricTypeFk')
                ->joinWith('blankFk.themeFk')
                ->orderBy('blank_fk, print_fk, pack_fk')
                ->all();
            $inn = SlsOrder::findOne($orderId)->clientFk->inn;
        }
        if ($preorderId > 0) {
            $items = SlsPreorderItem::readItemsSort($preorderId);
            $inn = SlsPreorder::findOne($preorderId)->clientFk->inn;
        }

        $activeSheet->setCellValue('A2', $inn);

        foreach ($items as $item) {

            foreach (Sizes::fields as $size) {

                $count = $item->$size;

                if ($count > 0) {

                    //if ($item->blankFk->modelFk->classFk->groupFk->flag_child_size) {
                    if ($item->blankFk->modelFk->isChildModel()) {
                        $sizeArr = Sizes::kids;
                    } else {
                        $sizeArr = Sizes::adults;
                    }
                    $sizeStr = $sizeArr[$size];

                    $artStr =
                        $item->blankFk->hArt2() . '.' .
                        $item->printFk->hArt() . '.' .
                        $item->packFk->hArt() . '.' .
                        $sizeStr;

                    //$nameStr = $item->blankFk->hClientTitle($size);
                    $nameStr = $item->blankFk->hTitleForDocs2($sizeStr, $item->printFk, $item->packFk);

//					if ($item->pack_fk > 1) {
//						$packStr = ' Упаковка: ' . mb_strtolower($item->packFk->title);
//					} else {
//						$packStr = '';
//					}

                    $innerArt = "{$item->blank_fk}.{$item->print_fk}.{$item->pack_fk}.{$sizeStr}";
                    $eanObj = RefEan::find()
                        ->where(['blank_fk' => $item->blank_fk])
                        ->andWhere(['print_fk' => $item->print_fk])
                        ->andWhere(['pack_fk' => $item->pack_fk])
                        ->andWhere(['size' => $sizeStr])
                        ->one();
                    if ($eanObj) {
                        $ean13str = $eanObj->hEan13();
                    } else {
                        $ean13str = 'не назначен';
                    }
                    $price = $item->hPrice3($size);
                    $groupName = $item->blankFk->modelFk->classFk->groupFk->title;
                    $groupId = $item->blankFk->modelFk->classFk->groupFk->id;

                    $activeSheet->setCellValue('B' . $row, $row - 1);
                    $activeSheet->setCellValue('C' . $row, $nameStr);
                    $activeSheet->setCellValue('D' . $row, $count);
                    $activeSheet->setCellValue('E' . $row, $price);
                    $activeSheet->setCellValue('F' . $row, $ean13str);
                    $activeSheet->setCellValue('G' . $row, $innerArt);
                    $activeSheet->setCellValue('H' . $row, $artStr);
                    $activeSheet->setCellValue('I' . $row, $groupName);
                    $activeSheet->setCellValue('J' . $row, $groupId);
                    $row++;

                }
            }
        }
    }

    public function send()
    {
        $objWriter = IOFactory::createWriter($this->objExcel, 'Xlsx');

        ob_end_clean();
        ob_start();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filename = "invoice.xlsx";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        //$objWriter->save(APP_ROOT .'/aaa2.xlsx');
    }

}