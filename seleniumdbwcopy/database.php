<?php
$db_server = "localhost";
$db_user = "root";
$db_pass = "63101";
$db_name = "pricebite";
$db = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

function createTable($phoneNumber, $db) {
    if (!tableExists($phoneNumber, $db)) {
        $createsql = "CREATE TABLE `Swiggy_order_$phoneNumber` (
            Sno INT AUTO_INCREMENT PRIMARY KEY,
            Sid INT,
            Rest_name VARCHAR(255),
            Rest_loc VARCHAR(255),
            orderid DATETIME,
            item VARCHAR(255),
            quantity INT,
            FOREIGN KEY (Sid) REFERENCES User(Sid)
        )";
        $db->query($createsql);

        $sql = "CREATE TRIGGER before_insert_cart_$phoneNumber
            BEFORE INSERT ON Cart
            FOR EACH ROW
            BEGIN
                SET NEW.orderid = (SELECT orderid FROM `Swiggy_order_$phoneNumber` ORDER BY Sno DESC LIMIT 1);
            END;";
        $db->query($sql);
    }
}

function tableExists($phoneNumber, $db) {
    $sql = "SHOW TABLES LIKE 'Swiggy_order_$phoneNumber'";
    $result = $db->query($sql);
    return $result->num_rows > 0;
}
?>
