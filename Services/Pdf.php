<?php

namespace PouyaSoft\AppzaBundle\Services;

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Pdf
{
    private $cacheDir;
    private $webDir;

    public function __construct($cacheDir, $webDir)
    {
        $this->cacheDir = $cacheDir;
        $this->webDir = $webDir;
    }

    public function generate($fileName, array $config, $html, $fileAppend = null, $returnResponse = true)
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $config_merge = array_merge([
            'mode' => 'utf-8',
            'direction' => 'rtl',
            'tempDir' => $this->cacheDir . '/mpdf_tmp/',
            'fontDir' => array_merge($fontDirs, [$this->webDir . '/bundles/pouyasoftappza/font']),
            'fontdata' => $fontData + [
                    'vazir' => ['R' => 'Vazir-FD.ttf', 'B' => 'Vazir-Bold-FD.ttf', 'useOTL' => 0xFF, 'useKashida' => 75]
                ],
            'default_font' => 'vazir'
        ], $config);

        try {
            $pdf = new Mpdf($config_merge);
            $pdf->SetDirectionality($config_merge['direction']);
            $pdf->shrink_tables_to_fit = 1;
            $pdf->WriteHTML($html);

            if($fileAppend) {
                $pageCount = $pdf->SetSourceFile($fileAppend);

                for ($p = 1; $p <= $pageCount; $p++) {
                    $pdf->WriteHTML('<pagebreak />');
                    $pdf->UseTemplate($pdf->importPage($p));
                }
            }

            $result = $pdf->Output($fileName, Destination::STRING_RETURN);

            return $returnResponse ? new Response($result, 200, [
                'content-type' => 'application/pdf',
                'content-disposition' => sprintf('inline; filename="%s"', $fileName)
            ]) : $result;
        } catch (MpdfException $e) {
            if($returnResponse)
                throw new BadRequestHttpException($e->getMessage(), $e);
            else
                return null;
        }
    }
}