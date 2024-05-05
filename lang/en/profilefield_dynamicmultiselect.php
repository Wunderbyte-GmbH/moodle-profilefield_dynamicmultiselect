<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'profilefield_dynamicmultiselect', language 'en'
 *
 * @package   profilefield_dynamicmultiselect
 * @copyright 2016 onwards Antonello Moro  {@link http://antonellomoro.it}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Dynamic Multi Select';

$string['queryerrorfalse'] = "Error executing the query: return value is false";
$string['queryerrorempty'] = "No results found executing the query: cannnot validate";
$string['queryerroridmissing'] = 'id column is missing in query return values';
$string['queryerrordatamissing'] = 'data column is missing in query return values';
$string['queryerrordefaultmissing'] = 'Default value does not exists among the list of allowed values';
$string['sqlquery'] = 'Sql query';
$string['numbersqlvalues'] = 'Number of possible values';
$string['numbersqlvalueshelp'] = 'Note';
$string['numbersqlvalueshelp_help'] = 'Max 20 in this sample set, no limit on real usage';
$string['samplesqlvalues'] = 'Sample of possible values';
$string['sqlerror'] = 'Error executing the query';
$string['dynamicmultiselect:caneditsql'] = 'Can edit sql query for dynamic multiselect user custom field';
$string['param1sqlhelp'] = 'Sql query';
$string['param1sqlhelp_help'] = 'The query should return two columns: data and id.
ATTENTION: If you want that the data is saved, make id and data the same. If you wanto to save the keys 
(example: save userids, but display usernames so you can select them in a user friendly way: 
SELECT u.id as id
CONCAT(u.firstname, u.lastname)
AS data
FROM mdl_user u), then make id and data different.
Furthermore, it should return at least one value. Example: "select 1 id, \'hallo\' data"';
$string['savekey'] = 'Check if you want to save the id value instead of the data value from your SQL query.';
$string['savekeylabel'] = 'Save id of SQL query';