<?php
// Include TCPDF library
require_once('TCPDF/tcpdf.php');

// Include the PHP QR Code library
include('phpqrcode/qrlib.php');

// Include database connection
include("koneksi.php");

// Get the report ID from the URL
if (isset($_GET['id'])) {
    $id_laporan = $_GET['id'];

    // Fetch laporan data with join to armada table and sorting by isvalid
    $sql = "SELECT laporan.*, armada.layanan, armada.koridor, armada.operator 
            FROM laporan
            LEFT JOIN armada ON laporan.nomor_lambung = armada.nomor_lambung 
            WHERE laporan.id_laporan = ? AND laporan.isdelete = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_laporan);
    $stmt->execute();
    $result = $stmt->get_result();
    $laporan = $result->fetch_assoc();

    if (!$laporan) {
        echo "Laporan tidak ditemukan.";
        exit();
    }
} else {
    echo "Laporan ID tidak ditemukan.";
    exit();
}

// Create new PDF document
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Detail Laporan');
$pdf->SetSubject('Laporan Detail');

// Add a page
$pdf->AddPage();

// Set font for title
$pdf->SetFont('helvetica', 'B', 14);

// Add Title
$pdf->Cell(0, 14, 'Transport For Semarang | Forum Diskusi Transportasi Semarang', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Detail Laporan #' . $laporan['id_laporan'], 0, 1, 'C');

// Add content
$pdf->Ln(10); // Line break

// Table for laporan details

$pdf->Cell(40, 10, 'Nomor Lambung: ', 0, 0);
$pdf->Cell(150, 10, $laporan['nomor_lambung'], 0, 1);

$pdf->Cell(40, 10, 'Layanan: ', 0, 0);
$pdf->Cell(150, 10, $laporan['layanan'], 0, 1);

$pdf->Cell(40, 10, 'Koridor: ', 0, 0);
$pdf->Cell(150, 10, $laporan['koridor'], 0, 1);

$pdf->Cell(40, 10, 'Operator: ', 0, 0);
$pdf->Cell(150, 10, $laporan['operator'], 0, 1);

$pdf->Cell(40, 10, 'Keterangan: ', 0, 0);
$pdf->Multicell(150, 10, $laporan['keterangan'], 0, 1);

$pdf->Cell(40, 10, 'Lokasi: ', 0, 0);
$pdf->Cell(150, 10, $laporan['lokasi'], 0, 1);

$pdf->Cell(40, 10, 'Tanggal: ', 0, 0);
$pdf->Cell(150, 10, $laporan['tanggal'], 0, 1);

// Add image (Dokumentasi) with reduced size (50%)
if (!empty($laporan['dokumentasi'])) {
    $pdf->Ln(0); // Extra space between documentation and next section
    $pdf->Cell(0, 10, 'Dokumentasi:', 0, 1);
    $pdf->Image($laporan['dokumentasi'], 50, $pdf->GetY(), 50, 37.5, '', '', 'T', true, 150, '', false, false, 1);
}

// Add space before validation status (increased distance)
$pdf->Ln(52); // Increase space before validation status

// Add validation status
$pdf->Cell(40, 10, 'Status Validasi: ', 0, 0);
$status = ($laporan['isvalid'] == 1) ? 'Valid' : (($laporan['isvalid'] == 2) ? 'Ditolak' : 'Menunggu Validasi');
$pdf->Cell(150, 10, $status, 0, 1);

// Add Footer Text
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Laporan dicetak pada: ' . date('Y-m-d H:i:s'), 0, 1, 'C');

// Generate QR Code for "detail_laporan_umum.php" URL
$qr_url = 'https://example.com/detail_laporan_umum.php?id=' . $laporan['id_laporan'];

// Set QR code image
$tempfile = 'temp_qrcode.png'; // Temporary file to store the QR code image
QRcode::png($qr_url, $tempfile, 'L', 4, 4); // Generate QR Code and store in temp file

// Add QR code to PDF
$pdf->Ln(0); // Line break before QR code
$pdf->Cell(0, 10, 'Scan QR Code untuk melihat laporan lebih lanjut', 0, 1, 'C');
$pdf->Image($tempfile, 80, $pdf->GetY(), 50, 50, 'PNG');

// Clean up temporary QR code file
unlink($tempfile); // Delete the temporary file after embedding in the PDF

// Output the PDF (force download)
$pdf->Output('laporan_' . $laporan['id_laporan'] . '.pdf', 'I');
exit();
?>
