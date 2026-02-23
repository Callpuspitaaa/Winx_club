<?php
// File: admin/iuran_content.php

// Pastikan variabel $mysqli sudah ada dari file pemanggil (kas.php)
if(!isset($mysqli)) {
    die("File ini tidak bisa diakses langsung.");
}

// Logika untuk file ini sudah dipindahkan ke kas.php sebagai file utama
// Variabel seperti $tanggal_pilihan, $anggota, $status_iuran, $jumlah_iuran, $jumlah_lunas, $total_terkumpul
// diasumsikan sudah ada dari kas.php

?>

<div class="card">
    <h3>Pengaturan Jumlah Iuran</h3>
    <form action="proses_pengaturan_iuran.php" method="POST" class="simple-form">
        <div class="form-group">
            <label for="jumlah_iuran">Jumlah Iuran per Pertemuan (Rp)</label>
            <input type="number" id="jumlah_iuran" name="jumlah_iuran" value="<?php echo $jumlah_iuran; ?>" required>
        </div>
        <button type="submit" class="btn">Update Jumlah</button>
    </form>
</div>

<div class="content-area" style="margin-top: 2rem;">
    <h2>Data Kas Pertemuan</h2>
    <form method="GET" action="kas.php" class="simple-form" style="margin-bottom: 2rem;">
        <input type="hidden" name="tab" value="iuran">
        <div class="form-group">
            <label for="tanggal">Pilih Tanggal Pertemuan (Hari Rabu)</label>
            <input type="date" id="tanggal" name="tanggal" value="<?php echo $tanggal_pilihan; ?>" onchange="this.form.submit()">
        </div>
    </form>

    <form action="proses_iuran.php" method="POST">
        <input type="hidden" name="tanggal_pertemuan" value="<?php echo $tanggal_pilihan; ?>">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th>Status Pembayaran (Lunas)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($anggota) > 0): ?>
                        <?php foreach ($anggota as $member): ?>
                            <?php $status_sekarang = $status_iuran[$member['id']] ?? 'belum_lunas'; ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($member['nis']); ?></td>
                                <td><?php echo htmlspecialchars($member['kelas']); ?></td>
                                <td><input type="checkbox" name="status_bayar[<?php echo $member['id']; ?>]" value="lunas" <?php echo ($status_sekarang == 'lunas') ? 'checked' : ''; ?>></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">Belum ada anggota terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top: 1.5rem;">Simpan Data Kas</button>
    </form>
</div>

<div class="content-area" style="margin-top: 2rem;">
    <h2>Ringkasan Kas Pertemuan (<?php echo date("d F Y", strtotime($tanggal_pilihan)); ?>)</h2>
    <p>Jumlah Kas per Anggota: <strong>Rp <?php echo number_format($jumlah_iuran, 0, ',', '.'); ?></strong></p>
    <p>Jumlah Anggota Lunas: <strong><?php echo $jumlah_lunas; ?> dari <?php echo count($anggota); ?> anggota</strong></p>
    <p>Total Terkumpul: <strong style="color: green;">Rp <?php echo number_format($total_terkumpul, 0, ',', '.'); ?></strong></p>
</div>
