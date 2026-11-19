CREATE DATABASE IF NOT EXISTS db_epersen;
USE db_epersen;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin_tu', 'pimpinan') NOT NULL
);

CREATE TABLE surat_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(100) UNIQUE NOT NULL,
    asal_surat VARCHAR(150) NOT NULL,
    perihal TEXT NOT NULL,
    tanggal_terima DATE NOT NULL,
    file_pdf VARCHAR(255) NOT NULL,
    status ENUM('Baru', 'Terdisposisi') DEFAULT 'Baru',
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE disposisi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    surat_masuk_id INT NOT NULL,
    tujuan_bidang VARCHAR(100) NOT NULL,
    instruksi TEXT NOT NULL,
    tanggal_disposisi DATE NOT NULL,
    FOREIGN KEY (surat_masuk_id) REFERENCES surat_masuk(id)
);

CREATE TABLE surat_keluar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(100) UNIQUE NOT NULL,
    tujuan_surat VARCHAR(150) NOT NULL,
    perihal TEXT NOT NULL,
    tanggal_kirim DATE NOT NULL,
    file_lampiran VARCHAR(255) NOT NULL,
    status ENUM('Draft', 'Disetujui') DEFAULT 'Draft',
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
