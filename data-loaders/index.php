<?php

/**
 * TicketEvolution Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://code.ticketevolution.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@ticketevolution.com so we can send you a copy immediately.
 *
 * @category    TicketEvolution
 * @package     TicketEvolution
 * @author      J Cobb <j@teamonetickets.com>
 * @copyright   Copyright (c) 2012 Ticket Evolution INC (http://www.ticketevolution.com)
 * @license     http://code.ticketevolution.com/license/new-bsd     New BSD License
 */


require_once 'bootstrap.php';
?>

<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>Ticket Evolution DataLoaders</title>
	<meta name="description" content="Ticket Evolution DataLoaders">
	<meta name="author" content="J Cobb <j+ticketevolution@teamonetickets.com>">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style type="text/css">
        table {
            border-width: 1px;
            border-spacing: 1px;
            border-style: none;
            border-color: gray;
            border-collapse: collapse;
            background-color: white;
        }
        table th {
            border-width: 2px;
            padding: 8px;
            border-style: inset;
            border-color: gray;
            background-color: #758f95;
        }
        table td {
            border-width: 2px;
            padding: 4px;
            border-style: inset;
            border-color: gray;
            background-color: #dcf3f8;
        }
        .time {
        	font-variant: small-caps;
        }
    </style>

</head>
<body>
	<div id="container">
		<header>

		</header>

		<div id="main" role="main">
		    <h1>Ticket Evolution DataLoaders</h1>
		    <p>These “DataLoader” scripts can be used to populate local database tables with a cache of the Ticket Evolution data. If you choose to do this then you should be sure to run each of these scripts at least daily. We suggest adding them to your <code>crontab</code> on your server.</p>

            <?php
                $table = new TicketEvolution_Db_Table_DataLoaderStatus();

                $scripts = array(
                    'brokers'        => array(
                        'active',
                    ),
                    'categories'        => array(
                        'active',
                        'deleted',
                    ),
                    'configurations'        => array(
                        'active',
                    ),
                    'events'        => array(
                        'active',
                        'deleted',
                    ),
                    'offices'        => array(
                        'active',
                    ),
                    'performers'        => array(
                        'active',
                        'deleted',
                    ),
                    'users'        => array(
                        'active',
                    ),
                    'venues'        => array(
                        'active',
                        'deleted',
                    ),
                );
                echo '<h2>Status of scripts run based upon <i>updated_at</i> date</h1>' . PHP_EOL
                   . '<table summary="Status of scripts run based upon updated_at date" style="border: 1 px solid grey;">' . PHP_EOL
                   . '<tr>' . PHP_EOL
                   . '<th>Script</th>' . PHP_EOL
                   . '<th>Type</th>' . PHP_EOL
                   . '<th>Last Run</th>' . PHP_EOL
                   . '<th>Run Now</th>' . PHP_EOL
                   . '</tr>' . PHP_EOL
                ;
                foreach ($scripts as $script => $types) {
                    // See if we have an entry in `tevoDataLoaderStatus` for this script

                    echo '<tr>' . PHP_EOL
                       . '<td rowspan="' . count($types) . '">' . ucwords($script) . '</td>' . PHP_EOL
                    ;
                    foreach ($types as $type) {
                        $row = $table->find($script, $type)->current();

                        $file = strtolower($script);
                        if ($type != 'active') {
                            $file .= '-' . $type;
                        }
                        $file .= '.php';

                        echo '<td>' . ucwords($type) . '</td>' . PHP_EOL
                           . '<td>'
                        ;
                        if (!empty($row)) {
                            $dateLastRun = new DateTime($row->lastRun);
                            echo '<span class="date">' . $dateLastRun->format(TicketEvolution_DateTime::DATE_FULL_US) . '</span> <span class="time">' . $dateLastRun->format('g:i:s a') . '</span>';
                        } else {
                            echo 'Not yet run';
                        }
                        echo '</td>' . PHP_EOL
                           . '<td><a href="' . $file . '">Run Now</a></td>' . PHP_EOL
                           . '</tr>' . PHP_EOL
                        ;

                        unset($row);
                    }
                }
                echo '</table>' . PHP_EOL;
            ?>
		</div>

		<footer>

		</footer>
	</div>
</body>
</html>
