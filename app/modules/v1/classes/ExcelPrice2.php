<?php

namespace app\modules\v1\classes;


use app\controllers\ApiController;
use app\extension\Sizes;
use app\models\AnxUser;
use app\modules\AppMod;
use app\modules\v1\models\pr\PrStorProd;
use app\modules\v1\models\ref\RefArtBlank;
use app\modules\v1\models\ref\RefBlankGroup;
use app\modules\v1\models\ref\RefCollectDiv;
use app\modules\v1\models\ref\RefProductPrint;
use app\modules\v1\models\sls\SlsItem;
use app\objects\Prices;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use Yii;
use function implode;

class ExcelPrice2
{

    private $objExcel;

    private $rests = [];
    private $restsPrint = [];
    private $nds = 0;

    // Отображать остатки
    private $flagRest = false;

    // Отображать кол-во проданного за период
    private $flagReport = false;

    // Объект Prices
    private $prices;

//    /**
//     * @var SlsPaysReport
//     */
//    private $salesReport;

    /**
     * ExcelPrice2 constructor.
     * @param $refArtBlanks
     * @param $refProductPrints
     * @param $nds
     * @param $flagRest
     * @param $flagReport - флаг ренедеринга кол-ва проданного за период
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \Throwable
     * @throws \yii\web\HttpException
     */
    function __construct($refArtBlanks, $refProductPrints, $mode, $nds, $flagRest, $flagReport)
    {

        if ((int)$nds > 0) {
            $this->nds = (int)$nds;
        }

//        if ($flagRest) {
//            $this->flagRest = true;
//        }
//
//        if ($flagReport) {
//            $this->flagReport = true;
//
//            $dateStart = \Yii::$app->request->get('start');
//            $dateEnd = \Yii::$app->request->get('end');
//            if ($dateEnd == '') $dateEnd = 'now';
//            $this->salesReport = new SlsPaysReport($dateStart, $dateEnd);
//        }

        $this->objExcel = new Spreadsheet();

        $this->prepRestProd();
        $this->prepRestProdPrint();

        /** @var RefBlankGroup[] $groups */
        $groups = RefBlankGroup::readAllSort();

        /** @var RefCollectDiv[] $categories */
        $categories = RefCollectDiv::find()->all();

        $numSheet = 0;

        $this->prices = new Prices();

        /// Рендерить индексную прайса
        $this->renderIndexSheet(0);
        $numSheet++;

        $sexArr = [
            ['id' => 1, 'code' => 'МУЖ', 'color' => 'F44336'],
            ['id' => 2, 'code' => 'ЖЕН', 'color' => '4CAF50'],
            ['id' => 3, 'code' => 'МАЛ', 'color' => 'F44336'],
            ['id' => 4, 'code' => 'ДЕВ', 'color' => '4CAF50'],
        ];

        if ($mode === 'assort') {
            // Рендерить листы прайса изделий (ассортиментная матрица)
            foreach ($categories as $category) {
                foreach ($sexArr as $sexInfo) {
                    $prods = array_merge(
                        RefArtBlank::readForPrice(null, $category->id, $sexInfo['id'], $refArtBlanks, 'assort'),
                        RefProductPrint::readForPrice(null, $category->id, $sexInfo['id'], $refProductPrints, 'assort'));

                    $cardProds = [];

                    foreach ($prods as $prod) {
                        $cardProds[] = new CardProd($prod);
                    }

                    if (count($cardProds) > 0) {
                        $this->renderProdSheet(
                            $numSheet,
                            $category->name . " " . $sexInfo['code'],
                            $sexInfo['color'],
                            $cardProds
                        );
                        $numSheet++;
                    }
                }
            }
        }

        if ($mode === 'discount') {
            // Рендерить листы прайса изделий (акционный товар)
            foreach ($groups as $group) {
                foreach ($sexArr as $sexInfo) {
                    $prods = array_merge(
                        RefArtBlank::readForPrice($group->id, null, $sexInfo['id'], $refArtBlanks, 'discount'),
                        RefProductPrint::readForPrice($group->id, null, $sexInfo['id'], $refProductPrints, 'discount'));

                    $cardProds = [];

                    foreach ($prods as $prod) {
                        $cardProds[] = new CardProd($prod);
                    }

                    if (count($cardProds) > 0) {
                        $this->renderProdSheet(
                            $numSheet,
                            $group->title . " " . $sexInfo['code'],
                            $sexInfo['color'],
                            $cardProds
                        );
                        $numSheet++;
                    }
                }
            }
        }

        // Устанавливает активный лист при открытии
        $this->objExcel->setActiveSheetIndex(0);
    }

    public function save()
    {
        $objWriter = IOFactory::createWriter($this->objExcel, 'Xlsx');
        /** @var AnxUser $user */
        $user = Yii::$app->getUser()->getIdentity();
        $userPricesDir = Yii::getAlias(AppMod::filesRout[AppMod::filesB2B_Prices]) . '/' . $user->url_key;
        if (!file_exists($userPricesDir)) {
            mkdir($userPricesDir);
        }

        $filename = Yii::$app->security->generateRandomString(128) . '.xlsx';
        $userPricePath = $userPricesDir . '/' . $filename;
        if (file_exists($userPricePath)) {
            unlink($userPricePath);
        }
        $objWriter->save($userPricePath);
        return $filename;
    }

    private function renderIndexSheet($num)
    {
        $sheet = $this->objExcel->createSheet($num);
        $sheet->setTitle("Пояснения");
        $sheet->getTabColor()->setRGB('FFEB3B');
        //$sheet->getProtection()->setSheet(true);


        //$sheet->getDefaultRowDimension()->setRowHeight(-1);

        $sheet->getRowDimension('1')->setRowHeight(30);


        $sheet->getColumnDimension('A')->setWidth(3);
        $sheet->getColumnDimension('B')->setWidth(120);


        // Цена в прайсе указана без учёта НДС
        $styleArray = [
            'font' => [
                'size' => 14
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ]
        ];
        $sheet->getStyle("B1")->applyFromArray($styleArray);

        // Перенос на всей колонке
        $styleArray = ['alignment' => ['wrap' => true]];
        $sheet->getStyle("B1:B20")->applyFromArray($styleArray);


        if ($this->nds > 0) {
            $sheet->setCellValue('B1', 'Цена в прайсе указана с учетом НДС');
        } else {
            $sheet->setCellValue('B1', 'Цена в прайсе указана без учёта НДС');
        }


        $sheet->setCellValue('B4', 'Прайс размещен на вкладках этого документа, разбит по группам товаров. Цветовые обозначения:');
        $sheet->setCellValue('B5', '- на складе более 10 шт.');
        $sheet->setCellValue('B6', '- на складе менее 10 шт.');
        $sheet->setCellValue('B7', '- товар отсутствует на складе');

        $sheet->getStyle('A5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()
            ->setRGB("C8E6C9");
        $sheet->getStyle('A6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()
            ->setRGB("FFE0B2");
        $sheet->getStyle('A7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()
            ->setRGB("FFCDD2");


        $sheet->getRowDimension('10')->setRowHeight(30);
        $sheet->setCellValue('B10',
            "* Для перехода по ссылкам без CTRL (В LibreOffice) зайдите в меню Сервис/Параметры/LibreOffice" .
            "/Безопасность/Параметры\n и снимите галочку \"Ctrl-щелчок необходим для перехода по ссылкам\""
        );

    }


    /**
     * @param $numSheet
     * @param $nameSheet
     * @param $colorSheet
     * @param $prods RefArtBlank[]
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function renderProdSheet($numSheet, $nameSheet, $colorSheet, $cardProds)
    {

        $sheet = $this->objExcel->createSheet($numSheet);
        $sheet->setTitle($nameSheet);
        $sheet->getTabColor()->setRGB($colorSheet);
        //$sheet->getProtection()->setSheet(true);

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);

        $line = 1;
        $posSumm = [];

        /** @var CardProd $prod */
        foreach ($cardProds as $prod) {

            ///
            ///  Характеристики
            ///
            $sheet->setCellValue("A{$line}", $prod->modelFk->fashion . ' ' . $prod->hClientArt);
            $sheet->mergeCells("A{$line}:B{$line}");
            $sheet->getStyle("A{$line}:E{$line}")->getFont()->setBold(true);
            $sheet->getStyle("A{$line}:E{$line}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $pos1 = $line + 1;
            $sheet->setCellValue("A{$pos1}", "Модель");
            $sheet->setCellValue("B{$pos1}", $prod->modelFk->hModelTitleShort3());

            $pos2 = $line + 2;
            $sheet->setCellValue("A{$pos2}", "Декор");
            $sheet->setCellValue("B{$pos2}", $prod->themeFk->hThemeDescript());

            $pos3 = $line + 3;
            $sheet->setCellValue("A{$pos3}", "Ткань");
            $sheet->setCellValue("B{$pos3}", $prod->fabricTypeFk->type_price);

            $pos4 = $line + 4;
            $sheet->setCellValue("A{$pos4}", "Состав");
            $sheet->setCellValue("B{$pos4}", $prod->fabricTypeFk->struct);

            $pos5 = $line + 5;
            $sheet->setCellValue("A{$pos5}", "Плотность");
            $sheet->setCellValue("B{$pos5}", $prod->fabricTypeFk->desity . " г/м2");

            ///
            ///  Заказ
            ///
            $sheet->setCellValue("C{$line}", "Размеры");
            $sheet->setCellValue("D{$line}", "Заказ");
            $sheet->setCellValue("E{$line}", "Цена/1шт");

            if ($this->flagRest) {
                $sheet->setCellValue("F{$line}", "Остатки");
            }

            if ($this->flagReport) {
                $sheet->setCellValue("F{$line}", "Продажи");
            }

//            $style = [
//                'alignment' => [
//                    'horizontal' => 'center',
//                    'vertical' => 'center',
//                ]
//            ];

            $sizePos = 1;
            foreach (Sizes::fields2 as $fSize) {
                $sPos = $sizePos + $line;
                $fPrice = Sizes::prices[$fSize];
                if ($prod->$fPrice > 0) {
                    $sheet->setCellValue("C{$sPos}", $prod->modelFk->hSizeStr($fSize));
                    $sheet->getStyle("D{$sPos}")->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($prod->printId === 1 ? $this->getColorForCell($prod->prodId, $fSize)
                            : $this->getColorForCellPrint($prod->prodId . '-' . $prod->printId, $fSize));

                    $price = $this->prices->getPrice($prod->prodId, $prod->printId, $fSize);
                    $price = round($price * (1 - $this->prices->getDiscount($prod->prodId, $prod->printId) / 100));
                    $sheet->setCellValue("E{$sPos}", $price);

//                    if ($this->flagRest) {
//                        //$sheet->setCellValue("F{$sPos}", $this->rests[$prod->id][$fSize]);
//                        $sheet->setCellValue("F{$sPos}", $this->getProdRest($prod->id, $fSize));
//                        $sheet->getStyle("F{$sPos}")->applyFromArray($style);
//                    }
//                    if ($this->flagReport) {
//                        $sheet->setCellValue("F{$sPos}", $this->salesReport->getSales($prod->id, 1, $fSize));
//                        $sheet->getStyle("F{$sPos}")->applyFromArray($style);
//                    }

                    $sizePos++;
                }
            }
            $summLine = $line + $sizePos;
            $sheet->setCellValue("C{$summLine}", "ИТОГО");

            // Бордер заказа
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '646369'],
                    ]
                ]
            ];
            $startPosUnprotect = $line + 1;
            $sheet->getStyle("C{$startPosUnprotect}:E{$summLine}")->applyFromArray($styleArray);

            // Снять защиту
            $endPosUnprotect = $summLine - 1;
            $sheet->getStyle("D{$startPosUnprotect}:D{$endPosUnprotect}")->getProtection()
                ->setLocked(Protection::PROTECTION_UNPROTECTED);

            // Формула Итого
            $formula1 = "=SUM(D{$startPosUnprotect}:D{$endPosUnprotect})";
            $sheet->setCellValue("D{$summLine}", $formula1);

            $arr = [];
            for ($i = $startPosUnprotect; $i <= $endPosUnprotect; $i++) {
                $arr[] = "(D{$i}*E{$i})";
            }
            $formula2 = '=' . implode('+', $arr);
            $sheet->setCellValue("E{$summLine}", $formula2);

            $posSumm[] = $summLine;

            ///
            ///  Фотографии
            ///
            $hDst = 182;
            $imgItems = [1 => 'G', 2 => 'J', 3 => 'M', 4 => 'P'];
            foreach ($imgItems as $imgNum => $imgCol) {
                if ($prod->printId === 1) {
                    $path = realpath(Yii::getAlias(AppMod::filesRout[AppMod::filesImageBaseProds]));
                } else {
                    $path = realpath(Yii::getAlias(AppMod::filesRout[AppMod::filesImageProdsPrints]));
                }

                if ($prod->printId === 1) {
                    $_fileName = str_pad($prod->prodId, 4, '0', STR_PAD_LEFT) . '_' . $imgNum;
                } else {
                    $_fileName = str_pad($prod->prodId, 4, '0', STR_PAD_LEFT) . '-' .
                        str_pad($prod->printId, 3, '0', STR_PAD_LEFT) . '_' . $imgNum;
                }

                $fileName = $_fileName . '.jpg';
                $fileNameSmall = $_fileName . '.sm.jpg';
                $fullPath = $path . '/' . $fileNameSmall;

                if ($prod->printId === 1) {
                    $url = CURRENT_API_URL . '/v1/files/public/' . AppMod::filesImageBaseProds . '/' . $fileName;
                } else {
                    $url = CURRENT_API_URL . '/v1/files/public/' . AppMod::filesImageProdsPrints . '/' . $fileName;
                }

                if (file_exists($fullPath)) {
                    $gdImage = imagecreatefromjpeg($fullPath);
                    $objDrawing = new MemoryDrawing();
                    $objDrawing->setImageResource($gdImage);
                    $objDrawing->setRenderingFunction(MemoryDrawing::RENDERING_JPEG);
                    $objDrawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
                    $objDrawing->setHeight($hDst);
                    $objDrawing->setCoordinates("{$imgCol}" . ($line + 1));
                    $objDrawing->setWorksheet($sheet);
                    $sheet->getCell("{$imgCol}{$line}")->getHyperlink()->setUrl($url);
                    $sheet->getCell("{$imgCol}{$line}")->getStyle()->getFont()->setUnderline(true);
                    $sheet->getCell("{$imgCol}{$line}")->getStyle()->getFont()->getColor()->setRGB('0077ff');
                    $sheet->setCellValue("{$imgCol}{$line}", "УВЕЛИЧИТЬ");
                }
            }


            $line = $line + 13;
        }

        // Общее итого по листу
        $arr3 = [];
        $arr4 = [];
        foreach ($posSumm as $pos) {
            $arr3[] = "D{$pos}";
            $arr4[] = "E{$pos}";
        }

        $formula3 = "=" . implode('+', $arr3);
        $formula4 = "=" . implode('+', $arr4);

        $sheet->setCellValue("D{$line}", $formula3);
        $sheet->setCellValue("E{$line}", $formula4);


        $sheet->mergeCells("A{$line}:B{$line}");

        $sheet->getStyle("A{$line}:B{$line}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle("A{$line}:E{$line}")->getFont()->setBold(true);
        $sheet->setCellValue("A{$line}", "ИТОГО {$nameSheet}:");

    }


    /**
     * @param $numSheet
     * @param $nameSheet
     * @param $colorSheet
     * @param $prods RefProductPrint[]
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function renderProdPrintSheet($numSheet, $nameSheet, $colorSheet, $prods)
    {

        $sheet = $this->objExcel->createSheet($numSheet);
        $sheet->setTitle($nameSheet);
        $sheet->getTabColor()->setRGB($colorSheet);
        //$sheet->getProtection()->setSheet(true);

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);

        $line = 1;
        $posSumm = [];

        foreach ($prods as $prod) {

            ///
            ///  Характиристики
            ///
            $sheet->setCellValue("A{$line}",
                $prod->blankFk->modelFk->classFk->title . ' ' . $prod->blankFk->hClientArt($prod->print_fk));
            $sheet->mergeCells("A{$line}:B{$line}");
            $sheet->getStyle("A{$line}:E{$line}")->getFont()->setBold(true);
            $sheet->getStyle("A{$line}:E{$line}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $pos1 = $line + 1;
            $sheet->setCellValue("A{$pos1}", "Модель");
            $sheet->setCellValue("B{$pos1}", $prod->blankFk->modelFk->hModelTitleShort3());

            $pos2 = $line + 2;
            $sheet->setCellValue("A{$pos2}", "Декор");
            $sheet->setCellValue("B{$pos2}", $prod->blankFk->themeFk->hThemeDescript());

            $pos3 = $line + 3;
            $sheet->setCellValue("A{$pos3}", "Ткань");
            $sheet->setCellValue("B{$pos3}", $prod->blankFk->fabricTypeFk->type_price);

            $pos4 = $line + 4;
            $sheet->setCellValue("A{$pos4}", "Состав");
            $sheet->setCellValue("B{$pos4}", $prod->blankFk->fabricTypeFk->struct);

            $pos5 = $line + 5;
            $sheet->setCellValue("A{$pos5}", "Плотность");
            $sheet->setCellValue("B{$pos5}", $prod->blankFk->fabricTypeFk->desity . " г/м2");


//            $style = [
//                'alignment' => [
//                    'horizontal' => 'center',
//                    'vertical' => 'center',
//                ]
//            ];

            ///
            ///  Заказ
            ///
            $sheet->setCellValue("C{$line}", "Размеры");
            $sheet->setCellValue("D{$line}", "Заказ");
            $sheet->setCellValue("E{$line}", "Цена/1шт");
            $sizePos = 1;
            foreach (Sizes::fields2 as $fSize) {
                $sPos = $sizePos + $line;
                $fPrice = Sizes::prices[$fSize];
                if ($prod->$fPrice > 0) {
                    $sheet->setCellValue("C{$sPos}", $prod->blankFk->modelFk->hSizeStr($fSize));
                    $sheet->getStyle("D{$sPos}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()
                        ->setRGB($this->getColorForCellPrint("{$prod->blank_fk}-{$prod->print_fk}", $fSize));
                    $price = $this->prices->getPrice($prod->blank_fk, $prod->print_fk, $fSize);
                    $price = round($price * (1 - $this->prices->getDiscount($prod->id, $prod->print_fk) / 100));
                    $sheet->setCellValue("E{$sPos}", $price);

//                    if ($this->flagRest) {
//                        //$sheet->setCellValue("F{$sPos}", $this->restsPrint["{$prod->blank_fk}-{$prod->print_fk}"][$fSize]);
//                        $sheet->setCellValue("F{$sPos}", $this->getProdPrintRest($prod->blank_fk, $prod->print_fk, $fSize));
//                        $sheet->getStyle("F{$sPos}")->applyFromArray($style);
//                    }
//                    if ($this->flagReport) {
//                        // http://textile/api/excel-price?flagReport=1&start=01.05.2019&end=20.08.2019
//                        $count = $this->salesReport->getSales($prod->blank_fk, $prod->print_fk, $fSize);
//                        $sheet->setCellValue("F{$sPos}", $count);
//                        $sheet->getStyle("F{$sPos}")->applyFromArray($style);
//                    }

                    $sizePos++;
                }
            }
            $summLine = $line + $sizePos;
            $sheet->setCellValue("C{$summLine}", "ИТОГО");

            // Бордер заказа
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '646369'],
                    ]
                ]
            ];
            $startPosUnprotect = $line + 1;
            $sheet->getStyle("C{$startPosUnprotect}:E{$summLine}")->applyFromArray($styleArray);

            // Снять защиту
            $endPosUnprotect = $summLine - 1;
            $sheet->getStyle("D{$startPosUnprotect}:D{$endPosUnprotect}")->getProtection()
                ->setLocked(Protection::PROTECTION_UNPROTECTED);

            // Формула Итого
            $formula1 = "=SUM(D{$startPosUnprotect}:D{$endPosUnprotect})";
            $sheet->setCellValue("D{$summLine}", $formula1);

            $arr = [];
            for ($i = $startPosUnprotect; $i <= $endPosUnprotect; $i++) {
                $arr[] = "(D{$i}*E{$i})";
            }
            $formula2 = '=' . implode('+', $arr);
            $sheet->setCellValue("E{$summLine}", $formula2);

            $posSumm[] = $summLine;


            ///
            ///  Фотографии
            ///
            $hDst = 182;
            $imgItems = [1 => 'G', 2 => 'J', 3 => 'M'];
            foreach ($imgItems as $imgNum => $imgCol) {
                $path = realpath(Yii::getAlias(AppMod::filesRout[AppMod::filesImageProdsPrints]));
                $_fileName = str_pad($prod->blank_fk, 4, '0', STR_PAD_LEFT) . '-' .
                    str_pad($prod->print_fk, 3, '0', STR_PAD_LEFT) . '_' . $imgNum;
                $fileName = $_fileName . '.jpg';
                $fileNameSmall = $_fileName . '.sm.jpg';
                $fullPath = $path . '/' . $fileNameSmall;
                $url = CURRENT_API_URL . '/v1/files/public/' . AppMod::filesImageProdsPrints . '/' . $fileName;

                if (file_exists($fullPath)) {
                    $gdImage = imagecreatefromjpeg($fullPath);
                    $objDrawing = new MemoryDrawing();
                    $objDrawing->setImageResource($gdImage);
                    $objDrawing->setRenderingFunction(MemoryDrawing::RENDERING_JPEG);
                    $objDrawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
                    $objDrawing->setHeight($hDst);
                    $objDrawing->setCoordinates("{$imgCol}" . ($line + 1));
                    $objDrawing->setWorksheet($sheet);
                    $sheet->getCell("{$imgCol}{$line}")->getHyperlink()->setUrl($url);
                    $sheet->getCell("{$imgCol}{$line}")->getStyle()->getFont()->setUnderline(true);
                    $sheet->getCell("{$imgCol}{$line}")->getStyle()->getFont()->getColor()->setRGB('0077ff');
                    $sheet->setCellValue("{$imgCol}{$line}", "УВЕЛИЧИТЬ");
                }
            }


            $line = $line + 13;
        }

        // Общее итого по листу
        $arr3 = [];
        $arr4 = [];
        foreach ($posSumm as $pos) {
            $arr3[] = "D{$pos}";
            $arr4[] = "E{$pos}";
        }

        $formula3 = "=" . implode('+', $arr3);
        $formula4 = "=" . implode('+', $arr4);

        $sheet->setCellValue("D{$line}", $formula3);
        $sheet->setCellValue("E{$line}", $formula4);


        $sheet->mergeCells("A{$line}:B{$line}");

        $sheet->getStyle("A{$line}:B{$line}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle("A{$line}:E{$line}")->getFont()->setBold(true);
        $sheet->setCellValue("A{$line}", "ИТОГО {$nameSheet}:");
    }


    /**
     * Подготовить массив с остатками по изделиям без принтов
     */
    private function prepRestProd()
    {
        $rests = PrStorProd::find()
            ->select(PrStorProd::selectSumParams)
            ->where(['print_fk' => 1])
            ->groupBy('blank_fk')
            ->asArray()
            ->all();

        $restsBlank = [];

        foreach ($rests as $line) {
            $restsBlank[$line["blank_fk"]] = $line;
        }

        $orders = SlsItem::readRestProdsForPrice();
        $ordersBlank = [];
        foreach ($orders as $line) {
            $ordersBlank[$line["blank_fk"]] = $line;
        }

        foreach ($restsBlank as $lineRest) {
            $blankId = $lineRest['blank_fk'];
            if (isset($ordersBlank[$blankId])) {
                $curOrder = $ordersBlank[$blankId];
            } else {
                $curOrder = [
                    'size_2xs' => 0,
                    'size_xs' => 0,
                    'size_s' => 0,
                    'size_m' => 0,
                    'size_l' => 0,
                    'size_xl' => 0,
                    'size_2xl' => 0,
                    'size_3xl' => 0,
                    'size_4xl' => 0,

                ];
            }
            $this->rests[$blankId] = [
                'size_2xs' => (int)$lineRest['size_2xs'] - (int)$curOrder['size_2xs'],
                'size_xs' => (int)$lineRest['size_xs'] - (int)$curOrder['size_xs'],
                'size_s' => (int)$lineRest['size_s'] - (int)$curOrder['size_s'],
                'size_m' => (int)$lineRest['size_m'] - (int)$curOrder['size_m'],
                'size_l' => (int)$lineRest['size_l'] - (int)$curOrder['size_l'],
                'size_xl' => (int)$lineRest['size_xl'] - (int)$curOrder['size_xl'],
                'size_2xl' => (int)$lineRest['size_2xl'] - (int)$curOrder['size_2xl'],
                'size_3xl' => (int)$lineRest['size_3xl'] - (int)$curOrder['size_3xl'],
                'size_4xl' => (int)$lineRest['size_4xl'] - (int)$curOrder['size_4xl'],
            ];
        }
    }

    /**
     * Подготовить массив с остатками по изделиям без принтов
     */
    private function prepRestProdPrint()
    {
        $rests = PrStorProd::find()
            ->select(PrStorProd::selectSumParams)
            ->where('print_fk != 1')
            ->groupBy('blank_fk, print_fk')
            ->asArray()
            ->all();

        $restsArr = [];

        foreach ($rests as $line) {

            $art = "{$line["blank_fk"]}-{$line["print_fk"]}";

            $restsArr[$art] = $line;
        }

        $orders = SlsItem::readRestProdsPrintForPrice();
        $ordersArr = [];
        foreach ($orders as $line) {
            $art = "{$line["blank_fk"]}-{$line["print_fk"]}";
            $ordersArr[$art] = $line;
        }

        foreach ($restsArr as $art => $lineRest) {


            if (isset($ordersArr[$art])) {
                $curOrder = $ordersArr[$art];
            } else {
                $curOrder = [
                    'size_2xs' => 0,
                    'size_xs' => 0,
                    'size_s' => 0,
                    'size_m' => 0,
                    'size_l' => 0,
                    'size_xl' => 0,
                    'size_2xl' => 0,
                    'size_3xl' => 0,
                    'size_4xl' => 0,

                ];
            }

            $this->restsPrint[$art] = [
                'size_2xs' => (int)$lineRest['size_2xs'] - (int)$curOrder['size_2xs'],
                'size_xs' => (int)$lineRest['size_xs'] - (int)$curOrder['size_xs'],
                'size_s' => (int)$lineRest['size_s'] - (int)$curOrder['size_s'],
                'size_m' => (int)$lineRest['size_m'] - (int)$curOrder['size_m'],
                'size_l' => (int)$lineRest['size_l'] - (int)$curOrder['size_l'],
                'size_xl' => (int)$lineRest['size_xl'] - (int)$curOrder['size_xl'],
                'size_2xl' => (int)$lineRest['size_2xl'] - (int)$curOrder['size_2xl'],
                'size_3xl' => (int)$lineRest['size_3xl'] - (int)$curOrder['size_3xl'],
                'size_4xl' => (int)$lineRest['size_4xl'] - (int)$curOrder['size_4xl'],
            ];
        }
    }


    private function getColorForCell($blankId, $size)
    {

        if (!isset($this->rests[$blankId][$size])) {
            return 'FFCDD2';
        }
        if ($this->rests[$blankId][$size] === 0) {
            return 'FFCDD2';
        }
        if (($this->rests[$blankId][$size] > 0) && ($this->rests[$blankId][$size] <= 10)) {
            return 'FFE0B2';
        }
        if (($this->rests[$blankId][$size] > 10)) {
            return 'C8E6C9';
        }

        return 'FFCDD2';
    }

    private function getColorForCellPrint($strId, $size)
    {

        if (!isset($this->restsPrint[$strId][$size])) {
            return 'FFCDD2';
        }
        if ($this->restsPrint[$strId][$size] === 0) {
            return 'FFCDD2';
        }
        if (($this->restsPrint[$strId][$size] > 0) && ($this->restsPrint[$strId][$size] <= 10)) {
            return 'FFE0B2';
        }
        if (($this->restsPrint[$strId][$size] > 10)) {
            return 'C8E6C9';
        }

        return 'FFCDD2';
    }

//    private function getProdRest($prodId, $fSize)
//    {
//        if (isset($this->rests[$prodId][$fSize])) {
//            return $this->rests[$prodId][$fSize];
//        } else {
//            return '';
//        }
//    }
//
//    private function getProdPrintRest($prodId, $printId, $fSize)
//    {
//        if (isset($this->restsPrint["{$prodId}-{$printId}"][$fSize])) {
//
//            return $this->restsPrint["{$prodId}-{$printId}"][$fSize];
//        } else {
//            return '';
//        }
//    }

}
