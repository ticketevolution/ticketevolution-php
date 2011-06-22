<?php

/**
 * Ticketevolution Framework
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
 * @category    Ticketevolution
 * @package     Ticketevolution
 * @author      J Cobb <j@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Ticket Evolution INC (http://www.ticketevolution.com)
 * @license     http://code.ticketevolution.com/license/new-bsd     New BSD License
 * @version     $Id$
 */


require_once 'application.php';
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
                $table = new Ticketevolution_Db_Table_Dataloaderstatus();
                
                $scripts = array(
                    'brokers',
                    'categories',
                    'configurations',
                    'events',
                    'offices',
                    'performers',
                    'users',
                    'venues'
                );
                echo '<h2>Status of scripts run based upon <i>updated_at</i> date</h1>' . PHP_EOL
                   . '<table summary="Status of scripts run based upon updated_at date" style="border: 1 px solid grey;">' . PHP_EOL
                   . '<tr>' . PHP_EOL
                   . '<th>Script</th>' . PHP_EOL
                   . '<th>Last Run</th>' . PHP_EOL
                   . '<th>Run Now</th>' . PHP_EOL
                   . '</tr>' . PHP_EOL
                ;
                foreach($scripts as $script) {
                    // See if we have an entry in `tevoDataLoaderStatus` for this script
                    $row = $table->find($script);
                    
                    echo '<tr>' . PHP_EOL
                       . '<td>' . ucwords($script) . '</td>' . PHP_EOL
                       . '<td>'
                    ;
                    if(isset($row[0])) {
                        $dateLastRun = new Zend_Date($row[0]->lastRun, Zend_Date::ISO_8601);
                        echo $dateLastRun->get(Zend_Date::DATE_FULL . ' ' . Zend_Date::TIMES);
                    } else {
                        echo 'Not yet run';
                    }
                    echo '</td>' . PHP_EOL
                       . '<td><a href="' . $script . '.php">Run Now</a></td>' . PHP_EOL
                       . '</tr>' . PHP_EOL
                    ;
                }
                echo '</table>' . PHP_EOL;
            ?>
		</div>

		<footer>

		</footer>
	</div>
</body>
</html>