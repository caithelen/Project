<?php
$servername = "localhost";
$username = "root";
$password = "Ehw2019!";
$dbname = "tours";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}
?>
<?php
class Payment {
    // Database connection and table name
    private $conn;
    private $table = 'payments';

    // Object properties corresponding to table columns
    public $payment_id;
    public $booking_id;
    public $payment_date;
    public $amount;
    public $payment_method;
    public $payment_status;

    // Constructor: expects a PDO connection object
    public function __construct($db) {
        $this->conn = $db;
    }

?>


