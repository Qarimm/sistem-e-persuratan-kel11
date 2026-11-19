<?php session_start(); echo '<h1>Sistem Informasi E-Persuratan - Kelompok 11</h1>'; ?>
                                <?php if ($row['status'] === 'Terdisposisi'): ?>
                                    <small><strong><?= htmlspecialchars($row['tujuan_bidang']) ?>:</strong> <?= htmlspecialchars($row['instruksi']) ?></small>
                                <?php elseif ($_SESSION['user']['role'] === 'pimpinan'): ?>
                                    <form method="POST" class="d-flex gap-1">
                                        <input type="hidden" name="surat_masuk_id" value="<?= $row['id'] ?>">
                                        <input type="text" name="tujuan_bidang" class="form-control form-control-sm" placeholder="Bidang" required>
                                        <input type="text" name="instruksi" class="form-control form-control-sm" placeholder="Instruksi" required>
                                        <button type="submit" name="simpan_disposisi" class="btn btn-sm btn-primary">Kirim</button>
                                    </form>
                                <?php else: ?>
                                    <small class="text-muted">Menunggu pimpinan</small>
                                <?php endif; ?>
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
                <?php if ($_SESSION['user']['role'] === 'admin_tu'): ?>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#formSK">+ Buat Surat Keluar</button>
                <?php endif; ?>
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
                    <thead class="table-light">
                        <tr><th>No. Surat</th><th>Tujuan</th><th>Perihal</th><th>Status</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listSK as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nomor_surat']) ?><br><small class="text-muted"><?= $row['tanggal_kirim'] ?></small></td>
                            <td><?= htmlspecialchars($row['tujuan_surat']) ?></td>
                            <td><?= htmlspecialchars($row['perihal']) ?></td>
                            <td><span class="badge bg-<?= $row['status'] === 'Disetujui' ? 'success' : 'warning text-dark' ?>"><?= $row['status'] ?></span></td>
                            <td>
                                <a href="uploads/<?= urlencode($row['file_lampiran']) ?>" target="_blank" class="btn btn-sm btn-outline-danger">PDF</a>
                                <?php if ($row['status'] === 'Draft' && $_SESSION['user']['role'] === 'pimpinan'): ?>
                                    <a href="?acc_sk=<?= $row['id'] ?>" class="btn btn-sm btn-success">Setujui</a>
                                <?php endif; ?>
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
