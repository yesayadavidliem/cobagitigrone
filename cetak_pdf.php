<?php
require('TCPDF/tcpdf.php');
include('koneksi.php');

// Periksa ID notulensi
if (!isset($_GET['id'])) {
    header("Location: manajemen_notulen.php");
    exit();
}

$id_notulensi = intval($_GET['id']);

// Ambil data notulensi
$sql = "SELECT * FROM notulensi WHERE id_notulensi = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_notulensi);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data || $data['issign'] == 0) {
    header("Location: manajemen_notulen.php");
    exit();
}

// Kelas PDF khusus untuk header dan footer
class MYPDF extends TCPDF {
    // Header
    public function Header() {
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 15, 'Notulensi Rapat', 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    // Footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Halaman '.$this->getAliasNumPage().' dari '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Inisialisasi PDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistem Notulensi');
$pdf->SetTitle('Notulensi Rapat');
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Tambahkan halaman
$pdf->AddPage();

// Judul
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Detail Notulensi', 0, 1, 'C');
$pdf->Ln(5);

// Informasi Notulensi
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(40, 10, 'ID Notulensi:', 0, 0);
$pdf->Cell(0, 10, $data['id_notulensi'], 0, 1);

$pdf->Cell(40, 10, 'Tanggal:', 0, 0);
$pdf->Cell(0, 10, $data['tanggal_notulen'], 0, 1);

$pdf->Cell(40, 10, 'Judul:', 0, 0);
$pdf->MultiCell(0, 10, $data['judul_notulen']);

$pdf->Cell(40, 10, 'CC:', 0, 0);
$pdf->MultiCell(0, 10, $data['cc_notulen']);

$pdf->Cell(40, 10, 'Isi:', 0, 0);
$pdf->MultiCell(0, 10, $data['isi_notulen']);

$pdf->Ln(5);

// Lampiran
if ($data['lampiran_notulen'] !== null) {
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Lampiran:', 0, 1);
    $pdf->Ln(5);

    // Simpan lampiran sementara ke file dengan format valid
    $lampiran_path = 'temp_lampiran.jpg';
    if (file_put_contents($lampiran_path, $data['lampiran_notulen'])) {
        $image_info = getimagesize($lampiran_path);
        if ($image_info && in_array($image_info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
            $pdf->Image($lampiran_path, '', '', 100, 100, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
        } else {
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Cell(0, 10, 'Lampiran tidak valid.', 0, 1);
        }
        unlink($lampiran_path); // Hapus file sementara
    } else {
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Gagal memproses lampiran.', 0, 1);
    }
} else {
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Tidak ada lampiran.', 0, 1);
}

// Output PDF
$pdf->Output('Notulensi_' . $data['id_notulensi'] . '.pdf', 'I');
?>
