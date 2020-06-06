<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/package/class.pack.archive.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/package/duparchive/class.pack.archive.duparchive.state.expand.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/package/duparchive/class.pack.archive.duparchive.state.create.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.global.entity.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.system.global.entity.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/lib/dup_archive/classes/class.duparchive.loggerbase.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/lib/dup_archive/classes/class.duparchive.engine.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/lib/dup_archive/classes/states/class.duparchive.state.create.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/lib/dup_archive/classes/states/class.duparchive.state.expand.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.duparchive.expandstate.entity.php');
require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.exceptions.php');

class DUP_PRO_Dup_Archive_Logger extends DupArchiveLoggerBase
{

    public function log($s, $flush = false, $callingFunctionOverride = null)
    {
        // rsr todo ignoring flush for now
        DUP_PRO_LOG::trace($s, true, $callingFunctionOverride);
    }
}

/**
 *  DUP_PRO_ZIP
 *  Creates a zip file using the built in PHP ZipArchive class
 */
class DUP_PRO_Dup_Archive extends DUP_PRO_Archive
{
    // Using a worker time override since evidence shorter time works much
    const WorkerTimeInSec = 10;

    /**
     *  CREATE
     *  Creates the zip file and adds the SQL file to the archive
     */
    public static function create(DUP_PRO_Archive $archive, $buildProgress)
    {
        /* @var $buildProgress DUP_PRO_Build_Progress */

        try {
            $package = &$archive->Package;

            if ($buildProgress->retries > DUP_PRO_Constants::MAX_BUILD_RETRIES) {
                $error_msg              = DUP_PRO_U::__('Package build appears stuck so marking package as failed. Is the Max Worker Time set too high?.');
                DUP_PRO_Log::error(DUP_PRO_U::__('Build Failure'), $error_msg, false);
                $buildProgress->failed = true;
                return true;
            } else {
                // If all goes well retries will be reset to 0 at the end of this function.
                $buildProgress->retries++;
                $archive->Package->update();
            }

            /* @var $archive DUP_PRO_Archive */
            /* @var $buildProgress DUP_PRO_Build_Progress */
            $global = DUP_PRO_Global_Entity::get_instance();
            $done   = false;

            $profileEventFunction = null;

            if ($global->trace_profiler_on) {
                $profileEventFunction = 'DUP_PRO_LOG::profile';
            }

            DupArchiveEngine::init(new DUP_PRO_Dup_Archive_Logger(), $profileEventFunction, $archive);

            $archive->Package->safe_tmp_cleanup(true);

            $compressDir  = rtrim(DupProSnapLibIOU::safePath($archive->PackDir), '/');
            $sqlPath      = DupProSnapLibIOU::safePath("{$archive->Package->StorePath}/{$archive->Package->Database->File}");
            $archivePath  = DupProSnapLibIOU::safePath("{$archive->Package->StorePath}/{$archive->File}");

			$filterDirs	 = empty($archive->FilterDirs)  ? 'not set' : rtrim(str_replace(';', "\n\t", $archive->FilterDirs));
			$filterFiles = empty($archive->FilterFiles) ? 'not set' : rtrim(str_replace(';', "\n\t", $archive->FilterFiles));
            $filterExts  = empty($archive->FilterExts)  ? 'not set' : $archive->FilterExts;
            $filterOn    = ($archive->FilterOn) ? 'ON' : 'OFF';
			
            $scanFilepath = DUPLICATOR_PRO_SSDIR_PATH_TMP."/{$archive->Package->NameHash}_scan.json";

            $skipArchiveFinalization = false;

            try{
                $scanReport = $package->getScanReportFromJson($scanFilepath);
            }catch(DUP_PRO_NoScanFileException $ex){
                DUP_PRO_LOG::trace("**** scan file $scanFilepath doesn't exist!!");

                DUP_PRO_Log::error($ex->getMessage(), '', false);

                $buildProgress->failed = true;
                return true;
            }catch (DUP_PRO_NoFileListException $ex){
                DUP_PRO_LOG::trace("**** list of files doesn't exist!!");

                DUP_PRO_Log::error($ex->getMessage(), '', false);

                $buildProgress->failed = true;
                return true;
            }catch(DUP_PRO_NoDirListException $ex){
                DUP_PRO_LOG::trace("**** list of directories doesn't exist!!");

                DUP_PRO_Log::error($ex->getMessage(), '', false);

                $buildProgress->failed = true;
                return true;
            }catch(DUP_PRO_EmptyScanFileException $ex){
                $errorText = $ex->getMessage();
                $fixText = DUP_PRO_U::__("Click on \"Resolve This\" button to fix the JSON settings.");

                DUP_PRO_LOG::trace($errorText);
                DUP_PRO_Log::error("$errorText **RECOMMENDATION:  $fixText.", '', false);

                $systemGlobal = DUP_PRO_System_Global_Entity::get_instance();

                $systemGlobal->add_recommended_quick_fix($errorText, $fixText, 'global:{json_mode:1}');

                $systemGlobal->save();

                $buildProgress->failed = true;
                return true;
            }

            // Ensure database sql is added
            $scanReport->ARC->Files[] = $sqlPath;
            $scanReport->ARC->FileAliases = array();
            $scanReport->ARC->FileAliases[$sqlPath] = $archive->Package->get_sql_ark_file_path();


            if ($buildProgress->archive_started == false) {

                DUP_PRO_Log::info("\n********************************************************************************");
                DUP_PRO_Log::info("ARCHIVE Type=DUP Mode=DupArchive");
                DUP_PRO_Log::info("********************************************************************************");
                DUP_PRO_Log::info("ARCHIVE DIR:  ".$compressDir);
                DUP_PRO_Log::info("ARCHIVE FILE: ".basename($archivePath));
                DUP_PRO_Log::info("FILTERS: *{$filterOn}*");
                DUP_PRO_Log::info("DIRS:  {$filterDirs}");
                DUP_PRO_Log::info("EXTS:  {$filterExts}");
                DUP_PRO_Log::info("FILES:  {$filterFiles}");

                DUP_PRO_Log::info("----------------------------------------");
                DUP_PRO_Log::info("COMPRESSING");
                DUP_PRO_Log::info("SIZE:\t".$scanReport->ARC->Size);
                DUP_PRO_Log::info("STATS:\tDirs ".$scanReport->ARC->DirCount." | Files ".$scanReport->ARC->FileCount." | Total ".$scanReport->ARC->FullCount);

                if (($scanReport->ARC->DirCount == '') || ($scanReport->ARC->FileCount == '') || ($scanReport->ARC->FullCount == '')) {
                    DUP_PRO_Log::error('Invalid Scan Report Detected', 'Invalid Scan Report Detected', false);
                    $buildProgress->failed = true;
                    return true;
                }

                try {
					DupArchiveEngine::createArchive($archivePath, $buildProgress->current_build_compression);
                } catch (Exception $ex) {
                    DUP_PRO_Log::error('Error initializing archive', $ex->getMessage(), false);
                    $buildProgress->failed = true;
                    return true;
                }

                $buildProgress->archive_started = true;

                $buildProgress->retries = 0;

                $archive->Package->Update();
            }

            try {
                if ($buildProgress->custom_data == null) {
                    $createState = DUP_PRO_Dup_Archive_Create_State::createNew($archive->Package, $archivePath, $compressDir, self::WorkerTimeInSec, $buildProgress->current_build_compression, true);
                } else {
                    DUP_PRO_LOG::traceObject('Resumed build_progress', $archive->Package->build_progress);
                    $createState = DUP_PRO_Dup_Archive_Create_State::createFromPackage($archive->Package);
                }

                if($buildProgress->retries > 1) {
                    // Indicates it had problems before so move into robustness mode
                    $createState->isRobust = true;
                    //$createState->timeSliceInSecs = self::WorkerTimeInSec / 2;
                    $createState->save();
                }

                if ($createState->working) {
                    DupArchiveEngine::addItemsToArchive($createState, $scanReport->ARC);

                    if($createState->isCriticalFailurePresent()) {

                        throw new Exception($createState->getFailureSummary());
                    }

                    $totalFileCount = count($scanReport->ARC->Files);
                    DUP_PRO_Log::trace("Total file count ".$totalFileCount);
                    
                    $status = DupProSnapLibUtil::getWorkPercent(DUP_PRO_PackageStatus::ARCSTART, DUP_PRO_PackageStatus::ARCVALIDATION, $totalFileCount, $createState->currentFileIndex);
                    
                    if ($status == DUP_PRO_PackageStatus::ARCSTART) {
                        do_action('duplicator_pro_package_before_set_status' , $archive->Package , DUP_PRO_PackageStatus::ARCSTART);
                    } elseif ($status == DUP_PRO_PackageStatus::ARCVALIDATION) {
                        do_action('duplicator_pro_package_before_set_status' , $archive->Package , DUP_PRO_PackageStatus::ARCVALIDATION);
                    }

                    $archive->Package->Status = $status;

                    $buildProgress->retries = 0;

                    $createState->save();

                    if ($status == DUP_PRO_PackageStatus::ARCSTART) {
                        do_action('duplicator_pro_package_after_set_status' , $archive->Package , DUP_PRO_PackageStatus::ARCSTART);
                    } elseif ($status == DUP_PRO_PackageStatus::ARCVALIDATION) {
                        do_action('duplicator_pro_package_after_set_status' , $archive->Package , DUP_PRO_PackageStatus::ARCVALIDATION);
                    }

                    DUP_PRO_LOG::traceObject("Stored Create State", $createState);
                    DUP_PRO_LOG::traceObject('Stored build_progress', $archive->Package->build_progress);

                    if ($createState->working == false) {
                        // Want it to do the final cleanup work in an entirely new thread so return immediately
                        $skipArchiveFinalization = true;
                        DUP_PRO_LOG::traceObject("Done build phase. Create State=", $createState);
                    }
                }
            }catch (DupProSnapLib_32BitSizeLimitException $exception){
                $global = DUP_PRO_System_Global_Entity::get_instance();
                $err = 'Package build failure due to building a large package on 32 bit PHP.';
                $fix = sprintf("%s <a href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-package-035-q' target='_blank'>%s</a> %s",
                DUP_PRO_U::__('Package build failure due to building a large package on 32 bit PHP. Please see '),
                DUP_PRO_U::__("Tech docs"),
                DUP_PRO_U::__("for instructions on how to resolve."));
                $global->add_recommended_text_fix($err, $fix);
                $global->save();
                $buildProgress->failed = true;
                return true;
            } catch (Exception $ex) {
                $message = DUP_PRO_U::__('Problem adding items to archive.').' '.$ex->getMessage();

                DUP_PRO_Log::error(DUP_PRO_U::__('Problems adding items to archive.'), $message, false);
                DUP_PRO_LOG::traceObject($message." EXCEPTION:", $ex);
                $buildProgress->failed = true;
                return true;
            }

            //-- Final Wrapup of the Archive
            if ((!$skipArchiveFinalization) && ($createState->working == false)) {

                if(!$buildProgress->installer_built) {

                    $package->Installer->build($package, $buildProgress);

                    DUP_PRO_LOG::traceObject("INSTALLER", $package->Installer);

                    $expandStateEntity = DUP_PRO_DupArchive_Expand_State_Entity::get_by_package_id($archive->Package->ID);

                    if ($expandStateEntity == null) {

                        DUP_PRO_DupArchive_Expand_State_Entity::delete_all();

                        $expandStateEntity = new DUP_PRO_DupArchive_Expand_State_Entity();

                        $expandStateEntity->package_id = $archive->Package->ID;

                        $expandStateEntity->archivePath            = $archivePath;
                        $expandStateEntity->working                = true;
                        $expandStateEntity->timeSliceInSecs        = self::WorkerTimeInSec;
                        $expandStateEntity->basePath               = DUPLICATOR_PRO_SSDIR_PATH_TMP.'/validate';
                        $expandStateEntity->validateOnly           = true;
                        $expandStateEntity->validationType         = DupArchiveValidationTypes::Standard;
                        $expandStateEntity->working                = true;
                        $expandStateEntity->expectedDirectoryCount = count($scanReport->ARC->Dirs) - $createState->skippedDirectoryCount + $package->Installer->numDirsAdded; 
                        $expandStateEntity->expectedFileCount      = count($scanReport->ARC->Files) - $createState->skippedFileCount + $package->Installer->numFilesAdded;

                        DUP_PRO_LOG::traceObject("EXPAND STATE ENTITY", $expandStateEntity);
                        $expandStateEntity->save();
                    }
                }
                else {
                    // $build_progress->warnings = $createState->getWarnings(); Auto saves warnings within build progress along the way



                    try {
                        $expandStateEntity = DUP_PRO_DupArchive_Expand_State_Entity::get_by_package_id($archive->Package->ID);
                        
                        $expandState = new DUP_PRO_DupArchive_Expand_State($expandStateEntity);

                        if($buildProgress->retries > 1) {

                            // Indicates it had problems before so move into robustness mode
                            $expandState->isRobust = true;
                            //$expandState->timeSliceInSecs = self::WorkerTimeInSec / 2;
                            $expandState->save();
                        }

                        DUP_PRO_LOG::traceObject('Resumed validation expand state', $expandState);

                        DupArchiveEngine::expandArchive($expandState);

                        $totalFileCount = count($scanReport->ARC->Files);
                        $archiveSize    = @filesize($expandState->archivePath);

                        $status = DupProSnapLibUtil::getWorkPercent(DUP_PRO_PackageStatus::ARCVALIDATION, DUP_PRO_PackageStatus::ARCDONE, $archiveSize, $expandState->archiveOffset);

                        if ($status == DUP_PRO_PackageStatus::ARCDONE) {
                            do_action('duplicator_pro_package_before_set_status' , $archive->Package , DUP_PRO_PackageStatus::ARCDONE);
                        }

                        $archive->Package->Status = DupProSnapLibUtil::getWorkPercent(DUP_PRO_PackageStatus::ARCVALIDATION, DUP_PRO_PackageStatus::ARCDONE, $archiveSize, $expandState->archiveOffset);
                    } catch (Exception $ex) {
                        DUP_PRO_LOG::traceError('Exception:'.$ex->getMessage().':'.$ex->getTraceAsString());
                        $buildProgress->failed = true;
                        return true;
                    }

                    if($expandState->isCriticalFailurePresent())
                    {
                        // Fail immediately if critical failure present - even if havent completed processing the entire archive.

                        DUP_PRO_Log::error(DUP_PRO_U::__('Build Failure'), $expandState->getFailureSummary(), false);

                        $buildProgress->failed = true;
                        return true;
                    } else if (!$expandState->working) {

                        $buildProgress->archive_built = true;
                        $buildProgress->retries       = 0;

                        $archive->Package->update();

                        $timerAllEnd = DUP_PRO_U::getMicrotime();
                        $timerAllSum = DUP_PRO_U::elapsedTime($timerAllEnd, $archive->Package->timer_start);

                        DUP_PRO_LOG::traceObject("create state", $createState);

                        $archiveFileSize = @filesize($archivePath);
                        DUP_PRO_Log::info("COMPRESSED SIZE: ".DUP_PRO_U::byteSize($archiveFileSize));
                        DUP_PRO_Log::info("ARCHIVE RUNTIME: {$timerAllSum}");
                        DUP_PRO_Log::info("MEMORY STACK: ".DUP_PRO_Server::getPHPMemory());
                        DUP_PRO_LOG::info("CREATE WARNINGS: ".$createState->getFailureSummary(false, true));
                        DUP_PRO_LOG::info("VALIDATION WARNINGS: ".$expandState->getFailureSummary(false, true));

                        $archive->file_count = $expandState->fileWriteCount + $expandState->directoryWriteCount - $package->Installer->numDirsAdded - $package->Installer->numFilesAdded;

                        $archive->Package->update();

                        $done = true;

                        if ($status == DUP_PRO_PackageStatus::ARCDONE) {
                            do_action('duplicator_pro_package_after_set_status' , $archive->Package , DUP_PRO_PackageStatus::ARCDONE);
                        }
                        
                    } else {
                        $expandState->save();
                    }
                }
            }
        } catch (Exception $ex) {
            // Have to have a catchall since the main system that calls this function is not prepared to handle exceptions
            DUP_PRO_LOG::traceError('Top level create Exception:'.$ex->getMessage().':'.$ex->getTraceAsString());
            $buildProgress->failed = true;
            return true;
        }

        $buildProgress->retries = 0;

        return $done;
    }
}
