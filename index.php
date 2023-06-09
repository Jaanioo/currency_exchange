<?php
require_once 'Controller/CurrencyConverter.php';
require_once 'View/CurrencyTableView.php';
require_once 'View/CurrencyConverterView.php';
require_once 'View/ConvertionsTableView.php';
require_once 'Database.php';

// Create an instance of the Database class
$database = new Database();

// Create an instance of the ConvertionsTableView class
$convertionsTable = new ConvertionsTableView($database);

// Create an instance of the CurrencyConverter class
$converter = new CurrencyConverter(
    'https://api.nbp.pl/api/exchangerates/tables/A?format=json', $database);

// Fetch exchange rates from the API
$rates = $converter->fetchExchangeRates();

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Exchange</title>
    <style>
        .container {
            display: flex;
        }

        .container > div {
            flex: 1;
            margin-right: 20px;
        }
    </style>
</head>
<body>

<?php
$title = "Converter of currencies";
echo "<h1>$title</h1>";

try {
    if (count($rates) > 0) {
        echo "<div class='container'>";
        echo "<div>";
        $currencyTable = new CurrencyTableView($rates, $database);
        $currencyTable->displayRatesTable();
        echo "</div>";

        echo "<div>";
        $currencyForm = new CurrencyConverterView($rates);
        $currencyForm->displayConverter();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $amount = $_POST["amount"];
            $fromCurrency = $_POST["fromRate"];
            $toCurrency = $_POST["toRate"];

            $convertedAmount = $converter->convertCurrency($amount, $fromCurrency, $toCurrency);
            $convertedRate = number_format($convertedAmount / $amount, 2);

            if ($convertedAmount !== null) {
                echo "<br>";
                $formatedNumber = number_format($convertedAmount, 2);
                $converter->addConvertionToDB($fromCurrency, $toCurrency, $amount, $formatedNumber, $convertedRate);
                echo "<strong>Converted amount:</strong> $formatedNumber $toCurrency";
            } else {
                throw new Exception("Unable to convert currency.");
            }
        }

        $convertionsTable->displayConvertionsTable();
        echo "</div>";
        echo "</div>";
    } else {
        throw new Exception("Unable to fetch exchange rates.");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

</body>
</html>