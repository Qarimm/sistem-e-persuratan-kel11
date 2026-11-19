CREATE DATABASE IF NOT EXISTS db_epersen;
USE db_epersen;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin_tu', 'pimpinan') NOT NULL
);

CREATE TABLE IF NOT EXISTS surat_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(100) UNIQUE NOT NULL,
    asal_surat VARCHAR(150) NOT NULL,
    perihal TEXT NOT NULL,
    tanggal_terima DATE NOT NULL,
    file_pdf VARCHAR(255) NOT NULL,
    status ENUM('Baru', 'Terdisposisi') DEFAULT 'Baru',
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS disposisi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    surat_masuk_id INT NOT NULL,
    tujuan_bidang VARCHAR(100) NOT NULL,
    instruksi TEXT NOT NULL,
    tanggal_disposisi DATE NOT NULL,
    FOREIGN KEY (surat_masuk_id) REFERENCES surat_masuk(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS surat_keluar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(100) UNIQUE NOT NULL,
    tujuan_surat VARCHAR(150) NOT NULL,
    perihal TEXT NOT NULL,
    tanggal_kirim DATE NOT NULL,
    file_lampiran VARCHAR(255) NOT NULL,
    status ENUM('Draft', 'Disetujui') DEFAULT 'Draft',
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Akun Default untuk Pengujian (Password: password)
INSERT IGNORE INTO users (id, nama, username, password, role) VALUES
(1, 'Aulia Qarim (Admin TU)', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin_tu'),
(2, 'Yofi Afriarno (Pimpinan)', 'pimpinan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pimpinan');
