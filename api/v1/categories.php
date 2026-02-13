<?php
include 'auth_check.php';

// Ambil semua kategori dari tabel categories
$stmt = $pdo->query("SELECT id, nama_kategori FROM categories ORDER BY nama_kategori ASC");
$data = $stmt->fetchAll();

echo json_encode(['status' => 'success', 'data' => $data]);