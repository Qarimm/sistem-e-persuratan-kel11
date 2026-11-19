<?php class SuratMasuk { public function tambahSurat($asal, $file) { if (empty($asal)) return 'Asal surat wajib diisi'; if ($file['ext'] !== 'pdf') return 'Format berkas harus PDF'; return 'Surat masuk berhasil dicatat'; } } ?>
// Modul Surat Masuk & Disposisi - Yofi Afriarno (202343501166)
