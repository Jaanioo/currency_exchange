<?php

include 'Database.php';

class ConvertionsTableView
{
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function displayConvertionsTable(): void
    {
        try {
            $connection = $this->database->connectToDatabase();

            echo "<h2>Conversion History</h2>";

            echo "<table style='border-collapse: collapse'>";
            echo "<tr>
                    <th style='border: 1px solid black; padding: 5px;'>From Currency</th>
                    <th style='border: 1px solid black; padding: 5px;'>To Currency</th>
                    <th style='border: 1px solid black; padding: 5px;'>From Amount</th>
                    <th style='border: 1px solid black; padding: 5px;'>To Amount</th>
                    <th style='border: 1px solid black; padding: 5px;'>Conversion Rate</th>
                    <th style='border: 1px solid black; padding: 5px;'>Conversion Time</th>
                </tr>";

            $query = "SELECT from_code, to_code, from_value, to_value, conv_rate, conv_time 
                      FROM conversions 
                      ORDER BY conv_time DESC 
                      LIMIT 10";

            $result = mysqli_query($connection, $query);

            if (!$result) {
                throw new Exception(mysqli_error($connection));
            }

            while ($row = mysqli_fetch_assoc($result)) {
                $fromCurrency = $row['from_code'];
                $toCurrency = $row['to_code'];
                $fromAmount = $row['from_value'];
                $toAmount = $row['to_value'];
                $conversionRate = $row['conv_rate'];
                $conversionTime = $row['conv_time'];

                echo "<tr>
                        <td style='border: 1px solid black; padding: 5px;'>$fromCurrency</td>
                        <td style='border: 1px solid black; padding: 5px;'>$toCurrency</td>
                        <td style='border: 1px solid black; padding: 5px;'>$fromAmount</td>
                        <td style='border: 1px solid black; padding: 5px;'>$toAmount</td>
                        <td style='border: 1px solid black; padding: 5px;'>$conversionRate</td>
                        <td style='border: 1px solid black; padding: 5px;'>$conversionTime</td>
                    </tr>";
            }
            echo "</table>";
        } catch (Exception $e) {
            echo "An error occurred: " . $e->getMessage();
        }
    }
}
