<?php
// Modul Surat Masuk & Disposisi - Yofi Afriarno (202343501166)
class SuratMasuk {
    private $conn;

    public function __construct($db) {
        $this->conn =$db;
    }

    public function getAll() {
        $stmt =$this->conn->query("SELECT sm.*, d.tujuan_bidang, d.instruksi, d.tanggal_disposisi 
                                    FROM surat_masuk sm 
                                    LEFT JOIN disposisi d ON sm.id = d.surat_masuk_id 
                                    ORDER BY sm.id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tambahSurat($nomor,$asal, $perihal,$tanggal, $file,$userId) {
        if (empty($nomor) || empty($asal) \vert{}\vert{} empty($perihal)) {
            return ['status' => false, 'msg' => 'Semua kolom wajib diisi!'];
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            return ['status' => false, 'msg' => 'Format berkas harus PDF!'];
        }
        if ($file['size'] > 2097152) {
            return ['status' => false, 'msg' => 'Ukuran file maksimal 2MB!'];
        }

        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
        $namaFile = 'SM_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
        move_uploaded_file($file['tmp_name'], 'uploads/' .$namaFile);

        $sql = "INSERT INTO surat_masuk (nomor_surat, asal_surat, perihal, tanggal_terima, file_pdf, user_id) 
                VALUES (:nomor, :asal, :perihal, :tanggal, :file, :uid)";
        $stmt =$this->conn->prepare($sql);$stmt->execute([
            ':nomor' => $nomor,
            ':asal' => $asal,
            ':perihal' => $perihal,
            ':tanggal' => $tanggal,
            ':file' => $namaFile,
            ':uid' => $userId
        ]);
        return ['status' => true, 'msg' => 'Surat masuk berhasil dicatat.'];
    }

    public function tambahDisposisi($suratId,$tujuanBidang, $instruksi) {$sql = "INSERT INTO disposisi (surat_masuk_id, tujuan_bidang, instruksi, tanggal_disposisi) 
                VALUES (:sid, :tujuan, :instruksi, CURDATE())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':sid' =>$suratId, ':tujuan' => $tujuanBidang, ':instruksi' =>$instruksi]);

        $upd =$this->conn->prepare("UPDATE surat_masuk SET status = 'Terdisposisi' WHERE id = :sid");
        $upd->execute([':sid' =>$suratId]);
        return ['status' => true, 'msg' => 'Disposisi berhasil diteruskan.'];
    }
}
?>
