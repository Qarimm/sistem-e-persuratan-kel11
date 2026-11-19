<?php
// Modul Autentikasi & Dashboard - Aulia Qarim (202343501176)
session_start();
require_once 'config/database.php';
require_once 'SuratMasuk.php';
require_once 'SuratKeluar.php';

$database = new Database();
$db = $database->getConnection();
$smObj = new SuratMasuk($db);
$skObj = new SuratKeluar($db);
$alert = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && ($password === 'password' || password_verify($password, $user['password']))) {
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        $alert = "Username atau password salah!";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (isset($_SESSION['user'])) {
    $uid = $_SESSION['user']['id'];
    if (isset($_POST['tambah_sm'])) {
        $alert = $smObj->tambahSurat($_POST['nomor_surat'], $_POST['asal_surat'], $_POST['perihal'], $_POST['tanggal_terima'], $_FILES['file_pdf'], $uid)['msg'];
    }
    if (isset($_POST['simpan_disposisi'])) {
        $alert = $smObj->tambahDisposisi($_POST['surat_masuk_id'], $_POST['tujuan_bidang'], $_POST['instruksi'])['msg'];
    }
    if (isset($_POST['tambah_sk'])) {
        $alert = $skObj->simpanSuratKeluar($_POST['nomor_surat'], $_POST['tujuan_surat'], $_POST['perihal'], $_POST['tanggal_kirim'], $_FILES['file_lampiran'], $uid)['msg'];
    }
    if (isset($_GET['acc_sk'])) {
        $alert = $skObj->setujuiSurat($_GET['acc_sk'])['msg'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Persuratan - Kelompok 11</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold">E-Persuratan | Kelompok 11 Unindra</span>
        <?php if (isset($_SESSION['user'])): ?>
            <div class="text-white small">
                Halo, <strong><?= htmlspecialchars($_SESSION['user']['nama']) ?></strong> (<?= $_SESSION['user']['role'] ?>)
                <a href="?logout=1" class="btn btn-sm btn-outline-light ms-2">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</nav>
<div class="container pb-5">
    <?php if ($alert): ?><div class="alert alert-info"><?= htmlspecialchars($alert) ?></div><?php endif; ?>
    <?php if (!isset($_SESSION['user'])): ?>
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold">Login Sistem E-Persuratan</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" placeholder="admin / pimpinan" required></div>
                            <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" placeholder="password" required></div>
                            <button type="submit" name="login" class="btn btn-primary w-100">Masuk</button>
                        </form>
                        <hr><small class="text-muted">Demo: <code>admin</code> / <code>password</code> atau <code>pimpinan</code> / <code>password</code></small>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php
        $listSM = $smObj->getAll();
        $listSK = $skObj->getAll();
        $totalDisposisi = count(array_filter($listSM, fn($s) => $s['status'] === 'Terdisposisi'));
        ?>
        <div class="row mb-4 g-3">
            <div class="col-md-4"><div class="card border-primary"><div class="card-body"><h6 class="text-muted">Total Surat Masuk</h6><h3 class="fw-bold text-primary"><?= count($listSM) ?></h3></div></div></div>
            <div class="col-md-4"><div class="card border-success"><div class="card-body"><h6 class="text-muted">Surat Terdisposisi</h6><h3 class="fw-bold text-success"><?= $totalDisposisi ?></h3></div></div></div>
            <div class="col-md-4"><div class="card border-warning"><div class="card-body"><h6 class="text-muted">Total Surat Keluar</h6><h3 class="fw-bold text-warning"><?= count($listSK) ?></h3></div></div></div>
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                <span>Arsip Surat Masuk & Disposisi</span>
                <?php if ($_SESSION['user']['role'] === 'admin_tu'): ?><button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#formSM">+ Catat Surat Masuk</button><?php endif; ?>
            </div>
            <div class="collapse p-3 bg-light border-bottom" id="formSM">
                <form method="POST" enctype="multipart/form-data" class="row g-2">
                    <div class="col-md-3"><input type="text" name="nomor_surat" class="form-control" placeholder="Nomor Surat" required></div>
                    <div class="col-md-3"><input type="text" name="asal_surat" class="form-control" placeholder="Asal Instansi" required></div>
                    <div class="col-md-2"><input type="date" name="tanggal_terima" class="form-control" required></div>
                    <div class="col-md-4"><input type="file" name="file_pdf" accept=".pdf" class="form-control" required></div>
                    <div class="col-md-10"><input type="text" name="perihal" class="form-control" placeholder="Perihal Surat" required></div>
                    <div class="col-md-2"><button type="submit" name="tambah_sm" class="btn btn-success w-100">Simpan</button></div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light"><tr><th>No. Surat</th><th>Asal</th><th>Perihal</th><th>Status</th><th>Disposisi</th><th>Berkas</th></tr></thead>
                    <tbody>
                        <?php foreach ($listSM as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nomor_surat']) ?><br><small class="text-muted"><?= $row['tanggal_terima'] ?></small></td>
                            <td><?= htmlspecialchars($row['asal_surat']) ?></td>
                            <td><?= htmlspecialchars($row['perihal']) ?></td>
                            <td><span class="badge bg-<?= $row['status'] === 'Terdisposisi' ? 'success' : 'secondary' ?>"><?= $row['status'] ?></span></td>
                            <td>
                                <?php if ($row['status'] === 'Terdisposisi'): ?>
                                    <small><strong><?= htmlspecialchars($row['tujuan_bidang']) ?>:</strong> <?= htmlspecialchars($row['instruksi']) ?></small>
                                <?php elseif ($_SESSION['user']['role'] === 'pimpinan'): ?>
                                    <form method="POST" class="d-flex gap-1">
                                        <input type="hidden" name="surat_masuk_id" value="<?= $row['id'] ?>">
                                        <input type="text" name="tujuan_bidang" class="form-control form-control-sm" placeholder="Bidang" required>
                                        <input type="text" name="instruksi" class="form-control form-control-sm" placeholder="Instruksi" required>
                                        <button type="submit" name="simpan_disposisi" class="btn btn-sm btn-primary">Kirim</button>
                                    </form>
                                <?php else: ?><small class="text-muted">Menunggu pimpinan</small><?php endif; ?>
                            </td>
                            <td><a href="uploads/<?= urlencode($row['file_pdf']) ?>" target="_blank" class="btn btn-sm btn-outline-danger">PDF</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                <span>Arsip Surat Keluar</span>
                <?php if ($_SESSION['user']['role'] === 'admin_tu'): ?><button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#formSK">+ Buat Surat Keluar</button><?php endif; ?>
            </div>
            <div class="collapse p-3 bg-light border-bottom" id="formSK">
                <form method="POST" enctype="multipart/form-data" class="row g-2">
                    <div class="col-md-3"><input type="text" name="nomor_surat" class="form-control" placeholder="Nomor Surat" required></div>
                    <div class="col-md-3"><input type="text" name="tujuan_surat" class="form-control" placeholder="Tujuan Surat" required></div>
                    <div class="col-md-2"><input type="date" name="tanggal_kirim" class="form-control" required></div>
                    <div class="col-md-4"><input type="file" name="file_lampiran" accept=".pdf" class="form-control" required></div>
                    <div class="col-md-10"><input type="text" name="perihal" class="form-control" placeholder="Perihal Surat Keluar" required></div>
                    <div class="col-md-2"><button type="submit" name="tambah_sk" class="btn btn-success w-100">Ajukan</button></div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light"><tr><th>No. Surat</th><th>Tujuan</th><th>Perihal</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($listSK as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nomor_surat']) ?><br><small class="text-muted"><?= $row['tanggal_kirim'] ?></small></td>
                            <td><?= htmlspecialchars($row['tujuan_surat']) ?></td>
                            <td><?= htmlspecialchars($row['perihal']) ?></td>
                            <td><span class="badge bg-<?= $row['status'] === 'Disetujui' ? 'success' : 'warning text-dark' ?>"><?= $row['status'] ?></span></td>
                            <td>
                                <a href="uploads/<?= urlencode($row['file_lampiran']) ?>" target="_blank" class="btn btn-sm btn-outline-danger">PDF</a>
                                <?php if ($row['status'] === 'Draft' && $_SESSION['user']['role'] === 'pimpinan'): ?><a href="?acc_sk=<?= $row['id'] ?>" class="btn btn-sm btn-success">Setujui</a><?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
