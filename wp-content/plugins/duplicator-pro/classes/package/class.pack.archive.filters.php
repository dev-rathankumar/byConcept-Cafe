<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Defines the scope from which a filter item was created/retrieved from
 * @package DupicatorPro\classes
 */
class DUP_PRO_Archive_Filter_Scope_Base
{

    /**
     * @var array All internal storage items that we decide to filter
     */
    public $Core             = array();
    //TODO: Enable with Settings UI

    /**
     * @var array Global filter items added from settings
     */
    public $Global           = array();

    /**
     * @var array Items when creating a package or template
     */
    public $Instance         = array();

    /**
     * @var array Items that are not readable
     */
    public $Unreadable       = array();

    /**
     * @var int Number of unreadable items
     */
    private $unreadableCount = 0;

    /**
     * @param string $item A path to an unreadable item
     */
    public function addUnreadableItem($item)
    {
        $this->unreadableCount++;
        if ($this->unreadableCount <= DUPLICATOR_PRO_SCAN_MAX_UNREADABLE_COUNT) {
            $this->Unreadable[] = $item;
        }
    }

    /**
     * @return int returns number of unreadable items
     */
    public function getUnreadableCount()
    {
        return $this->unreadableCount;
    }
}

/**
 * Defines the scope from which a filter item was created/retrieved from
 * @package DupicatorPro\classes
 */
class DUP_PRO_Archive_Filter_Scope_Directory extends DUP_PRO_Archive_Filter_Scope_Base
{

    /**
     * @var array Items that are not readable
     */
    public $Warning    = array();

    /**
     * @var array Directories containing other WordPress installs
     */
    public $AddonSites = array();

    /**
     * @var array Items that are too large
     */
    public $Size       = array();
}

/**
 * Defines the scope from which a filter item was created/retrieved from
 * @package DupicatorPro\classes
 */
class DUP_PRO_Archive_Filter_Scope_File extends DUP_PRO_Archive_Filter_Scope_Base
{

    /**
     * @var array Items that are not readable
     */
    public $Warning = array();

    /**
     * @var array Items that are too large
     */
    public $Size    = array();
}

/**
 * Defines the filtered items that are pulled from there various scopes
 * @package DupicatorPro\classes
 */
class DUP_PRO_Archive_Filter_Info
{

    /**
     * Contains all folder filter info
     * @var DUP_PRO_Archive_Filter_Scope_Directory 
     */
    public $Dirs  = null;

    /**
     * Contains all folder filter info
     * @var DUP_PRO_Archive_Filter_Scope_File 
     */
    public $Files = null;

    /**
     * Contains all folder filter info
     * @var DUP_PRO_Archive_Filter_Scope_Base 
     */
    public $Exts  = null;

    /**
     * tree size structure for client jstree
     * @var DUP_PRO_Tree_files 
     */
    public $TreeSize = null;

    /**
     * tree char warnings structure for client jstree
     * @var DUP_PRO_Tree_files 
     */
    public $TreeWarning = null;

    public function __construct()
    {
        $this->reset(true);
    }
    
    public function __clone()
    {
        DUP_PRO_LOG::trace("CLONE ".__CLASS__);
        
        if (is_object($this->Dirs)) {
            $this->Dirs = clone $this->Dirs;
        }
        if (is_object($this->Files)) {
            $this->Files = clone $this->Files;
        }
        if (is_object($this->Exts)) {
            $this->Exts = clone $this->Exts;
        }
        if (is_object($this->TreeSize)) {
            $this->TreeSize = clone $this->TreeSize;
        }
        if (is_object($this->TreeSize)) {
            $this->TreeWarning = clone $this->TreeWarning;
        }
    }

    /**
     * reset and clean all object
     */
    public function reset($initTreeObjs = false)
    {
        $exclude = array("Unreadable", "Instance");
        if (is_null($this->Dirs)) {
            $this->Dirs = new DUP_PRO_Archive_Filter_Scope_Directory();
        } else {
            $this->resetMember($this->Dirs, $exclude);
        }

        if (is_null($this->Files)) {
            $this->Files = new DUP_PRO_Archive_Filter_Scope_File();
        } else {
            $this->resetMember($this->Files, $exclude);
        }

        $this->Exts  = new DUP_PRO_Archive_Filter_Scope_Base();

        if ($initTreeObjs) {
            $this->TreeSize    = new DUP_PRO_Tree_files(ABSPATH, false);
            $this->TreeWarning = new DUP_PRO_Tree_files(ABSPATH, false);
        } else {
            $this->TreeSize    = null;
            $this->TreeWarning = null;
        }
    }

    /**
     * Resets all properties of $member to their default values except the ones in $exclude
     *
     * @param object $member
     * @param array $exclude Properties to exclude from resetting
     * @throws ReflectionException
     */
    private function resetMember($member, $exclude = array())
    {
        $refClass = new ReflectionClass($member);
        $defaults = $refClass->getDefaultProperties();

        foreach ($member as $key => $value) {
            if (!in_array($key, $exclude)) {
                if (isset($defaults[$key])) {
                    $member->$key = $defaults[$key];
                } else {
                    $member->$key = null;
                }
            }
        }
    }
}