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
 *
 * Multiselect profile field.
 *
 * @package   profilefield_dynamicmultiselect
 * @copyright  2016 onwards Antonello Moro {http://antonellomoro.it}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Class profile_field_multiselect
 *
 * @copyright  2016 onwards Antonello Moro {http://antonellomoro.it}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_field_dynamicmultiselect extends profile_field_base {
    /** @var array $options */
    public array $options;

    /** @var array $datakey */
    public static array $datakey = [];

    /** @var  array @calls array indexed by @fieldid-$userid. It keeps track of recordset,
     * so that we don't do the query twice for the same field */
    private static array $acalls = [];

    /**
     * Constructor method.
     *
     * Pulls out the options for the menu from the database and sets the corresponding key for the data if it exists.
     *
     * @param int $fieldid
     * @param int $userid
     */
    public function __construct($fieldid=0, $userid=0) {
        global $DB;
        // First call parent constructor.
        parent::__construct($fieldid, $userid, $fielddata = null);
        // Only if we actually need data.
        if ($fieldid !== 0 && $userid !== 0) {
            $mykey = $fieldid.','.$userid; // It will always work because they are number, so no chance of ambiguity.
            if (array_key_exists($mykey , self::$acalls)) {
                $rs = self::$acalls[$mykey];
            } else {
                $sql = $this->field->param1;
                $rs = $DB->get_records_sql($sql);
                self::$acalls[$mykey] = $rs;
            }
            $this->options = [];
            if ($this->field->required) {
                $this->options[''] = get_string('choose').'...';
            }

            foreach ($rs as $key => $option) {
                $this->options[format_string($key)] = format_string($option->data); // Multilang formatting.
            }

            // Set the data key.
            if ($this->data !== null) {
                $this->data = str_replace("\r", '', $this->data);
                $datatmp = explode("\n", $this->data);
                if ($this->field->param2 === '1') {
                    foreach ($datatmp as $key => $option1) {
                        if (isset($this->options[$option1])) {
                            self::$datakey[] = $option1;
                        }
                    }
                } else {
                    foreach ($datatmp as $key => $option1) {
                        static::$datakey[] = (int)array_search($option1, $this->options);
                    }
                }
            }
        }

    }

    /**
     * deprecated old costructor
     * @param int $fieldid
     * @param int $userid
     */
    public function profile_field_menu(int $fieldid = 0, int $userid = 0) {
        self::__construct($fieldid, $userid);
    }

    /**
     * Create the code snippet for this field instance
     * Overwrites the base class method
     * @param moodleform $mform
     */
    public function edit_field_add($mform) {
        $mform->addElement('autocomplete', $this->inputname, format_string($this->field->name), $this->options);
        $mform->getElement($this->inputname)->setMultiple(true);
    }

    /**
     * Set the default value for this field instance
     * Overwrites the base class method
     * @param moodleform $mform
     */
    public function edit_field_set_default($mform) {
        if (false !== array_search($this->field->defaultdata, $this->options)) {
            $defaultkey = (int)array_search($this->field->defaultdata, $this->options);
        } else {
            $defaultkey = '';
        }
        $mform->setDefault($this->inputname, $defaultkey);
    }

    /**
     * The data from the form returns the key. This should be converted to the
     * respective option string to be saved in database
     * Overwrites base class accessor method
     * @param   mixed    $data - the key returned from the select input in the form
     * @param   stdClass $datarecord The object that will be used to save the record
     */
    public function edit_save_data_preprocess($data, $datarecord) {
        $string = '';
        if (is_array($data)) {
            foreach ($data as $key) {
                // Check if the id or the data value from the query should be saved.
                if (isset($this->options[$key]) && $this->field->param2 === '1') {
                    $string .= $key."\r\n";
                } else if (isset($this->options[$key])){
                    $string .= $this->options[$key]."\r\n";
                }
            }
            return substr($string, 0, -2);
        }
        return $this->options[$data] ?? null;
    }

    /**
     * When passing the user object to the form class for the edit profile page
     * we should load the key for the saved data
     * Overwrites the base class method
     * @param stdClass $user
     */
    public function edit_load_user_data($user) {
        if ($this->data !== null) {
            $user->{$this->inputname} = profile_field_dynamicmultiselect::$datakey;
        }
    }

    /**
     * HardFreeze the field if locked.
     * @param moodleform $mform
     * @throws coding_exception
     * @throws dml_exception
     */
    public function edit_field_set_locked($mform) {
        if (!$mform->elementExists($this->inputname)) {
            return;
        }
        if ($this->is_locked() and !has_capability('moodle/user:update', context_system::instance())) {
            $mform->hardFreeze($this->inputname);
            $mform->setConstant($this->inputname, self::$datakey);
        }
    }
    /**
     * Convert external data (csv file) from value to key for processing later
     * by edit_save_data_preprocess
     *
     * @param string $value one of the values in menu options.
     * @return int options key for the menu
     */
    public function convert_external_data(string $value): ?int {
        $retval = array_search($value, $this->options);

        // If value is not found in options then return null, so that it can be handled
        // later by edit_save_data_preprocess.
        if ($retval === false) {
            $retval = null;
        }
        return $retval;
    }

    /**
     * Display the data for this field.
     */
    public function display_data(): string {
        $sql = $this->field->param1;
        global $DB;
        $rs = $DB->get_records_sql($sql);
        $ret = '';
        foreach (profile_field_dynamicmultiselect::$datakey as $key) {
            if (array_key_exists($key, $rs)) {
                if ($this->field->param2 === '1') {
                    $ret .= $key. "\r\n";
                } else {
                    $ret .= $rs[$key]->data . "\r\n";
                }
            } else {
                $ret .= 'N/A' . "\r\n";
            }
        }
        return $ret;
    }
}


