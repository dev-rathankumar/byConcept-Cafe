<?php
	
	require_once(dirname(__FILE__).'../../../../wp-config.php');


if (class_exists('Customerly')) {
    if (isset($_POST["fields"])) {

        header('Content-Type: application/json');

        $data = array();
        $fields = $_POST["fields"];
        $field_keys = array_keys($fields);

        if (count($field_keys) > 0) {

            for ($i = 0; $i != count($field_keys); $i++) {

                $k = $field_keys[$i];
                $field = $fields[$k];

                if (isset($field["id"])) {
                    if (isset($field["value"])) {
                        $id = $field["id"];
                        $value = $field["value"];
                        if ($id == "email") {
                            $email = $value;
                        }
                        if ($id == "name") {
                            $name = $value;
                        }
                        $data[$id] = $value;
                    }
                }

            }

            if (isset($email)) {
                $result = Customerly::create_leads($email, $name, $data);
            }
        }
        print_r($result);

    } else {
        print_r(array('result' => 'error'));
    }
}
