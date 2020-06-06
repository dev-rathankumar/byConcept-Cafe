<?php
defined("ABSPATH") or die("");
class DUP_PRO_Import_U
{
    public static function PurgeOldImports() 
    {
		if(file_exists(DUPLICATOR_PRO_PATH_IMPORTS)) {
			$files = scandir(DUPLICATOR_PRO_PATH_IMPORTS);

			if($files !== false) {
				foreach ($files as $file) {
					$filepath = DUPLICATOR_PRO_PATH_IMPORTS . "/{$file}";
					DUP_PRO_LOG::trace("checking {$filepath}");
					if(is_file($filepath)) {
						if (filemtime($filepath) <= time() - DUP_PRO_Constants::IMPORTS_CLEANUP_SECS) {
							@unlink($filepath);
						}
					}
				}
			} else {
				DUP_PRO_LOG::trace("Couldn't get list of files in " . DUPLICATOR_PRO_PATH_IMPORTS);
			}
		}
    }
    
    public static function PurgeAllImports()
    {
        $files = scandir(DUPLICATOR_PRO_PATH_IMPORTS);
        
        if($files !== false) {
            foreach ($files as $file) {
                $filepath = DUPLICATOR_PRO_PATH_IMPORTS . "/{$file}";
                @unlink($filepath);
            }
        } else {
            DUP_PRO_LOG::trace("Couldn't get list of files in " . DUPLICATOR_PRO_PATH_IMPORTS);
        }
    }
}