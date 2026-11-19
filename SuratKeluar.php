<?php
// Modul Surat Keluar & Arsip - Yunus Robani (202343501292)
class SuratKeluar {
    private $conn;
    public function __construct($db) { $this->conn = $db; }
    public function getAll() {
        return $this->conn->query("SELECT * FROM surat_keluar ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }
    public function simpanSuratKeluar($nomor, $tujuan, $perihal, $tanggal, $file, $userId) {
        if (empty($nomor) || empty($tujuan) || empty($perihal)) return ['status' => false, 'msg' => 'Semua kolom wajib diisi!'];
        if (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'pdf') return ['status' => false, 'msg' => 'Lampiran harus PDF!'];
        if ($file['size'] > 2097152) return ['status' => false, 'msg' => 'Maksimal 2MB!'];
        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
        $namaFile = 'SK_' . time() . '_' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], 'uploads/' . $namaFile);
        $stmt = $this->conn->prepare("INSERT INTO surat_keluar (nomor_surat, tujuan_surat, perihal, tanggal_kirim, file_lampiran, user_id) VALUES (:n, :t, :p, :tgl, :f, :u)");
        $stmt->execute([':n' => $nomor, ':t' => $tujuan, ':p' => $perihal, ':tgl' => $tanggal, ':f' => $namaFile, ':u' => $userId]);
        return ['status' => true, 'msg' => 'Surat keluar berhasil diajukan sebagai Draft.'];
    }
    public function setujuiSurat($id) {
        $this->conn->prepare("UPDATE surat_keluar SET status = 'Disetujui' WHERE id = :id")->execute([':id' => $id]);
        return ['status' => true, 'msg' => 'Surat keluar telah disetujui Pimpinan.'];
    }
}
?>
