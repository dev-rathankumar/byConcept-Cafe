<?php

/**
 * WXR Parser that makes use of the SimpleXML PHP extension.
 */
class esig_Parser_SimpleXML {

    function parse($file, $tableName, $queryNumber) {
        $authors = $posts = $categories = $tags = $terms = array();

        $internal_errors = libxml_use_internal_errors(true);

        $dom = new DOMDocument;
        $old_value = null;
        if (function_exists('libxml_disable_entity_loader')) {
            $old_value = libxml_disable_entity_loader(true);
        }
        $success = $dom->loadXML(file_get_contents($file));
        if (!is_null($old_value)) {
            libxml_disable_entity_loader($old_value);
        }

        if (!$success || isset($dom->doctype)) {
            return new WP_Error('SimpleXML_parse_error', __('There was an error when reading this WXR file', 'wordpress-importer'), libxml_get_errors());
        }



        $xml = simplexml_import_dom($dom);
        unset($dom);

        // halt if loading produces an error
        if (!$xml)
            return new WP_Error('SimpleXML_parse_error', __('There was an error when reading this WXR file', 'wordpress-importer'), libxml_get_errors());


        $results = $xml->xpath('/approveme/table[@name="' . $tableName . '"]/record');

        $countResult = count($results);

        if ($countResult > 200) {

            $startNumber = 200 * $queryNumber;

            if ($startNumber > $countResult) {
                return false;
            }
            $slice = array_slice($results, $startNumber, 200);

            $nextQueryNumber = 200 * ($queryNumber + 1);
            if ($nextQueryNumber >= $countResult) {
                $slice['nextQuery'] = 0;
            } else {
                $slice['nextQuery'] = 1;
            }
            $slice['totalRecord'] = $countResult;
            return $slice;
        } else {
            $results['totalRecord'] = $countResult;
            $results['nextQuery'] = 0;
        }

        return $results;
    }

}

/**
 * WXR Parser that makes use of the XML Parser PHP extension.
 */
class esig_Parser_XML {

    var $wp_tags = array();
    var $wp_sub_tags = array();
    var $startNumber = 0;
    var $limit = 500;
    var $totalRecord = 0;
    var $primary_key = false;
    var $tableName = null;

    function parse($file, $tableName, $sub_tags, $primary_key, $queryNumber) {

        $this->wp_tags[] = $tableName;
        $this->tableName = $tableName;
        $this->wp_sub_tags = $sub_tags;
        $this->primary_key = $primary_key;
        $this->startNumber = $queryNumber;
        $this->limit = $this->startNumber + $this->limit;

        $this->cdata = $this->data = $this->sub_data = $this->in_tag = $this->in_sub_tag = false;
        $this->records = array();

        $xml = xml_parser_create('UTF-8');
        xml_parser_set_option($xml, XML_OPTION_SKIP_WHITE, 1);
        xml_parser_set_option($xml, XML_OPTION_CASE_FOLDING, 0);
        xml_set_object($xml, $this);
        xml_set_character_data_handler($xml, 'cdata');
        xml_set_element_handler($xml, 'tag_open', 'tag_close');

        if (!xml_parse($xml, file_get_contents($file), true)) {
            $current_line = xml_get_current_line_number($xml);
            $current_column = xml_get_current_column_number($xml);
            $error_code = xml_get_error_code($xml);
            $error_string = xml_error_string($error_code);
            return new WP_Error('XML_parse_error', 'There was an error when reading this WXR file', array($current_line, $current_column, $error_string));
        }
        xml_parser_free($xml);

        //if ( ! preg_match( '/^\d+\.\d+$/', $this->wxr_version ) )
        //return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer' ) );

        if ($this->totalRecord <= $this->limit) {
            $this->records['nextQuery'] = 0;
        } else {
            $this->records['nextQuery'] = $this->limit + 1;
        }
        $this->records['totalRecord'] = $this->totalRecord;

        return $this->records;
    }

    function tag_open($parse, $tag, $attr) {

        if (in_array($tag, $this->wp_tags)) {
            $this->in_tag = $tag;  // substr( $tag, 3 );
            return;
        }
        if (in_array($tag, $this->wp_sub_tags)) {
            $this->in_sub_tag = $tag; //substr( $tag, 3 );
            return;
        }
    }

    function cdata($parser, $cdata) {

        if (!trim($cdata))
            return;

        if (false !== $this->in_tag || false !== $this->in_sub_tag) {
            $this->cdata .= $cdata;
        } else {
            $this->cdata .= trim($cdata);
        }
    }

    function tag_close($parser, $tag) {

        if ($this->tableName == $tag) {

            if (!empty($this->sub_data)) {
                $this->totalRecord = $this->sub_data[$this->primary_key];
                if ($this->startNumber <= $this->sub_data[$this->primary_key] && $this->limit >= $this->sub_data[$this->primary_key]) {
                    $this->records[] = $this->sub_data;
                }
            }

            $this->sub_data = false;
            return;
        }


        switch ($tag) {

            case 'approveme':
            case 'siteinfo':
            default:
                if ($this->in_sub_tag) {
                    $this->sub_data[$this->in_sub_tag] = !empty($this->cdata) ? $this->cdata : '';
                    $this->in_sub_tag = false;
                } else if ($this->in_tag) {
                    $this->data[$this->in_tag] = !empty($this->cdata) ? $this->cdata : '';
                    $this->in_tag = false;
                }
        }

        $this->cdata = false;
    }

}
