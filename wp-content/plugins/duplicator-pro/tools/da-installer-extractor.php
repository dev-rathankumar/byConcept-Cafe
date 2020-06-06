<?php
defined("ABSPATH") or die("");
/*
  Duplicator Pro Website Installer Bootstrap
  Copyright (C) 2017, Snap Creek LLC
  website: snapcreek.com

  Duplicator Pro Plugin is distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */


/**
 * Bootstrap utility to exatract the core installer
 *
 * Standard: PSR-2
 *
 * @package SC\DUPX\Bootstrap
 * @link http://www.php-fig.org/psr/psr-2/
 *
 *  To force extraction mode:
 *		installer.php?unzipmode=auto
 *		installer.php?unzipmode=ziparchive
 *		installer.php?unzipmode=shellexec
 */

class DupArchive_Installer_Extractor
{
		const VERSION			 = '3.8.9';

	/**
	 * Instantiate the Bootstrap Object
	 *
	 * @return null
	 */
	public function __construct()
	{
	}

	/**
	 * Run the bootstrap process which includes checking for requirements and running
	 * the extraction process
	 *
	 * @return null | string	Returns null if the run was successful otherwise an error message
	 */
	public function run()
	{
		echo "*** DupArchive Installer Extractor *** <br/><br/>";

		DupArchiveMiniExpander::init("DupArchive_Installer_Extractor::log");
		
		try {
			$candidates = glob('*_archive.daf');
			
			$numCandidates = count($candidates);
			
			if($numCandidates == 0) {
				die('Error. No .daf files found in this directory. Put one in the directory then browse to the extractor again.');
			} else if($numCandidates > 1) {
				die('Error. More than one .daf file found in this directory. Put exactly one in the directory then browse to the extractor again.');
			}
			
			$archivePath = dirname(__FILE__)."/{$candidates[0]}";
			$archiveName = basename($archivePath);
			
			$installerName = 'installer-backup.php';
			$installerPath = dirname(__FILE__)."/{$installerName}";
			
			DupArchiveMiniExpander::expandItems($archivePath, $installerName, dirname(__FILE__));
			
			if(!file_exists($installerPath)) {
				
				// Backup installer isn't there so look for full name				
				
				$installerPath = str_replace('_archive.daf', '_installer.php', $archivePath);							
				$installerName = basename($installerPath);
				
				DupArchiveMiniExpander::expandItems($archivePath, $installerName, dirname(__FILE__));
				
				if(!file_exists($installerPath)) {
					die("Error. Installer was not found within {$archivePath}");
				}				
			}
			
			echo "SUCCCESSFUL EXTRACT.<br/><br/>";
			echo "ARCHIVE: {$archiveName}<br/>";
			echo "INSTALLER: {$installerName}<br/>";
			
			$installerUrl = '//'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'])."/{$installerName}";
			die("<a href='{$installerUrl}'>Launch Installer</a>");
		} catch (Exception $ex) {
			self::log("Error expanding installer subdirectory:".$ex->getMessage());
			throw $ex;
		}		
	}	

	/**
     *  Attempts to set the 'dup-installer' directory permissions
     *
     * @return null
     */
	private function fixInstallerPerms()
	{
		$file_perms = substr(sprintf('%o', fileperms(__FILE__)), -4);
		$file_perms = octdec($file_perms);
		//$dir_perms = substr(sprintf('%o', fileperms(dirname(__FILE__))), -4);

		// No longer using existing directory permissions since that can cause problems.  Just set it to 755
		$dir_perms = '755';
		$dir_perms = octdec($dir_perms);
		$installer_dir_path = $this->installerContentsPath;

		$this->setPerms($installer_dir_path, $dir_perms, false);
		$this->setPerms($installer_dir_path, $file_perms, true);
	}

	/**
     * Set the permissions of a given directory and optionally all files
     *
     * @param string $directory		The full path to the directory where perms will be set
     * @param string $perms			The given permission sets to use such as '0755'
	 * @param string $do_files		Also set the permissions of all the files in the directory
     *
     * @return null
     */
	private function setPerms($directory, $perms, $do_files)
	{
		if (!$do_files) {
			// If setting a directory hiearchy be sure to include the base directory
			$this->setPermsOnItem($directory, $perms);
		}

		$item_names = array_diff(scandir($directory), array('.', '..'));

		foreach ($item_names as $item_name) {
			$path = "$directory/$item_name";
			if (($do_files && is_file($path)) || (!$do_files && !is_file($path))) {
				$this->setPermsOnItem($path, $perms);
			}
		}
	}

	/**
     * Set the permissions of a single directory or file
     *
     * @param string $path			The full path to the directory or file where perms will be set
     * @param string $perms			The given permission sets to use such as '0755'
     *
     * @return bool		Returns true if the permission was properly set
     */
	private function setPermsOnItem($path, $perms)
	{
		$result = @chmod($path, $perms);
		$perms_display = decoct($perms);
		if ($result === false) {
			self::log("Couldn't set permissions of $path to {$perms_display}<br/>");
		} else {
			self::log("Set permissions of $path to {$perms_display}<br/>");
		}
		return $result;
	}


	/**
     * Logs a string to the dup-installer-bootlog__[HASH].txt file
     *
     * @param string $s			The string to log to the log file
     *
     * @return null
     */
	public static function log($s)
	{
		$timestamp = date('M j H:i:s');
		//file_put_contents(self::BOOTSTRAP_LOG, "$timestamp $s\n", FILE_APPEND);
        echo "{$s}<br/>";
	}
	
	/**
     * Attempts to get the archive file path
     *
     * @return string	The full path to the archive file
     */
	private function getArchiveFilePath()
	{
		$archive_filename = self::ARCHIVE_FILENAME;

		if (isset($_GET['archive'])) {
			$archive_filename = $_GET['archive'];
		}

		$archive_filepath = str_replace("\\", '/', dirname(__FILE__) . '/' . $archive_filename);
		return $archive_filepath;
	}

	

	/**
     * Checks to see if a string starts with specific characters
     *
     * @return bool		Returns true if the string starts with a specific format
     */
	private function startsWith($haystack, $needle)
	{
		return $needle === "" || strrpos($haystack, $needle, - strlen($haystack)) !== false;
	}

	/**
     * Checks to see if the server supports issuing commands to shell_exex
     *
     * @return bool		Returns true shell_exec can be ran on this server
     */
	public function hasShellExec()
	{
		$cmds = array('shell_exec', 'escapeshellarg', 'escapeshellcmd', 'extension_loaded');

		//Function disabled at server level
		if (array_intersect($cmds, array_map('trim', explode(',', @ini_get('disable_functions'))))) return false;

		//Suhosin: http://www.hardened-php.net/suhosin/
		//Will cause PHP to silently fail
		if (extension_loaded('suhosin')) {
			$suhosin_ini = @ini_get("suhosin.executor.func.blacklist");
			if (array_intersect($cmds, array_map('trim', explode(',', $suhosin_ini)))) return false;
		}
		// Can we issue a simple echo command?
		if (!@shell_exec('echo duplicator')) return false;

		return true;
	}
}

$installerExtractor  = new DupArchive_Installer_Extractor();
$installerExtractor->run();

//---------- DUPARCHIVE MINI EXPANDER ------------------------

class DupArchiveHeaderMiniU
{
    const MaxStandardHeaderFieldLength = 128;

    public static function readStandardHeaderField($archiveHandle, $ename)
    {
        $expectedStart = "<{$ename}>";
        $expectedEnd = "</{$ename}>";

        $startingElement = fread($archiveHandle, strlen($expectedStart));

        if($startingElement !== $expectedStart) {
            throw new Exception("Invalid starting element. Was expecting {$expectedStart} but got {$startingElement}");
        }

        return stream_get_line($archiveHandle, self::MaxStandardHeaderFieldLength, $expectedEnd);
    }
}

class DupArchiveMiniItemHeaderType
{
    const None      = 0;
    const File      = 1;
    const Directory = 2;
    const Glob      = 3;
}

class DupArchiveMiniFileHeader
{
    public $fileSize;
    public $mtime;
    public $permissions;
    public $hash;
    public $relativePathLength;
    public $relativePath;

    static function readFromArchive($archiveHandle)
    {
        $instance = new DupArchiveMiniFileHeader();

        $instance->fileSize           = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'FS');
        $instance->mtime              = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'MT');
        $instance->permissions        = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'P');
        $instance->hash                = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'HA');
        $instance->relativePathLength = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'RPL');

        // Skip <RP>
        fread($archiveHandle, 4);

        $instance->relativePath       = fread($archiveHandle, $instance->relativePathLength);

        // Skip </RP>
        fread($archiveHandle, 5);

        // Skip the #F!
        //fread($archiveHandle, 3);
        // Skip the </F>
        fread($archiveHandle, 4);

        return $instance;
    }
}

class DupArchiveMiniDirectoryHeader
{
    public $mtime;
    public $permissions;
    public $relativePathLength;
    public $relativePath;

   // const MaxHeaderSize                = 8192;
   // const MaxStandardHeaderFieldLength = 128;

    static function readFromArchive($archiveHandle)
    {
        $instance = new DupArchiveMiniDirectoryHeader();

        $instance->mtime              = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'MT');
        $instance->permissions        = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'P');
        $instance->relativePathLength = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'RPL');

        // Skip the <RP>
        fread($archiveHandle, 4);

        $instance->relativePath       = fread($archiveHandle, $instance->relativePathLength);

        // Skip the </RP>
        fread($archiveHandle, 5);

        // Skip the </D>
        fread($archiveHandle, 4);

        return $instance;
    }
}

class DupArchiveMiniGlobHeader //extends HeaderBase
{
    public $originalSize;
    public $storedSize;
    public $hash;

 //   const MaxHeaderSize = 255;

   public static function readFromArchive($archiveHandle, $skipGlob)
    {
        $instance = new DupArchiveMiniGlobHeader();

      //  DupArchiveUtil::log('Reading glob starting at ' . ftell($archiveHandle));

        $startElement = fread($archiveHandle, 3);

        //if ($marker != '?G#') {
        if ($startElement != '<G>') {
            throw new Exception("Invalid glob header marker found {$startElement}. location:" . ftell($archiveHandle));
        }

        $instance->originalSize           = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'OS');
        $instance->storedSize             = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'SS');
        $instance->hash                    = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'HA');

        // Skip the </G>
        fread($archiveHandle, 4);

        if ($skipGlob) {
          //  DupProSnapLibIOU::fseek($archiveHandle, $instance->storedSize, SEEK_CUR);
		    if(fseek($archiveHandle, $instance->storedSize, SEEK_CUR) === -1)
			{
                throw new Exception("Can't fseek when skipping glob at location:".ftell($archiveHandle));
            }
        }

        return $instance;
    }
}

class DupArchiveMiniHeader
{
    public $version;
    public $isCompressed;

//    const MaxHeaderSize = 50;

    private function __construct()
    {
        // Prevent instantiation
    }

    public static function readFromArchive($archiveHandle)
    {
        $instance = new DupArchiveMiniHeader();

        $startElement = fgets($archiveHandle, 4);

        if ($startElement != '<A>') {
            throw new Exception("Invalid archive header marker found {$startElement}");
        }

        $instance->version           = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'V');
        $instance->isCompressed      = DupArchiveHeaderMiniU::readStandardHeaderField($archiveHandle, 'C') == 'true' ? true : false;

        // Skip the </A>
        fgets($archiveHandle, 5);

        return $instance;
    }
}

class DupArchiveMiniWriteInfo
{
    public $archiveHandle       = null;
    public $currentFileHeader   = null;
    public $destDirectory       = null;
    public $directoryWriteCount = 0;
    public $fileWriteCount      = 0;
    public $isCompressed        = false;
    public $enableWrite         = false;

    public function getCurrentDestFilePath()
    {
        if($this->destDirectory != null)
        {
            return "{$this->destDirectory}/{$this->currentFileHeader->relativePath}";
        }
        else
        {
            return null;
        }
    }

}

class DupArchiveMiniExpander
{

    public static $loggingFunction     = null;

    public static function init($loggingFunction)
    {
        self::$loggingFunction = $loggingFunction;
    }

    public static function log($s, $flush=false)
    {
        if(self::$loggingFunction != null) {
            call_user_func(self::$loggingFunction, "MINI EXPAND:$s", $flush);
        }
    }

    public static function expandDirectory($archivePath, $relativePath, $destPath)
    {
        self::expandItems($archivePath, $relativePath, $destPath);
    }

    public static function expandItems($archivePath, $inclusionFilter, $destDirectory, $ignoreErrors = false)
    {
        $archiveHandle = fopen($archivePath, 'rb');

        if ($archiveHandle === false) {
            throw new Exception("Can’t open archive at $archivePath!");
        }

        $archiveHeader = DupArchiveMiniHeader::readFromArchive($archiveHandle);

        $writeInfo = new DupArchiveMiniWriteInfo();

        $writeInfo->destDirectory = $destDirectory;
        $writeInfo->isCompressed  = $archiveHeader->isCompressed;

        $moreToRead = true;

        while ($moreToRead) {

            if ($writeInfo->currentFileHeader != null) {

                try {
                    if (self::passesInclusionFilter($inclusionFilter, $writeInfo->currentFileHeader->relativePath)) {

                        self::writeToFile($archiveHandle, $writeInfo);

                        $writeInfo->fileWriteCount++;
                    }
                    else if($writeInfo->currentFileHeader->fileSize > 0) {
                      //  self::log("skipping {$writeInfo->currentFileHeader->relativePath} since it doesn’t match the filter");

                        // Skip the contents since the it isn't a match
                        $dataSize = 0;

                        do {
                            $globHeader = DupArchiveMiniGlobHeader::readFromArchive($archiveHandle, true);

                            $dataSize += $globHeader->originalSize;

                            $moreGlobs = ($dataSize < $writeInfo->currentFileHeader->fileSize);
                        } while ($moreGlobs);
                    }

                    $writeInfo->currentFileHeader = null;

                    // Expand state taken care of within the write to file to ensure consistency
                } catch (Exception $ex) {

                    if (!$ignoreErrors) {
                        throw $ex;
                    }
                }
            } else {

                $headerType = self::getNextHeaderType($archiveHandle);

                switch ($headerType) {
                    case DupArchiveMiniItemHeaderType::File:

                        //$writeInfo->currentFileHeader = DupArchiveMiniFileHeader::readFromArchive($archiveHandle, $inclusionFilter);
						$writeInfo->currentFileHeader = DupArchiveMiniFileHeader::readFromArchive($archiveHandle);

                        break;

                    case DupArchiveMiniItemHeaderType::Directory:

                        $directoryHeader = DupArchiveMiniDirectoryHeader::readFromArchive($archiveHandle);

                     //   self::log("considering $inclusionFilter and {$directoryHeader->relativePath}");
                        if (self::passesInclusionFilter($inclusionFilter, $directoryHeader->relativePath)) {

                        //    self::log("passed");
                            $directory = "{$writeInfo->destDirectory}/{$directoryHeader->relativePath}";

                          //  $mode = $directoryHeader->permissions;

                            // rodo handle this more elegantly @mkdir($directory, $directoryHeader->permissions, true);
                            @mkdir($directory, 0755, true);


                            $writeInfo->directoryWriteCount++;
                        }
                        else {
                     //       self::log("didnt pass");
                        }


                        break;

                    case DupArchiveMiniItemHeaderType::None:
                        $moreToRead = false;
                }
            }
        }

        fclose($archiveHandle);
    }

    private static function getNextHeaderType($archiveHandle)
    {
        $retVal = DupArchiveMiniItemHeaderType::None;
        $marker = fgets($archiveHandle, 4);

        if (feof($archiveHandle) === false) {
            switch ($marker) {
                case '<D>':
                    $retVal = DupArchiveMiniItemHeaderType::Directory;
                    break;

                case '<F>':
                    $retVal = DupArchiveMiniItemHeaderType::File;
                    break;

                case '<G>':
                    $retVal = DupArchiveMiniItemHeaderType::Glob;
                    break;

                default:
                    throw new Exception("Invalid header marker {$marker}. Location:".ftell($archiveHandle));
            }
        }

        return $retVal;
    }

    private static function writeToFile($archiveHandle, $writeInfo)
    {
		$destFilePath = $writeInfo->getCurrentDestFilePath();

		if($writeInfo->currentFileHeader->fileSize > 0)
		{
			/* @var $writeInfo DupArchiveMiniWriteInfo */
			$parentDir = dirname($destFilePath);

			if (!file_exists($parentDir)) {

				$r = @mkdir($parentDir, 0755, true);

				if(!$r)
				{
					throw new Exception("Couldn't create {$parentDir}");
				}
			}

			$destFileHandle = fopen($destFilePath, 'wb+');

			if ($destFileHandle === false) {
				throw new Exception("Couldn't open {$destFilePath} for writing.");
			}

			do {

				self::appendGlobToFile($archiveHandle, $destFileHandle, $writeInfo);

				$currentFileOffset = ftell($destFileHandle);

				$moreGlobstoProcess = $currentFileOffset < $writeInfo->currentFileHeader->fileSize;
			} while ($moreGlobstoProcess);

			fclose($destFileHandle);

            @chmod($destFilePath, 0644);

			self::validateExpandedFile($writeInfo);
		} else {
			if(touch($destFilePath) === false) {
				throw new Exception("Couldn't create $destFilePath");
			}
            @chmod($destFilePath, 0644);
		}
    }

    private static function validateExpandedFile($writeInfo)
    {
        /* @var $writeInfo DupArchiveMiniWriteInfo */

        if ($writeInfo->currentFileHeader->hash !== '00000000000000000000000000000000') {
            
            $hash = hash_file('crc32b', $writeInfo->getCurrentDestFilePath());

            if ($hash !== $writeInfo->currentFileHeader->hash) {

                throw new Exception("MD5 validation fails for {$writeInfo->getCurrentDestFilePath()}");
            }
        }
    }

    // Assumption is that archive handle points to a glob header on this call
    private static function appendGlobToFile($archiveHandle, $destFileHandle, $writeInfo)
    {
        /* @var $writeInfo DupArchiveMiniWriteInfo */
        $globHeader = DupArchiveMiniGlobHeader::readFromArchive($archiveHandle, false);

        $globContents = fread($archiveHandle, $globHeader->storedSize);

        if ($globContents === false) {

            throw new Exception("Error reading glob from {$writeInfo->getDestFilePath()}");
        }

        if ($writeInfo->isCompressed) {
            $globContents = gzinflate($globContents);
        }

        if (fwrite($destFileHandle, $globContents) === false) {
            throw new Exception("Error writing data glob to {$destFileHandle}");
        }
    }

    private static function passesInclusionFilter($filter, $candidate)
    {
        return (substr($candidate, 0, strlen($filter)) == $filter);
    }
}
?>
