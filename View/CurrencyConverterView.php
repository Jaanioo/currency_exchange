<?php

class CurrencyConverterView
{
    private $rates;

    public function __construct($rates)
    {
        $this->rates = $rates;
    }

    public function displayConverter()
    {
        try {
            echo "<h2>Exchange your currency: </h2>";

            echo "<form method='post'>";
            echo "<label for='amount'>Amount:</label>";
            echo "<input type='text' name='amount' id='amount' required pattern='[0-9]+(?:\.[0-9]+)?' title='Please enter a valid number'>";

            echo "<label for='fromRate'>From Rate:</label>";
            echo "<select name='fromRate'id='fromRate' required>";

            $priorityCurrencies = ['PLN', 'USD', 'EUR', 'GBP', 'CHF'];

            foreach ($priorityCurrencies as $code) {
                echo "<option value='$code'>$code</option>";
            }

            $currencyCodes = array_column($this->rates, 'code');

            foreach ($currencyCodes as $code) {
                if (!in_array($code, $priorityCurrencies)) {
                    echo "<option value='$code'>$code</option>";
                }
            }

            echo "</select>";

            echo "<label for='toRate'>To Rate:</label>";
            echo "<select name='toRate' id='toRate' required>";

            foreach ($priorityCurrencies as $code) {
                echo "<option value='$code'>$code</option>";
            }

            foreach ($currencyCodes as $code) {
                if (!in_array($code, $priorityCurrencies)) {
                    echo "<option value='$code'>$code</option>";
                }
            }

            echo "</select>";

            echo "<input type='submit' value='Convert'>";
            echo "</form>";
        } catch (Exception $e) {
            echo "An error occurred: " . $e->getMessage();
        }
    }
}
