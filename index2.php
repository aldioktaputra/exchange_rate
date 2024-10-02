<?php
function getExchangeRate($fromCurrency, $toCurrency) {
    $url = "https://www.google.com/finance/quote/{$fromCurrency}-{$toCurrency}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

    $response = curl_exec($ch);
    curl_close($ch);

    $dom = new DOMDocument();
    @$dom->loadHTML($response);

    $xpath = new DOMXPath($dom);

    // Update XPath to fetch the exchange rate from Google Finance
    $node = $xpath->query('//div[@class="YMlKec fxKbKc"]'); // Adjusting to the class used on Google Finance
    if ($node->length > 0) {
        $rateString = $node[0]->nodeValue;
        $rate = str_replace('.', '', $rateString);
        $rate = str_replace(',', '.', $rate);

        return floatval($rate);
    } else {
        return null;
    }
}

$result = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fromCurrency = $_POST['from_currency'];
    $toCurrency = $_POST['to_currency'];
    $amount = $_POST['amount'];

    $rate = getExchangeRate($fromCurrency, $toCurrency);
    if ($rate) {
        $convertedAmount = $amount * $rate;
        $result = "Live Rate: 1 $fromCurrency = " . number_format($rate, 2, ',', '.') . " $toCurrency<br>" .
                  "Converted Amount: " . number_format($convertedAmount, 2, ',', '.') . " $toCurrency";
    } else {
        $result = "Gagal mendapatkan nilai tukar dari Google.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Converter</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            max-width: 600px;
        }
        .alert {
            margin-top: 20px;
        }
        .form-control, .btn {
            border-radius: 30px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .form-group label {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="text-center">Currency Converter</h1>

    <?php if ($result): ?>
        <div class="alert alert-info" role="alert">
            <?php echo $result; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="mt-3">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="from_currency">From</label>
                <select name="from_currency" id="from_currency" class="form-control" required>
                    <option value="USD">USD</option>
                    <option value="IDR">IDR</option>
                    <option value="EUR">EUR</option>
                    <option value="MYR">MYR</option>
                    <option value="TRY">TRY</option>
                    <option value="ARS">ARS</option>
                    <option value="THB">THB</option>
                    <option value="SGD">SGD</option>
                    <!-- Add more currencies as needed -->
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="to_currency">To</label>
                <select name="to_currency" id="to_currency" class="form-control" required>
                    <option value="IDR">IDR</option>
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                    <option value="MYR">MYR</option>
                    <option value="TRY">TRY</option>
                    <option value="ARS">ARS</option>
                    <option value="THB">THB</option>
                    <option value="SGD">SGD</option>
                    <!-- Add more currencies as needed -->
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="text" name="amount" id="amount" class="form-control" placeholder="Enter amount" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Convert</button>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
