<?php

function getExchangeRate($from, $to) {
    $url = "https://www.google.com/search?q={$from}+to+{$to}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    
    $response = curl_exec($ch);
    curl_close($ch);

    $dom = new DOMDocument();
    @$dom->loadHTML($response);

    $xpath = new DOMXPath($dom);

    $node = $xpath->query('//div[@class="BNeawe iBp4i AP7Wnd"]');
    if ($node->length > 0) {
        $rateString = $node[0]->nodeValue;
        $rate = str_replace('.', '', $rateString);
        $rate = str_replace(',', '.', $rate);
        
        return floatval($rate);
    } else {
        return null;
    }
}

$rate = null;
$result = null;
$alertMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from = $_POST['from_currency'];
    $to = $_POST['to_currency'];
    $amount = floatval(str_replace(',', '.', $_POST['amount']));

    $rate = getExchangeRate($from, $to);

    if ($rate) {
        $convertedAmount = $amount * $rate;
        $formattedRate = number_format($rate, 2, ',', '.');
        $result = number_format($convertedAmount, 2, ',', '.');
        $alertMessage = "1 {$from} = {$formattedRate} {$to}<br>Converted amount: {$result} {$to}";
    } else {
        $alertMessage = "Error fetching exchange rate.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Converter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f6;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .alert {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
        }
        select, input[type="number"] {
            display: block;
            width: 100%;
            padding: 12px;
            margin: 5px 0 20px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            font-size: 16px;
        }
        input[type="number"]:focus, select:focus {
            border-color: #80bdff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, .5);
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
            width: 100%;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Currency Converter</h1>

    <?php if ($alertMessage): ?>
        <div class="alert">
            <?php echo $alertMessage; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label for="from_currency">From Currency:</label>
        <select name="from_currency" required>
            <option value="USD">US Dollar (USD)</option>
            <option value="EUR">Euro (EUR)</option>
            <option value="IDR">Indonesian Rupiah (IDR)</option>
            <option value="MYR">Malaysian Ringgit (MYR)</option>
            <option value="TRY">Turkish Lira (TRY)</option>
            <option value="ARS">Argentine Peso (ARS)</option>
            <option value="THB">Thai Baht (THB)</option>
            <option value="SGD">Singapore Dollar (SGD)</option>
            <option value="PHP">Philippine Peso (PHP)</option>
            <option value="VND">Vietnamese Dong (VND)</option>
            <option value="BND">Brunei Dollar (BND)</option>
            <option value="KHR">Cambodian Riel (KHR)</option>
            <option value="LAK">Lao Kip (LAK)</option>
            <option value="MMK">Myanmar Kyat (MMK)</option>
            <option value="MNT">Mongolian Tugrik (MNT)</option>
        </select>

        <label for="to_currency">To Currency:</label>
        <select name="to_currency" required>
            <option value="USD">US Dollar (USD)</option>
            <option value="EUR">Euro (EUR)</option>
            <option value="IDR">Indonesian Rupiah (IDR)</option>
            <option value="MYR">Malaysian Ringgit (MYR)</option>
            <option value="TRY">Turkish Lira (TRY)</option>
            <option value="ARS">Argentine Peso (ARS)</option>
            <option value="THB">Thai Baht (THB)</option>
            <option value="SGD">Singapore Dollar (SGD)</option>
            <option value="PHP">Philippine Peso (PHP)</option>
            <option value="VND">Vietnamese Dong (VND)</option>
            <option value="BND">Brunei Dollar (BND)</option>
            <option value="KHR">Cambodian Riel (KHR)</option>
            <option value="LAK">Lao Kip (LAK)</option>
            <option value="MMK">Myanmar Kyat (MMK)</option>
            <option value="MNT">Mongolian Tugrik (MNT)</option>
        </select>

        <label for="amount">Amount:</label>
        <input type="number" name="amount" step="0.01" min="0" placeholder="Enter amount" required>

        <button type="submit">Convert</button>
    </form>
</div>

</body>
</html>
