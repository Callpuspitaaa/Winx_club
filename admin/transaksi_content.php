<?php
// File: admin/transaksi_content.php

// Pastikan variabel $mysqli sudah ada dari file pemanggil (kas.php)
if(!isset($mysqli)) {
    die("File ini tidak bisa diakses langsung.");
}

// Ambil semua data transaksi umum dari tabel 'kas'
$sql_transaksi = "SELECT kas.*, users.username as admin_name FROM kas JOIN users ON kas.created_by = users.id ORDER BY tanggal DESC, id DESC";
$result_transaksi = $mysqli->query($sql_transaksi);

?>

<div class="card">
    <h3>Tambah Transaksi Baru (Pemasukan/Pengeluaran)</h3>
    <?php 
    if(isset($_GET['error'])) {
        echo '<p style="color: red; margin-bottom: 1rem;">' . htmlspecialchars($_GET['error']) . '</p>';
    }
    ?>
    <form action="proses_transaksi.php" method="POST" class="simple-form">
        <div class="form-group">
            <label for="tanggal">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <input type="text" id="deskripsi" name="deskripsi" placeholder="Contoh: Beli air mineral untuk latihan" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="pemasukan">Pemasukan (Rp)</label>
                <input type="number" id="pemasukan" name="pemasukan" value="0">
            </div>
            <div class="form-group">
                <label for="pengeluaran">Pengeluaran (Rp)</label>
                <input type="number" id="pengeluaran" name="pengeluaran" value="0">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
    </form>
</div>

<div class="content-area" style="margin-top: 2rem;">
    <h3>Riwayat Transaksi</h3>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                    <th>Pemasukan</th>
                    <th>Pengeluaran</th>
                    <th>Dicatat oleh</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_transaksi && $result_transaksi->num_rows > 0): ?>
                    <?php while($row = $result_transaksi->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date("d M Y", strtotime($row['tanggal'])); ?></td>
                            <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                            <td style="color: green;">+ Rp <?php echo number_format($row['pemasukan'], 0, ',', '.'); ?></td>
                            <td style="color: red;">- Rp <?php echo number_format($row['pengeluaran'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['admin_name']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Belum ada riwayat transaksi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
