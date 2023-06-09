<?php

class CurrencyTableView
{
    private $rates;
    private $database;

    public function __construct($rates, Database $database)
    {
        $this->rates = $rates;
        $this->database = $database;
    }

    public function displayRatesTable(): void
    {
        $connection = $this->database->connectToDatabase();

        echo "<h2>Current exchange rates</h2>";

        echo "<table style='border-collapse: collapse'>";
        echo "<tr>
                <th style='border: 1px solid black; padding: 5px;'>Currency</th>
                <th style='border: 1px solid black; padding: 5px;'>Code</th>
                <th style='border: 1px solid black; padding: 5px;'>Rate</th>
            </tr>";

        $removeCurrencyCode = 'PLN';

        foreach ($this->rates as $rate) {
            $currency = $rate['currency'];
            $code = $rate['code'];
            $currencyRate = $rate['mid'];

            if ($code === $removeCurrencyCode) {
                continue;
            }

            $existingRate = $this->getRateFromDatabase($connection, $currency, $code);

            if (!$existingRate) {
                $this->insertRateIntoDatabase($connection, $currency, $code, $currencyRate);
            } elseif ($existingRate['current_rate'] !== $currencyRate) {
                $this->updateRateInDatabase($connection, $currency, $code, $currencyRate);
            }

            echo "<tr>
                    <td style='border: 1px solid black; padding: 5px;'>$currency</td>
                    <td style='border: 1px solid black; padding: 5px;'>$code</td>
                    <td style='border: 1px solid black; padding: 5px;'>$currencyRate</td>
                </tr>";
        }

        echo "</table>";
    }

    private function getRateFromDatabase($connection, $currency, $code)
    {
        $query = "SELECT currency, code, current_rate FROM current_rates 
                  WHERE currency = '$currency' AND code = '$code'";

        $result = mysqli_query($connection, $query);

        if (!$result) {
            throw new Exception('Query failed: ' . mysqli_error($connection));
        }

        return mysqli_fetch_assoc($result);
    }

    private function insertRateIntoDatabase($connection, $currency, $code, $currencyRate)
    {
        $insertQuery = "INSERT INTO current_rates (currency, code, current_rate) 
                        VALUES ('$currency', '$code', '$currencyRate')";

        $insertResult = mysqli_query($connection, $insertQuery);

        if (!$insertResult) {
            throw new Exception('Insert query failed: ' . mysqli_error($connection));
        }
    }

    private function updateRateInDatabase($connection, $currency, $code, $currencyRate)
    {
        $updateQuery = "UPDATE current_rates SET current_rate = '$currencyRate'
                        WHERE currency = '$currency' AND code = '$code'";

        $updateResult = mysqli_query($connection, $updateQuery);

        if (!$updateResult) {
            throw new Exception('Update query failed: ' . mysqli_error($connection));
        }
    }
}