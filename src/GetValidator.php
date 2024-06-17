<?php

namespace Ilem\Validator;

use PDO;

/**
 * @author ilem Ilem <ilemsroom@gmail.com>
 * GetValidator
 */

 class GetValidator extends Validator{
    
    public function __construct(array $db_config = [])
    {

        $this->request = $_GET;

        foreach ($this->request as $key => $value) {
            $this->$key = $value;
        }

        if (!empty($db_config)) {
            foreach ($db_config as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                } else {
                    throw new \Exception('UnKnow Configuration ' . $key, 0);
                }
            }

        } else {
            foreach ($db_config as $key => $value) {
                $this->$key = $value;
            }
        }
    }
 }