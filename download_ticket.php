<?php
ob_start();
error_reporting(0);
require('config.php');

$booking_id = intval($_GET['id'] ?? 0);

// Booking + Show + Movie (with poster) + User
$stmt = $pdo->prepare("SELECT b.*, s.show_time, m.title as movie_title, m.poster, u.name as user_name 
                       FROM bookings b 
                       JOIN shows s ON b.show_id = s.id 
                       JOIN movies m ON s.movie_id = m.id 
                       JOIN users u ON b.user_id = u.id 
                       WHERE b.id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$booking) {
    ob_end_clean();
    die("Booking not found!");
}

require('fpdf/fpdf.php');
ob_end_clean();

$pdf = new FPDF('P','mm',array(100,190));
$pdf->AddPage();

// ===== HEADER (Dark Blue + Poster) =====
$pdf->SetFillColor(30,30,60);
$pdf->Rect(0,0,100,45,'F');

if(!empty($booking['poster']) && file_exists($booking['poster'])) {
    $pdf->Image($booking['poster'], 8, 8, 25, 35);
}

$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',18);
$pdf->SetXY(38,12);
$pdf->Cell(60,8,'CINEMA TICKET',0,1,'C');
$pdf->SetFont('Arial','',9);
$pdf->SetX(38);
$pdf->Cell(60,6,'ADMIT ONE - '.$booking['id'],0,1,'C');

// ===== MOVIE NAME (Golden Bar) =====
$pdf->SetFillColor(255,215,0);
$pdf->Rect(0,45,100,15,'F');
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','B',16);
$pdf->SetXY(0,48);
$pdf->Cell(100,8,$booking['movie_title'],0,1,'C');

// ===== DETAILS BOX =====
$pdf->SetFillColor(245,245,250);
$pdf->Rect(5,65,90,45,'F');

$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(120,120,120);
$pdf->SetXY(10,70);
$pdf->Cell(35,5,'NAME',0,0);
$pdf->Cell(25,5,'SEATS',0,0);
$pdf->Cell(30,5,'TIME',0,1);

$pdf->SetFont('Arial','B',10);
$pdf->SetTextColor(0,0,0);
$pdf->SetX(10);
$pdf->Cell(35,7,$booking['user_name'],0,0);
$pdf->Cell(25,7,$booking['seats'],0,0);
$pdf->Cell(30,7,date('h:i A', strtotime($booking['show_time'])),0,1);

$pdf->Ln(3);
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(120,120,120);
$pdf->SetX(10);
$pdf->Cell(40,5,'DATE',0,0);
$pdf->Cell(45,5,'AMOUNT',0,1);

$pdf->SetFont('Arial','B',12);
$pdf->SetTextColor(0,0,0);
$pdf->SetX(10);
$pdf->Cell(40,7,date('d M Y', strtotime($booking['booking_time'])),0,0);
$pdf->SetTextColor(220,20,60);
$pdf->Cell(45,7,'Rs '.$booking['total_amount'],0,1);

// ===== DASHED CUT LINE =====
$pdf->SetDrawColor(180,180,180);
for($i=5;$i<95;$i+=3){
    $pdf->Line($i,115,$i+1,115);
}

// ===== BETTER BARCODE (thin professional lines) =====
$pdf->SetFillColor(0,0,0);
$x = 8;
$code = str_pad($booking['id'], 6, '0', STR_PAD_LEFT) . preg_replace('/[^A-Z0-9]/', '', $booking['seats']);
for($i=0; $i<strlen($code)*3; $i++) {
    $char = $code[$i % strlen($code)];
    $height = 18 + (ord($char) % 12);
    $width = 0.5 + (ord($char) % 4) * 0.3;
    if($i % 2 == 0) {
        $pdf->Rect($x, 125, $width, $height, 'F');
    }
    $x += $width + 0.2;
}
$pdf->SetFont('Courier','B',9);
$pdf->SetTextColor(0,0,0);
$pdf->SetXY(0,148);
$pdf->Cell(100,5,$booking['id'].'-'.$booking['seats'],0,1,'C');

// ===== FOOTER =====
$pdf->SetFont('Arial','I',8);
$pdf->SetTextColor(150,150,150);
$pdf->SetXY(0,165);
$pdf->Cell(100,5,'Valid for one person only - Non transferable',0,1,'C');
$pdf->SetXY(0,172);
$pdf->Cell(100,5,'Show this ticket at entry',0,1,'C');

$pdf->Output('D','ticket_'.$booking_id.'.pdf');
?>