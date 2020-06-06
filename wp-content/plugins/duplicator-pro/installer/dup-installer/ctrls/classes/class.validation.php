<?php

class DUPX_Validation
{

    public static function getValidateResult()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $result        = array();

        if (DUPX_Conf_Utils::archiveExists()) {
            $arcCheck = 'Pass';
        } else {
            if (DUPX_Conf_Utils::isConfArkPresent()) {
                $arcCheck = 'Warn';
            } else {
                $arcCheck = 'Fail';
            }
        }

        // throw new Exception('test exception');

        $result['arcCheck']            = $arcCheck;
        $result['ret_is_dir_writable'] = DUPX_Server::is_dir_writable($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW));
        $datetime1                     = $GLOBALS['DUPX_AC']->created;
        $datetime2                     = date("Y-m-d H:i:s");
        $result['fulldays']            = round(abs(strtotime($datetime1) - strtotime($datetime2)) / 86400);
        $result['max_time_zero']       = ($GLOBALS['DUPX_ENFORCE_PHP_INI']) ? false : @set_time_limit(0);
        $result['max_time_size']       = 314572800;  //300MB
        $result['max_time_ini']        = ini_get('max_execution_time');
        $result['max_time_warn']       = (is_numeric($result['max_time_ini']) && $result['max_time_ini'] < 31 && $result['max_time_ini'] > 0) && DUPX_Conf_Utils::archiveSize() > $result['max_time_size'];

        $result['parent_has_wordfence'] = self::parentHasWordfence();

        //REQUIRMENTS
        $result['req']     = self::getReq($result['ret_is_dir_writable']);
        $result['all_req'] = in_array('Fail', $result['req']) ? 'Fail' : 'Pass';

        //NOTICES
        $result['notice']     = self::getNotices();
        $result['all_notice'] = in_array('Warn', $result['notice']) ? 'Warn' : 'Good';

        //SUMMATION
        $result['req_success'] = ($result['all_req'] == 'Pass' && $arcCheck != 'Fail');
        $result['req_notice']  = ($result['all_notice'] == 'Good');
        $result['all_success'] = ($result['req_success'] && $result['req_notice']);

        $req_counts                         = array_count_values($result['req']);
        $result['is_only_permission_issue'] = (isset($req_counts['Fail']) && 1 == $req_counts['Fail'] && 'Fail' == $result['req'][10] && 'Fail' == $result['all_req'] && $arcCheck != 'Fail');

        return $result;
    }

    protected static function getReq($ret_is_dir_writable)
    {
        $req       = array();
        $req['10'] = $ret_is_dir_writable['ret'] ? 'Pass' : 'Fail';
        $req['20'] = function_exists('mysqli_connect') ? 'Pass' : 'Fail';

        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $replaceEngine = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_REPLACE_ENGINE);
        if ($paramsManager->isHtmlInput(DUPX_Paramas_Manager::PARAM_REPLACE_ENGINE) || $replaceEngine !== DUPX_S3_Funcs::MODE_SKIP) {
            $req['50'] = 'Pass';
        } else {
            if (DUPX_InstallerState::getInstance()->isInstallerCreatedInThisLocation()) {
                $req['50'] = 'Pass';
            } else {
                $req['50'] = 'Fail';
            }
        }

        /**
         * not supporte yet
         */
        $managed = DUPX_Custom_Host_Manager::getInstance()->isManaged();
        if ($managed === DUPX_Custom_Host_Manager::HOST_WORDPRESSCOM || $managed === DUPX_Custom_Host_Manager::HOST_PANTHEON) {
            $req['60'] = 'Fail';
        } else {
            $req['60'] = 'Pass';
        }
        return $req;
    }

    protected static function getNotices()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $openbase             = ini_get("open_basedir");
        $datetime1            = $GLOBALS['DUPX_AC']->created;
        $datetime2            = date("Y-m-d H:i:s");
        $fulldays             = round(abs(strtotime($datetime1) - strtotime($datetime2)) / 86400);
        $max_time_size        = 314572800;  //300MB
        $max_time_ini         = ini_get('max_execution_time');
        $max_time_warn        = (is_numeric($max_time_ini) && $max_time_ini < 31 && $max_time_ini > 0) && DUPX_Conf_Utils::archiveSize() > $max_time_size;
        $parent_has_wordfence = self::parentHasWordfence();

        $notice       = array();
        $notice['10'] = DUPX_InstallerState::getInstance()->getMode() !== DUPX_InstallerState::MODE_OVR_INSTALL ? 'Good' : 'Warn';
        $notice['20'] = !DUPX_Conf_Utils::isConfArkPresent() ? 'Good' : 'Warn';
        if ($archiveConfig->exportOnlyDB) {
            $notice['25'] = DUPX_Server::isWordPress() ? 'Good' : 'Warn';
        }
        $notice['30'] = $fulldays <= 180 ? 'Good' : 'Warn';

        $packagePHP      = $archiveConfig->version_php;
        $packagePHPMajor = intval($packagePHP);
        $currentPHPMajor = intval(phpversion());
        $notice['45']    = ($packagePHPMajor === $currentPHPMajor || $GLOBALS['DUPX_AC']->exportOnlyDB) ? 'Good' : 'Warn';

        $notice['50'] = empty($openbase) ? 'Good' : 'Warn';
        $notice['60'] = !$max_time_warn ? 'Good' : 'Warn';
        $notice['70'] = !$parent_has_wordfence ? 'Good' : 'Warn';
        $notice['80'] = !$GLOBALS['DUPX_AC']->is_outer_root_wp_config_file ? 'Good' : 'Warn';
        if ($archiveConfig->exportOnlyDB) {
            $notice['90'] = 'Good';
        } else {
            $notice['90'] = (!$GLOBALS['DUPX_AC']->is_outer_root_wp_content_dir) ? 'Good' : 'Warn';
        }

        $space_free    = @disk_free_space($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW));
        $archive_size  = DUPX_Conf_Utils::archiveSize();
        $notice['100'] = ($space_free && $archive_size > 0 && $archive_size > $space_free) ? 'Warn' : 'Good';

        $notice['110'] = (DUPX_Custom_Host_Manager::getInstance()->isManaged() && $GLOBALS['DUPX_AC']->wp_tableprefix != DUPX_WPConfig::getValueFromLocalWpConfig('table_prefix', 'variable')) ? 'Warn' : 'Good';

        return $notice;
    }

    protected static function parentHasWordfence()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $path          = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW).'/../wp-content/plugins/wordfence/wordfence.php';
        DUPX_Handler::setMode(DUPX_Handler::MODE_OFF);
        $result        = @file_exists($path);
        DUPX_Handler::setMode();
        return $result;
    }
}