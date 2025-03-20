<!-- pk_test_ed99e88c9f3e1caf961089161641b23813a8fc41 -->
<?php
// Configuration
$paystack_secret_key = 'pk_test_ed99e88c9f3e1caf961089161641b23813a8fc41'; // Replace with your actual secret key

// Check if a transaction ID is provided
if (isset($_GET['transaction_id'])) {
    $transaction_id = $_GET['transaction_id'];

    // Initialize cURL
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.paystack.co/transaction/{$transaction_id}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer {$paystack_secret_key}",
            "Cache-Control: no-cache",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        // Pretty print JSON for better readability
        $formatted_response = json_encode(json_decode($response), JSON_PRETTY_PRINT);
        echo "<pre>";
        echo htmlspecialchars($formatted_response);
        echo "</pre>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Paystack Transaction Lookup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: 300px;
            padding: 10px;
        }
        pre {
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>Paystack Transaction Lookup</h1>
    <form method="GET">
        <label for="transaction_id">Enter Transaction ID:</label>
        <input type="text" id="transaction_id" name="transaction_id" required 
               placeholder="Enter Paystack Transaction ID" 
               value="<?php echo isset($_GET['transaction_id']) ? htmlspecialchars($_GET['transaction_id']) : ''; ?>">
        <button type="submit">Fetch Transaction Details</button>
    </form>

    <?php 
    // If a transaction ID was submitted, show the raw transaction details
    if (isset($_GET['transaction_id'])) {
        echo "<h2>Transaction Details</h2>";
    }
    ?>
</body>
</html>