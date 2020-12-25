<?php

namespace PouyaSoft\AppzaBundle\Services;

use DateTime;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PouyaSoft\SDateBundle\Service\jDateService;
use Roromix\Bundle\SpreadsheetBundle\Factory;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ExcelExport
{
    private $phpSpreadsheet;
    private $propertyAccessor;
    private $jDate;

    public function __construct(Factory $phpSpreadsheet, PropertyAccessorInterface $propertyAccessor, jDateService $jDate)
    {
        $this->phpSpreadsheet = $phpSpreadsheet;
        $this->propertyAccessor = $propertyAccessor;
        $this->jDate = $jDate;
    }

    /**
     * @param array $data data to show
     * @param array $columnOptions array key value pair.
     *      [
     *          'property' => '',
     *          'title' => '',
     *          'width' => 30,
     *          'convert' => null, function($value = null, $row = null, $options = [])
     *          'sum' => true,
     * ...]
     * @param array $fileOptions array key value pair.
     *      [
     *          'name' => 'result',
     *          'size' => PageSetup::PAPERSIZE_A5,
     *          'orientation' => PageSetup::ORIENTATION_PORTRAIT,
     *          'freezePane' => 'A2',
     *          'sumTitleFirstCol' => false,
     *          'event_final' ==> null, function($activeSheet, $columnOptions)
     *          'event_output' ==> null, function($activeSheet, $columnOptions)
     * ...]
     * @return StreamedResponse
     */
    public function create($data = [], array $columnOptions = [], array $fileOptions = [])
    {
        $selectedData = [];
        foreach ($data as $row) {
            $newRow = [];
            foreach ($columnOptions as $columnOption) {
                /** @var Callable $toStringFunction */
                $toStringFunction = $columnOption['convert'] ?? [$this, 'valueToStringDefault'];

                $newRow[] = $toStringFunction(!empty($columnOption['property']) ? $this->propertyAccessor->getValue($row, $columnOption['property']) : null, $row, $columnOption);
            }
            $selectedData []= $newRow;
        }

        Cell::setValueBinder(new ExcelValueBinder());

        $phpExcelObject = $this->phpSpreadsheet->createSpreadsheet();
        $phpExcelObject->setActiveSheetIndex(0);
        $activeSheet = $phpExcelObject->getActiveSheet();

        $activeSheet->getPageSetup()
            ->setOrientation($fileOptions['orientation'] ?? PageSetup::ORIENTATION_PORTRAIT)
            ->setPaperSize($fileOptions['size'] ?? PageSetup::PAPERSIZE_A5)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setHorizontalCentered(true)
            ->setVerticalCentered(false)
            ->setRowsToRepeatAtTopByStartAndEnd(1, 2);

        $activeSheet->getPageMargins()
            ->setTop(1 * 0.3937008)
            ->setRight(1 * 0.3937008)
            ->setLeft(1 * 0.3937008)
            ->setBottom(1 * 0.3937008)
            ->setHeader(0)
            ->setFooter(0);

        foreach ($columnOptions as $key => $columnOption) {
            $columnDimension = $activeSheet->getColumnDimensionByColumn($key + 1);
            $columnDimension->setWidth($columnOption['width']);
            if(isset($columnOption['style'])) $activeSheet->getStyle($columnDimension->getColumnIndex())->applyFromArray($columnOption['style']);
//            $activeSheet->getStyle($col)->applyFromArray(
//                isset($headerData[4]) ? GlobalFunctions::arrayMergeDeep($styleDefault, $headerData[4]) : $styleDefault
//            ); todo test for need this (override style for col)
        }

        //write data
        $activeSheet
            ->fromArray(array_column($columnOptions, 'title'), null, 'A1')
            ->fromArray($selectedData, null, 'A2')
            ->setRightToLeft(true)
            ->freezePane(isset($fileOptions['freezePane']) ? strtoupper($fileOptions['freezePane']) : 'A2')
        ;

        $hasSum = false;
        $sumRow = $activeSheet->getHighestDataRow() + 1;

        for ($i = count($columnOptions) - 1 ; $i >= 0; $i --) {
            if(!empty($columnOptions[$i]['sum'])) {
                $columnString = Coordinate::stringFromColumnIndex($i+1);
                $activeSheet->setCellValueByColumnAndRow($i+1, $sumRow, '=sum('.$columnString.'2:'.$columnString.($sumRow-1).')');
                $hasSum = true;
            }
            elseif($hasSum && !$fileOptions['sumTitleFirstCol']) {
                $activeSheet->setCellValueByColumnAndRow($i+1, $sumRow, 'مجموع');
                $hasSum = false;
            }
            elseif($hasSum && $fileOptions['sumTitleFirstCol'] && $i==0) {
                $activeSheet->setCellValueByColumnAndRow($i+1, $sumRow, 'مجموع');
                $hasSum = false;
            }
        }

        $styleFull = array(
            'font' => array(
                'name' => 'Tahoma',
                'size' => 11,
            ),
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ),
//            'numberFormat' => [
//                'formatCode' => NumberFormat::FORMAT_TEXT,
//            ]
        );

        if(isset($fileOptions['event_final'])) $fileOptions['event_output']($activeSheet, $columnOptions);

        $worksheetDataDimension = $activeSheet->calculateWorksheetDataDimension();
        $activeSheet->getStyle($worksheetDataDimension)->applyFromArray($styleFull);
        $activeSheet->getPageSetup()->setPrintArea($worksheetDataDimension);
        $activeSheet->setAutoFilter($worksheetDataDimension);

        if(isset($fileOptions['event_output'])) $fileOptions['event_output']($activeSheet, $columnOptions);

        //output
        $writer = $this->phpSpreadsheet->createWriter($phpExcelObject, 'Xlsx');
        $response = $this->phpSpreadsheet->createStreamedResponse($writer);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s').' GMT');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . ($fileOptions['name'] ?? 'result') . '.xlsx"');

        return $response;
    }

    private function valueToStringDefault($value = null, $row = null, $options = [])
    {
        if($value instanceof DateTime)
            return $this->jDate->georgianToPersian($value, 'yyyy/MM/dd', 'fa', 'persian', true);

        if(is_bool($value))
            return $value ? 'بله' : 'خیر';

        return $value ?: '--';
    }
}