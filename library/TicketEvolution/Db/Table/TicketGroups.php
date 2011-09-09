<?php
/**
 * TicketEvolution Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@teamonetickets.com so we can send you a copy immediately.
 *
 * @category    TicketEvolution
 * @package     TicketEvolution_Db
 * @subpackage  Table
 * @author      J Cobb <j@teamonetickets.com>
 * @author      Jeff Churchill <jeff@teamonetickets.com>
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */

/**
 * @see TicketEvolution_Db_Table_Abstract
 */
require_once 'TicketEvolution/Db/Table/Abstract.php';

/**
 * @category    TicketEvolution
 * @package     TicketEvolution_Db
 * @subpackage  Table
 * @copyright   Copyright (c) 2011 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/ticketevolution/ticketevolution-php/blob/master/LICENSE.txt     New BSD License
 */
class TicketEvolution_Db_Table_TicketGroups
    extends TicketEvolution_Db_Table_Abstract
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $_name   = 'tevoTicketGroups';

    /**
     * The primary key column or columns.
     * A compound key should be declared as an array.
     * You may declare a single-column primary key
     * as a string.
     *
     * @var mixed
     */
    protected $_primary   = 'ticketGroupId';
    
    /**
     * The column that we use to indicate status in boolean form
     *
     * @var string
     */
    protected $_statusColumn   = 'ticketGroupStatus';
    
    /**
     * Simple array of class names of tables that are "children" of the current
     * table, in other words tables that contain a foreign key to this one.
     * Array elements are not table names; they are class names of classes that
     * extend Zend_Db_Table_Abstract.
     *
     * @var array
     */
    protected $_dependentTables = array(
        'TicketEvolution_Db_Table_TicketGroupSeats',
    );
    
    
    /**
     * Associative array map of declarative referential integrity rules.
     * This array has one entry per foreign key in the current table.
     * Each key is a mnemonic name for one reference rule.
     *
     * Each value is also an associative array, with the following keys:
     * - columns       = array of names of column(s) in the child table.
     * - refTableClass = class name of the parent table.
     * - refColumns    = array of names of column(s) in the parent table,
     *                   in the same order as those in the 'columns' entry.
     * - onDelete      = "cascade" means that a delete in the parent table also
     *                   causes a delete of referencing rows in the child table.
     * - onUpdate      = "cascade" means that an update of primary key values in
     *                   the parent table also causes an update of referencing
     *                   rows in the child table.
     *
     * @var array
     */
    protected $_referenceMap    = array(
        'Events'            => array(
            'columns'           => 'eventId',
            'refTableClass'     => 'TicketEvolution_Db_Table_Events',
            'refColumns'        => 'eventId'
            ),
        'Brokers'            => array(
            'columns'           => 'brokerId',
            'refTableClass'     => 'TicketEvolution_Db_Table_Brokers',
            'refColumns'        => 'brokerId'
            ),
        'Offices'            => array(
            'columns'           => 'officeId',
            'refTableClass'     => 'TicketEvolution_Db_Table_Offices',
            'refColumns'        => 'officeId'
            ),
    );
}