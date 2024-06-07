<?php
require('fpdf/fpdf.php');
require('vendor/autoload.php'); // Incluir el archivo de carga automática de Composer

use Carbon\Carbon; // Importar la clase Carbon

class PDF extends FPDF
{
        // Definir una variable para la imagen de fondo
        var $background;

        function SetBackground($img)
        {
            $this->background = $img;
        }
    // Cabecera de página
    function Header()
    {
                // Si hay una imagen de fondo, dibujarla
                if ($this->background) {
                    $this->Image($this->background, 0, 0, $this->GetPageWidth(), $this->GetPageHeight());
                }
        // Logo
        $this->Image('img/Logo-AGN.png', 69, 10, 75);
        $this->Ln(60);
     
        $this->SetFont('Arial', 'B', 15);
    }

    // Pie de página
    function Footer()
    {
        $this->SetFont('Arial', 'I', 8);
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $relativo = filter_input(INPUT_POST, 'relativo');
    $referencia = filter_input(INPUT_POST, 'refencia');
    $destinatario = filter_input(INPUT_POST, 'destinatario');
    $remitente = filter_input(INPUT_POST, 'remitente');
    $asunto = filter_input(INPUT_POST, 'asunto');
    $mensaje = filter_input(INPUT_POST, 'mensaje');

$pdf = new PDF();
$pdf->SetBackground('C:/wamp64/www/admin/horas sociales/img/WhatsApp Image 2024-06-03 at 9.45.55 AM.jpeg');
$pdf->addpage();
$pdf->addfont('BemboStd-Bold.ttf','',"BemboStd-Bold.php");


// Configurar localización para Carbon
setlocale(LC_TIME, 'es_ES.UTF-8');
Carbon::setLocale('es'); // Establecer el idioma de Carbon a español

$fecha = Carbon::now()->isoFormat('D [ / ]MMMM[ / ]YYYY');

// Establecer los márgenes en milímetros
$pdf->SetMargins(17, 20, 20);
$pdf->addfont('MuseoSans_500.ttf','',"MuseoSans_500.php");
$pdf->setfont('BemboStd-Bold.ttf', '', 17);
$pdf->Cell(185, 0, "MEMORANDUM", 0, 0, 'C');
$pdf->Ln(5);
$pdf->SetLeftMargin(92);
$pdf->setfont('MuseoSans_500.ttf', '', 14);
$pdf->Ln(5);
$pdf->MultiCell(0, 7, mb_convert_encoding('A.106.1.'.$relativo , 'ISO-8859-1', 'UTF-8'));
$pdf->Ln(5);
$pdf->MultiCell(0, 7, mb_convert_encoding('Ref. '.$referencia , 'ISO-8859-1', 'UTF-8'));

$pdf->setfont('MuseoSans_500.ttf', '', 14);
$pdf->SetLeftMargin(30);
$pdf->Ln(10);
$pdf->MultiCell(0, 7, mb_convert_encoding('Para:'.$destinatario. '.', 'ISO-8859-1', 'UTF-8'));
$pdf->Ln(7);
$pdf->MultiCell(0, 7, mb_convert_encoding('De:' .$remitente. '.' , 'ISO-8859-1', 'UTF-8'));
$pdf->Ln(7);
$pdf->MultiCell(0, 7, mb_convert_encoding('Fecha:  ' . $fecha . '.', 'ISO-8859-1', 'UTF-8'));
$pdf->Ln(7);
$pdf->MultiCell(0, 7, mb_convert_encoding('Asunto: ' .$asunto, 'ISO-8859-1', 'UTF-8'));

$pdf->Line(30, 170, 210-20, 170);

$pdf->setfont('MuseoSans_500.ttf', '', 14);
$pdf->SetLeftMargin(30);
$pdf->Ln(20);
$pdf->MultiCell(0, 7, mb_convert_encoding('Mensaje: '.$mensaje.'.' , 'ISO-8859-1', 'UTF-8'));
$pdf->Ln(3);
$pdf->MultiCell(0, 7, mb_convert_encoding('Atentamente,', 'ISO-8859-1', 'UTF-8'));

$pdf->Output();
}
?>
