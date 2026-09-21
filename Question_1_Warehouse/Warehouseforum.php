<?php
require_once "dbconnWarehouse.php";

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
   
    if (ob_get_level()) {
        ob_end_clean();
    }

    $stmt = $pdo->query("SELECT item, quantity, supplier, category FROM warehouse_items");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="warehouse_inventory_' . date('Y-m-d') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    
    $output = fopen('php://output', 'w');

    
    fputcsv($output, ['Item', 'Quantity', 'Supplier', 'Category']);

    
    foreach ($items as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

if (isset($_POST['btnadd'])) {
    $item = $_POST['items'];
    $quantity = $_POST['quantity'];
    $supplier = $_POST['suppliers'];
    $category = $_POST['category']; 

  $stmt = $pdo->prepare("INSERT INTO warehouse_items (item, quantity, supplier, category) VALUES (?, ?, ?, ?)");
    $stmt->execute([$item, $quantity, $supplier, $category]);

    echo "<p style='color:green;'>Item Has Been added successfully!</p>";

   
}

if (isset($_POST['btnsearch'])) {
    $supplier = $_POST['suppliers'];

    $stmt = $pdo->prepare("SELECT * FROM warehouse_items WHERE supplier LIKE ?");
    $stmt->execute(["%$supplier%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows) > 0) {
        echo "<h3>Search Results:</h3>";
        echo "<table border='1'>
                <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Supplier</th>
                <th>Category</th>
                </tr>";
       foreach ($rows as $row) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['item']) . "</td>
                    <td>" . htmlspecialchars($row['quantity']) . "</td>
                    <td>" . htmlspecialchars($row['supplier']) . "</td>
                    <td>" . htmlspecialchars($row['category']) . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
     echo "<p>Suppliers is not found: " . htmlspecialchars($supplier) . "</p>";
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<H2>Warehouse Form</H2>

<form method="post">

    <Table>
        <tr>
            <td><label for="items">Items:</label></td>
            <td><input type="text" name="items" id="items" required></td>
        </tr>
        <tr>
            <td><label for="quantity">Quantity:</label></td>
            <td><input type="int" name="quantity" id="quantity" required></td>
        </tr>
        <tr>
            <td><label for="suppliers">Suppliers:</label></td>
            <td><input type="text" name="suppliers" id="supppliers" required></td>
        </tr>
        <tr>
            <td><label for="category">Category:</label></td>
            <td>
            <select name="category" id="category">
            <option value="stationary">Stationary</option>
            <option value="hardware">Hardware</option>
            <option value="electronics">Electronics</option>
            <option value="software">Software</option>
            </select>
            </td>
           
        </tr>
        <tr>
            <td><button type="submit" name="btnadd" >Add</button></td>
        </tr>
    </Table>

</form>

<h3>Search for Supplier</h3>

<form method="post">

<tr>
    <td><label for="suppliers">Supplier</label></td>
    <td><input type="text" name="suppliers" id="suppliers" required></td>
</tr>
<tr>
    <td><button type="search" name="btnsearch">Search</button></td>
</tr>

</form>

<a href="?export=csv">Export to CSV</a>
    
</body>                                             
</html>
