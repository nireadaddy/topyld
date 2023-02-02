<?php

// URL to retrieve stock data from the SET
$set_url = 'https://marketdata.set.or.th/mkt/marketnewsv3.do?language=th&country=TH&type=report&symbol=';

// List of stock symbols to check
$symbols = [
    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
    'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
    'U', 'V', 'W', 'X', 'Y', 'Z'
];

// Results array to store stock information
$results = [];

// Loop through each symbol
foreach ($symbols as $symbol) {

    // URL for the current symbol
    $url = $set_url . $symbol;

    // Retrieve the HTML content of the page
    $html = file_get_contents($url);

    // Load the HTML content into a DOM object
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    // Get the table with the stock information
    $table = $dom->getElementById('tablepress-6');

    // Get the rows of the table
    $rows = $table->getElementsByTagName('tr');

    // Loop through each row
    foreach ($rows as $row) {

        // Get the cells of the row
        $cells = $row->getElementsByTagName('td');

        // Check if the row contains stock information
        if ($cells->length > 0) {

            // Extract the stock information
            $symbol = $cells->item(0)->nodeValue;
            $name = $cells->item(1)->nodeValue;
            $last_price = $cells->item(2)->nodeValue;
            $dividend_yield = $cells->item(3)->nodeValue;

            // Check if the dividend yield is high
            if ($dividend_yield >= 5) {

                // Add the stock information to the results array
                $results[] = [
                    'Symbol' => $symbol,
                    'Name' => $name,
                    'Last Price' => $last_price,
                    'Dividend Yield' => $dividend_yield
                ];
            }
        }
    }
}

// Create the header row for the .csv file
$header = ['Symbol', 'Name', 'Last Price', 'Dividend Yield'];

// Initialize the .csv file
$csv = fopen('stocks.csv', 'w');

// Write the header row to the .csv file
fputcsv($csv, $header);

// Loop through each result and write it to the .csv file
foreach ($results as $result) {
    fputcsv($csv, $result);
}

// Close the .csv file
fclose($csv);
