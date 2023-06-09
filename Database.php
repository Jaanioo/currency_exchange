<?php

class Database
{
    public function connectToDatabase()
    {
        $connection = mysqli_connect(
            'mysql8-container',
            'root',
            'secret',
            'exchange_currency');

        if ($connection) {
            return $connection;
        }

        throw new Exception("Database connection failed.");
    }
}