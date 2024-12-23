<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit();
}

if (isset($_GET['logout'])) {
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach ($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time() - 3600, '/');
        }
    }
    session_destroy();
    header("Location: auth.php");
    exit();
}

class TicketBooking
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function addBooking($name, $email, $category, $price)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO form (name, email, category, price) VALUES (:name, :email, :category, :price)"
            );
            $stmt->execute([
                ':name' => htmlspecialchars($name),
                ':email' => htmlspecialchars($email),
                ':category' => htmlspecialchars($category),
                ':price' => htmlspecialchars($price),
            ]);
        } catch (PDOException $e) {
            throw new Exception("Failed to save data: " . $e->getMessage());
        }
    }

    public function getBookings()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM form ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Failed to retrieve data: " . $e->getMessage());
        }
    }

    public function deleteBooking($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM form WHERE id = :id");
            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            throw new Exception("Failed to delete data: " . $e->getMessage());
        }
    }
}

try {
    $db = new PDO('mysql:host=localhost;dbname=UAS', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$ticketBooking = new TicketBooking($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'] ?? null;
    if ($id !== null) {
        $ticketBooking->deleteBooking($id);
    }
}

$bookings = $ticketBooking->getBookings();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Data</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Booking Data</h1>
        <nav>
            <ul>
                <li><a href="form.php">Form</a></li>
                <li><a href="table.php">Tabel</a></li>
                <li><a href="?logout=true">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bookings)) : ?>
                    <?php foreach ($bookings as $row) : ?>
                        <tr>
                        <td><?= isset($row['name']) ? htmlspecialchars($row['name']) : 'N/A' ?></td>
                        <td><?= isset($row['email']) ? htmlspecialchars($row['email']) : 'N/A' ?></td>
                        <td><?= isset($row['category']) ? htmlspecialchars($row['category']) : 'N/A' ?></td>
                        <td><?= isset($row['price']) ? htmlspecialchars($row['price']) : 'N/A' ?></td>

                            <td>
                                <form action="table.php" method="POST">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                    <input type="hidden" name="delete" value="1">
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this booking?');">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5">No bookings found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
