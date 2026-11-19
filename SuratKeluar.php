<?php
// Modul Surat Keluar & Arsip - Yunus Robani (202343501292)
class SuratKeluar {
    private $conn;

    public function __construct($db) {
        $this->conn =$db;
    }

    public function getAll() {
        $stmt =$this->conn->query("SELECT * FROM surat_keluar ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function simpanSuratKeluar($nomor,$tujuan, $perihal,$tanggal, $file,$userId) {
        if (empty($nomor) || empty($tujuan) \vert{}\vert{} empty($perihal)) {
            return ['status' => false, 'msg' => 'Nomor surat, tujuan, dan perihal wajib diisi!'];
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            return ['status' => false, 'msg' => 'Lampiran harus berformat PDF!'];
        }
        if ($file['size'] > 2097152) {
            return ['status' => false, 'msg' => 'Ukuran file maksimal 2MB!'];
        }

        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
        $namaFile = 'SK_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
        move_uploaded_file($file['tmp_name'], 'uploads/' .$namaFile);

        $sql = "INSERT INTO surat_keluar (nomor_surat, tujuan_surat, perihal, tanggal_kirim, file_lampiran, user_id) 
                VALUES (:nomor, :tujuan, :perihal, :tanggal, :file, :uid)";
        $stmt =$this->conn->prepare($sql);$stmt->execute([
            ':nomor' => $nomor,
            ':tujuan' => $tujuan,
            ':perihal' => $perihal,
            ':tanggal' => $tanggal,
            ':file' => $namaFile,
            ':uid' => $userId
        ]);
        return ['status' => true, 'msg' => 'Surat keluar berhasil diajukan sebagai Draft.'];
    }

    public function setujuiSurat($id) {
        $stmt =$this->conn->prepare("UPDATE surat_keluar SET status = 'Disetujui' WHERE id = :id");
        $stmt->execute([':id' =>$id]);
        return ['status' => true, 'msg' => 'Surat keluar telah disetujui Pimpinan.'];
    }
}
?>
