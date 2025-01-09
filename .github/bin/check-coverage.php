<?php

$file = dirname(__DIR__, 2) . '/build/clover.xml';
$xml = new SimpleXMLElement(file_get_contents($file));
$metrics = $xml->xpath('//metrics');

$totalStatements = 0;
$coveredStatements = 0;

foreach ($metrics as $metric) {
    $totalStatements += (int)$metric['statements'];
    $coveredStatements += (int)$metric['coveredstatements'];
}

$coverage = ($totalStatements > 0) ? ($coveredStatements / $totalStatements) * 100 : 0;

if ($coverage < 100) {
    echo "Code coverage is " . number_format($coverage, 2) . "%, which is below the required 100%.\n";
    exit(1);
}

echo "Code coverage is 100%.\n";
exit(0);
