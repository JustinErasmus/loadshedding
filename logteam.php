<?php

date_default_timezone_set('Africa/Johannesburg');

define('LOADSHEDDING_STAGE', 2);

$today=date('d');

$teamZones = [
    '5' => ['Nadeem'],
    '10' => ['Justin'],
    '11' => ['Kamil'],
    '12' => ['Tohir'],
    '15' => ['Mel', 'Elvis', 'Mncedi'],

];

require_once 'vendor/autoload.php';

$csv = new parseCSV('stage'.LOADSHEDDING_STAGE.'.csv');

$data = $csv->data;

// Create an Array of Date Columns
$datesColumn = [];
for ($i=2; $i<=17; $i++)
{
    $datesColumn[$i] = [$data[0][$i]];
    
    if (!empty($data[1][$i])) {
        $datesColumn[$i][] = $data[1][$i];
    }
}


// Find Load Shedding
$loadShedding = [];

for ($i=2; $i<=count($data)-1; $i++)
{
    $startTime = $data[$i][0];
    $endTime = $data[$i][1]; 
    
    
    for ($j=2; $j<=17; $j++)
    {
        // Ignore if not today
        if (!in_array($today, $datesColumn[$j])) {
            continue;
        }
        
        // Get Zones
        $zones = explode(',', $data[$i][$j]);
        
        foreach ($zones as $zone)
        {
            $zone = (int)$zone;
            
            // Check whether it affects someone in team
            if (isset($teamZones[$zone])) {
                $loadShedding[$j][] = [
                    'date'=>$datesColumn[$j],
                    'zone'=>$zone,
                    'starttime'=>$startTime,
                    'endtime'=>$endTime,
                    'affects'=>$teamZones[$zone]
                ];
            }
        }
    }
}

echo 'Loadshedding in LogTeam for '.date('l, j F Y').' - Stage '.LOADSHEDDING_STAGE.PHP_EOL.PHP_EOL;

foreach ($loadShedding as $result)
{
    foreach ($result as $time)
    {
        echo sprintf('Zone %s', str_pad($time['zone'],2));
        
        echo sprintf(' - %s till %s for: ', $time['starttime'], $time['endtime']);
        
        echo implode(', ', $time['affects']);
        
        echo PHP_EOL;
    }
}