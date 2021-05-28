<?php

class currencylayer extends cls_dbtools
{
    public function currency_layer($originalCurrency, $date, $finalCurrency = 'USD')
    {
        $access_key = '3da0e9a1d2bb1bee46cb1dc1b2302aa7';

        $GET = http_build_query([
            'access_key' => $access_key,
            'date' => ($date ?: date('Y-m-d')),
            'source' => $finalCurrency,
            'currencies' => $originalCurrency,
        ]);
        if ($originalCurrency == $finalCurrency) {
            return 1;
        }
        if (strlen($originalCurrency) < 3) {
            return false;
        }
        $ch = curl_init('http://apilayer.net/api/historical?' . $GET);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);
        $exchangeRates = json_decode($json, true);
        return $exchangeRates["quotes"][$finalCurrency . $originalCurrency];
    }
 
    public function GetHistoricalCurrency($originalCurrency, $date, $finalCurrency = "USD")
    {
        $query = "SELECT 
                    tasa_cambio 
                FROM `currency_historical` 
                WHERE 
                    fecha = '{$date}' 
                    AND currency_codigo = '{$finalCurrency}{$originalCurrency}'";
        return $this->_SQL_tool($this->SELECT, __METHOD__, $query)[0]['tasa_cambio'];
    }
    public function exchangeRate($originalCurrency, $date, $finalCurrency = 'USD')
    {
        $date = $date ?: date('Y-m-d');
        $finalCurrency = $finalCurrency ?: 'USD';
        $tasa = (float)$this->GetHistoricalCurrency($originalCurrency, $date, $finalCurrency);
        if (empty($tasa)) {
            $tasa = $this->currency_layer($originalCurrency, $date, $finalCurrency);
            if (!empty($tasa) && $finalCurrency <> $originalCurrency) {
                    $query = "INSERT INTO currency_historical(fecha,currency_codigo,tasa_cambio) VALUES 
                    ('{$date}','{$finalCurrency}{$originalCurrency}','{$tasa}')";
                    $this->_SQL_tool($this->INSERT, _METHOD_, $query);
            }
        }
        return is_numeric($tasa) ? $tasa : false;
    }
}

