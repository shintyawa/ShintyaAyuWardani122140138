<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: auth.php"); // Redirect ke halaman login jika belum login
    exit();
}

if (isset($_GET['logout'])) {
    // Hapus semua cookie
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach ($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time() - 3600, '/'); // Hapus cookie dengan mengatur waktu kadaluwarsa ke masa lalu
        }
    }

    session_destroy(); // Hancurkan session
    header("Location: auth.php"); // Arahkan ke halaman login
    exit();
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "UAS";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemesanan Tiket Sepak Bola</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
            display: none;
        }
        .error-border {
            border: 1px solid red;
        }
    </style>
</head>
<body>
    <header>
        <h1>Form Pemesanan Tiket Sepak Bola</h1>
        <nav>
            <ul>
                <li><a href="form.php">Form Pemesanan</a></li>
                <li><a href="table.php">Tabel Pemesanan</a></li>
                <li><a href="?logout=true">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <form id="ticketForm" action="form.php" method="POST">
            <label for="name">Nama:</label>
            <input type="text" id="name" name="name">
            <div class="error-message" id="error-name">Kolom ini tidak boleh kosong</div>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
            <div class="error-message" id="error-email">Kolom ini tidak boleh kosong</div>

            <label for="category">Kategori Tiket:</label>
            <select id="category" name="category">
                <option value="">-- Pilih Kategori --</option>
                <option value="VIP">VIP</option>
                <option value="Reguler">Reguler</option>
                <option value="Ekonomi">Ekonomi</option>
            </select>
            <div class="error-message" id="error-category">Kolom ini tidak boleh kosong</div>

            <label for="price">Harga:</label>
            <input type="text" id="price" name="price" readonly>
            <div class="error-message" id="error-price">Kolom ini tidak boleh kosong</div>

            <button type="submit" name="submit">Pesan Tiket</button>
        </form>

        <?php
        if (isset($_POST['submit'])) {
            // Mengambil data form
            $name = $_POST['name'];
            $email = $_POST['email'];
            $category = $_POST['category'];
            $price = "";

            // Menentukan harga berdasarkan kategori
            switch ($category) {
                case 'VIP':
                    $price = "100000";
                    break;
                case 'Reguler':
                    $price = "50000";
                    break;
                case 'Ekonomi':
                    $price = "25000";
                    break;
            }

            // Menyimpan data ke database
            if ($name && $email && $category && $price) {
                $sql = "INSERT INTO form (nama, email, kategori, harga) VALUES ('$name', '$email', '$category', '$price')";
                if ($conn->query($sql) === TRUE) {
                    echo "<p>Data berhasil disimpan!</p>";
                } else {
                    echo "<p>Terjadi kesalahan: " . $conn->error . "</p>";
                }
            }
        }
        ?>

    </main>

    <script>
        document.getElementById('ticketForm').addEventListener('submit', function (e) {
            let isValid = true;

            // Validasi nama
            const name = document.getElementById('name');
            const errorName = document.getElementById('error-name');
            if (name.value.trim() === '') {
                errorName.style.display = 'block';
                name.classList.add('error-border');
                isValid = false;
            } else {
                errorName.style.display = 'none';
                name.classList.remove('error-border');
            }

            // Validasi email
            const email = document.getElementById('email');
            const errorEmail = document.getElementById('error-email');
            if (email.value.trim() === '') {
                errorEmail.style.display = 'block';
                email.classList.add('error-border');
                isValid = false;
            } else {
                errorEmail.style.display = 'none';
                email.classList.remove('error-border');
            }

            // Validasi kategori
            const category = document.getElementById('category');
            const errorCategory = document.getElementById('error-category');
            if (category.value === '') {
                errorCategory.style.display = 'block';
                category.classList.add('error-border');
                isValid = false;
            } else {
                errorCategory.style.display = 'none';
                category.classList.remove('error-border');
            }

            // Validasi harga
            const price = document.getElementById('price');
            const errorPrice = document.getElementById('error-price');
            if (price.value.trim() === '') {
                errorPrice.style.display = 'block';
                price.classList.add('error-border');
                isValid = false;
            } else {
                errorPrice.style.display = 'none';
                price.classList.remove('error-border');
            }

            // Hentikan submit jika ada error
            if (!isValid) {
                e.preventDefault();
            }
        });

        // Mengupdate harga ketika kategori dipilih
        document.getElementById('category').addEventListener('change', function () {
            const category = this.value;
            const priceField = document.getElementById('price');
            switch (category) {
                case 'VIP':
                    priceField.value = '100000';
                    break;
                case 'Reguler':
                    priceField.value = '50000';
                    break;
                case 'Ekonomi':
                    priceField.value = '25000';
                    break;
                default:
                    priceField.value = '';
            }
        });
    </script>
</body>
</html>
