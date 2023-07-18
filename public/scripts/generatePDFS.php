<?php
require("configurePDF.php");
require("./fpdf/fpdf.php");
function generateTourPDFForDate($markt, $date)
{
    class PDF extends FPDF
    {
        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 3.5, 'Seite ' . $this->PageNo(), 0, 0, 'C');
        }
    }
    $tourIDs = checkIfTourExistsForDay($markt, $date);
    $pdf = new PDF('L', 'mm', 'A4');
    $pdf->SetFont('Arial', '', 12);
    foreach ($tourIDs as $tid) {
        $tourData = getSortedDeliverysForTour($markt, $tid);
        $pdf->AddPage();
        $pdf->Cell(8, 15, conv(""), 1, 0);
        $pdf->Cell(25, 15, conv("Beleg"), 1, 0);
        $pdf->Cell(80, 15, conv("Kundendaten"), 1, 0);
        $pdf->Cell(80, 15, conv("Gerätedaten"), 1, 0);
        $pdf->Cell(30, 15, conv("Logistik"), 1, 0);
        $pdf->Cell(30, 15, conv("Spedition"), 1, 1);

        $tourCounter = 1;
        foreach ($tourData as $entry) {
            $lineValue = 15;
            $entry = explode("(-(-)-)", $entry);

            $pdf->Cell(8, 15, conv($tourCounter), 1, 0, 'C');
            $pdf->Cell(25, 15, conv($entry[1]), 1, 0);
            $adress = conv($entry[5] . ' ' . $entry[4] . "\n" . $entry[6] . ' ' . $entry[7] . "\n" . $entry[8] . ' ' . $entry[9]);
            $y = $pdf->GetY();
            $x = $pdf->GetX();
            $pdf->MultiCell(80, 5, $adress, 1, 'J', false);
            $pdf->SetXY($x + 80, $y);

            $temp = explode("(--(-)--)", $entry[3]);
            $artikel = explode("---", $temp[0]);
            $geraete = explode("---", $temp[1]);
            $gesString = "";
            for ($i = 0; $i < sizeof($artikel); $i++) {
                $gesString = $gesString . $artikel[$i] . " " . $geraete[$i] . "\n";
            }
            $y = $pdf->GetY();
            $x = $pdf->GetX();
            $pdf->MultiCell(80, $lineValue / sizeof($artikel), conv($gesString), 1, 'J', false);
            $pdf->SetXY($x + 80, $y);

            $pdf->Cell(30, 15, conv(""), 1, 0);
            $pdf->Cell(30, 15, conv(""), 1, 1);

            $tourCounter++;
        }
    }
    $fileName =  "tourSchedule" . date_format(date_create($date), "dmy");
    $pdf->Output("D", $fileName);
}
function getDeliveryPDF($markt, $deliveryID)
{
    class PDF extends FPDF
    {
        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 3.5, 'Seite ' . $this->PageNo(), 0, 0, 'C');
        }
    }

    $delData = getDeliveryData($markt, $deliveryID);
    $pdf = new PDF('P', 'mm', 'A4');

    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetY(50);
    $pdf->SetX(20);

    $pdf->Cell(50, 6, conv($delData[4] . ', ' . $delData[5]), 0, 2);
    $pdf->Cell(50, 6, conv($delData[8] . ' ' . $delData[9]), 0, 2);
    $pdf->Cell(50, 6, conv($delData[6] . ' ' . $delData[7]), 0, 0);
    $pdf->SetX(130);

    $pdf->Cell(20, 6, conv('Lieferdatum: ' . date_format(date_create($delData[1]), "d.m.Y")), 0, 2);
    $pdf->SetY(100);
    $pdf->SetX(20);

    $pdf->Cell(20, 6, conv('Belegnummer: ' . $delData[2]), 0, 2);
    $pdf->SetY(130);
    $pdf->SetX(50);
    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(40, 6, conv('Artikelnummer'), 0, 0);
    $pdf->Cell(40, 6, conv('Bezeichnung'), 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 12);

    $temp = explode("(--(-)--)", $delData[3]);
    $artikel = explode("---", $temp[0]);
    $geraete = explode("---", $temp[1]);

    for ($i = 0; $i < sizeof($artikel); $i++) {
        $pdf->SetX(50);
        $pdf->Cell(40, 6, conv($artikel[$i]), 0, 0);
        $pdf->Cell(40, 6, conv($geraete[$i]), 0, 1);
    }
    $fileName =  conv(date_format(date_create($delData[1]), "dmy") . '-' . $delData[0]);
    $pdf->Output("I", $fileName);
}
function conv($string)
{
    return iconv('UTF-8', 'windows-1252', $string);
}
?>