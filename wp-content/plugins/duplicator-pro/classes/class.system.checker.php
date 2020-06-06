<?php
defined("ABSPATH") or die("");
if (!defined('DUPLICATOR_PRO_VERSION')) exit; // Exit if accessed directly

require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.global.entity.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.storage.entity.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/class.u.import.php');

if (DUP_PRO_U::PHP53()) {
    require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/net/class.u.s3.php');
}

class DUP_PRO_System_Checker
{
    const SYSTEM_CHECK_PERIOD_IN_SEC = 86400;  // 24 hours

    public static function check()
    {
        $global = DUP_PRO_Global_Entity::get_instance();
        $time   = time();
                
        if (($time - $global->last_system_check_timestamp) >= self::SYSTEM_CHECK_PERIOD_IN_SEC) {
            
            DUP_PRO_LOG::trace("Doing system checker check because time = $time and last timestamp = {$global->last_system_check_timestamp}");
                
            if (DUP_PRO_U::PHP53()) {
                         
                try {
                    self::purgeOldS3MultipartUploads();
                } catch (Exception $ex) {
                    DUP_PRO_LOG::trace("Got exception during s3 system check: ".$ex->getMessage());
                }
            }
            
            DUP_PRO_Import_U::PurgeOldImports();
                
            $global->last_system_check_timestamp = time();
            $global->save();
        }
    }

    private static function purgeOldS3MultipartUploads()
    {
        $storages = DUP_PRO_Storage_Entity::get_all();

        foreach ($storages as $storage) {
            if ($storage->storage_type == DUP_PRO_Storage_Types::S3) {
                $s3_client = $storage->get_full_s3_client();

                $active_uploads = DUP_PRO_S3_U::get_active_multipart_uploads($s3_client, $storage->s3_bucket, $storage->s3_storage_folder);

                if (($active_uploads != null) && is_array($active_uploads)) {
                    foreach ($active_uploads as $active_upload) {
                        // Needs to be at least 48 hours old - don't want to much around with timezone so this is safe
                        $time_delta = time() - $active_upload->timestamp;

                        if ($time_delta > (48 * 3600)) {
                            DUP_PRO_LOG::trace("Aborting upload because timestamp = {$active_upload->timestamp} while time is ".time());
                            DUP_PRO_S3_U::abort_multipart_upload($s3_client, $storage->s3_bucket, $active_upload->key, $active_upload->upload_id);
                        }
                    }
                }
            }
        }
    }
}