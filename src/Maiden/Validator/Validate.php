<?php

class Validate
{
    private $_passed = false;
    private $_errors = array();
    private $_db = null;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    public function check($source, $items = array())
    {
        foreach ($items as $item => $rules) {
            foreach ($rules as $rule => $rule_value) {
                //echo "{$item} {$rule} must be {$rule_value} ". '<br>';

                $value = trim($source[$item]);

                if ($rule === 'required' && empty($value)) {
                    $this->addError("{$item} is required");
                } elseif (!empty($value)) {
                    switch ($rule) {
                        case 'min':
                            if (strlen($value) < $rule_value) {
                                $this->addError("{$item} must be a minimum of {$rule_value}");
                            }
                            break;
                        case 'max':
                            if (strlen($value) > $rule_value) {
                                $this->addError("{$item} must be a maximum of {$rule_value}");
                            }
                            break;
                        case 'matches':
                            if ($value != $source[$rule_value]) {
                                // something must match
                                $this->addError("{$rule_value} must match {$item}");
                            }
                            break;
                        case 'unique':
                            $check = $this->db->get($rule_value, array($item, '=', $value));
                            if ($check->count()) {
                                $this->addError("{$item} already exists.");
                            }
                            break;
                        default:
                            break;
                    }

                }

            }

        }

        if (empty($this->_errors)) {
            $this->_passed = true;
        }

        return $this;
    }

    private function addError($error)
    {
        $this->_errors[] = $error;
    }


    public function errors()
    {
        return $this->_errors;
    }

    public function passed()
    {
        return $this->_passed;
    }

    /*
     *
         username required must be 1
        username min must be 2
        username max must be 20
        username unique must be users
        password required must be 1
        password min must be 6
        password_again required must be 1
        password_again matches must be password
        name required must be 1
        name min must be 6
        name max must be 50

        Fatal error: Call to a member function passed()
        on a non-object in
        /hermes/bosoraweb081/b2604/ipg.pingpongpartnerscom/UniversityOfCs/portfolio/ppp/register.php
        on line 28

    */

}

?>