<?php

class CurrencyConverter
{
    private $apiUrl;
    private $database;

    public function __construct($apiUrl, Database $database) {
        $this->apiUrl = $apiUrl;
        $this->database = $database;
    }

    public function fetchExchangeRates() {
        try {
            $jsonData = file_get_contents($this->apiUrl);
            $data = json_decode($jsonData, true);

            if ($data && is_array($data) && count($data) > 0 && isset($data[0]['rates'])) {
                $rates = $data[0]['rates'];

                $rates[] = [
                    'currency' => 'Polski złoty',
                    'code' => 'PLN',
                    'mid' => 1.0,
                ];
                return $rates;
            }
        } catch (Exception $e) {
            throw new Exception('Failed to fetch exchange rates: ' . $e->getMessage());
        }

        return [];
    }

    public function convertCurrency($amount, $fromCurrency, $toCurrency)
    {
        try {
            $connection = $this->database->connectToDatabase();

            $query = "SELECT code, current_rate FROM current_rates
                      WHERE code='$fromCurrency' OR code='$toCurrency'";
            $result = mysqli_query($connection, $query);

            if (!$result) {
                throw new Exception('Query failed: ' . mysqli_error($connection));
            }

            $rates = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $code = $row['code'];
                $currentRate = $row['current_rate'];
                $rates[$code] = $currentRate;
            }

            $fromRate = $rates[$fromCurrency] ?? null;
            $toRate = $rates[$toCurrency] ?? null;

            if ($fromRate !== null && $toRate !== null) {
                $firstConver = ($fromRate * $amount);
                return ($firstConver / $toRate);
            }
        } catch (Exception $e) {
            throw new Exception('Currency conversion failed: ' . $e->getMessage());
        }

        return null;
    }

    public function addConvertionToDB(
        $fromCurrency,
        $toCurrency,
        $fromAmount,
        $toAmount,
        $conversionRate)
    {
        try {
            $connection = $this->database->connectToDatabase();

            $currentTime = date('Y-m-d H:i:s');

            $query = "INSERT INTO conversions (from_code, to_code, from_value, 
                     to_value, conv_rate, conv_time) ";
            $query .= "VALUES ('$fromCurrency', '$toCurrency', '$fromAmount', '$toAmount', 
                        $conversionRate, '$currentTime')";

            $result = mysqli_query($connection, $query);

            if (!$result) {
                throw new Exception('Query failed: ' . mysqli_error($connection));
            }
        } catch (Exception $e) {
            throw new Exception('Failed to add conversion to database: ' . $e->getMessage());
        }
    }
}
