<?php
    session_start();
    include 'config/koneksi.php';

    if (!isset($_SESSION['login'])) {
        header("Location: login.php");
        exit;
    }

    if ($_SESSION['role'] !== 'admin') {
        echo "Akses ditolak";
        exit;
    }

    $query = "SELECT * FROM users ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Manajemen User</title>
        <style>
            body {
                font-family: Arial;
                background: #f4f6f9;
            }
            .container {
                width: 90%;
                margin: 30px auto;
            }
            h2 {
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                background: white;
                border-radius: 10px;
                overflow: hidden;
            }
            th, td {
                padding: 12px;
                border-bottom: 1px solid #ddd;
            }
            th {
                background: #b03a72;
                color: white;
            }
            .btn {
                padding: 6px 10px;
                border-radius: 6px;
                text-decoration: none;
                color: white;
                font-size: 12px;
            }
            .approve { background: green; }
            .nonaktif { background: orange; }
            .hapus { background: red; }
        </style>
    </head>
    <body>

    <div class="container">
        <h2>Manajemen User</h2>

        <table>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

            <?php $no = 1; ?>
            <?php while($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['nama']; ?></td>
                <td><?= $row['email']; ?></td>
                <td><?= $row['role']; ?></td>
                <td><?= $row['status']; ?></td>
                <td>
                    <?php if ($row['status'] == 'pending'): ?>
                        <a class="btn approve" href="approve_user.php?id=<?= $row['id']; ?>">Approve</a>
                    <?php endif; ?>

                    <?php if ($row['status'] == 'aktif'): ?>
                        <a class="btn nonaktif" href="nonaktif_user.php?id=<?= $row['id']; ?>">Nonaktif</a>
                    <?php endif; ?>

                    <a class="btn hapus" href="delete_user.php?id=<?= $row['id']; ?>">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    </body>
    </html>