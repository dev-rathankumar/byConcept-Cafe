<?php
defined("ABSPATH") or die("");

abstract class DUP_PRO_Upload_Status
{
    const Pending   = 0;
    const Running   = 1;
    const Succeeded = 2;
    const Failed    = 3;
    const Cancelled = 4;

}

// Tracks the progress of the package with relation to a specific storage provider
// Used to track a specific upload as well as later report on its' progress
class DUP_PRO_Package_Upload_Info
{
    public $storage_id;
    public $archive_offset   = 0;		// Next byte of archive to copy
    public $copied_installer = false;   // Whether installer has been copied
    public $copied_archive   = false;	// Whether archive has been copied
    public $progress         = 0;		// 0-100 where this particular storage is at
    public $num_failures     = 0;		// How many times operation has failed and
    public $failed           = false;   // If catastrophic failure has been experienced or num_failures exceeded threshold
    public $cancelled        = false;
    public $upload_id        = null;
    public $failure_count    = 0;
    public $data             = '';     // Storage specific data
    public $data2            = '';     // Storage specific data

    /* Log related properties - these all SHOULD be public but since we need to json_encode them they have to be public. Ugh. */
    public $has_started            = false;
    public $status_message_details = '';  // Details about the storage run (succcess or falure)
    public $started_timestamp      = null;
    public $stopped_timestamp      = null;

    public function has_started()
    {
        return $this->has_started;
    }

    public function start()
    {
        $this->has_started       = true;
        $this->started_timestamp = time();
    }

    public function stop()
    {
        $this->stopped_timestamp = time();
    }

    public function set_stop_timestamp()
    {
        $this->stopped_timestamp = time();
    }

    public function get_started_timestamp()
    {
        return $this->started_timestamp;
    }

    public function get_stopped_timestamp()
    {
        return $this->stopped_timestamp;
    }

    public function get_status_text()
    {
        $status      = $this->get_status();
        $status_text = DUP_PRO_U::__('Unknown');

        if ($status == DUP_PRO_Upload_Status::Pending) {
            $status_text = DUP_PRO_U::__('Pending');
        } else if ($status == DUP_PRO_Upload_Status::Running) {
            $status_text = DUP_PRO_U::__('Running');
        } else if ($status == DUP_PRO_Upload_Status::Succeeded) {
            $status_text = DUP_PRO_U::__('Succeeded');
        } else if ($status == DUP_PRO_Upload_Status::Failed) {
            $status_text = DUP_PRO_U::__('Failed');
        } else if ($status == DUP_PRO_Upload_Status::Cancelled) {
            $status_text = DUP_PRO_U::__('Cancelled');
        }

        return $status_text;
    }

    public function get_status()
    {
        if ($this->cancelled) {
            $status = DUP_PRO_Upload_Status::Cancelled;
        } else if ($this->failed) {
            $status = DUP_PRO_Upload_Status::Failed;
        } else if ($this->has_started() === false) {
            $status = DUP_PRO_Upload_Status::Pending;
        } else if ($this->has_completed(true)) {
            $status = DUP_PRO_Upload_Status::Succeeded;
        } else {
            $status = DUP_PRO_Upload_Status::Running;
        }

        return $status;
    }

    public function set_status_message_details($status_message_details)
    {
        $this->status_message_details = $status_message_details;
    }

    // Set the message based on standard storage
    public function get_status_message()
    {
        /* @var $storage DUP_PRO_Storage_Entity */

        $storage = DUP_PRO_Storage_Entity::get_by_id($this->storage_id);

        $message = '';

        $status = $this->get_status();

        if ($storage != null) {
            if ($status == DUP_PRO_Upload_Status::Pending) {
                $message = $storage->get_pending_text();
            } else if ($status == DUP_PRO_Upload_Status::Failed) {
                $message = $storage->get_failed_text();
            } else if ($status == DUP_PRO_Upload_Status::Cancelled) {
                $message = $storage->get_cancelled_text();
            } else if ($status == DUP_PRO_Upload_Status::Succeeded) {
                $message = $storage->get_succeeded_text();
            } else {
                $message = $storage->get_action_text();
            }
        } else {
            $message = "Error. Unknown storage id {$this->storage_id}";
            DUP_PRO_LOG::trace($message);
        }

        $message_details = $this->status_message_details == '' ? '' : " ($this->status_message_details)";

        $message = "$message$message_details";

        return $message;
    }

    public function has_completed($count_only_success = false)
    {
        $retval = false;

        if ($count_only_success) {
            $retval = (($this->failed == false) && ($this->cancelled == false) && ($this->copied_installer && $this->copied_archive));
        } else {
            $retval = $this->failed || ($this->copied_installer && $this->copied_archive) || $this->cancelled;
        }

        if ($retval && ($this->stopped_timestamp == null)) {
            // Having to set stopped this way because we aren't OO and allow everyone to set failed/other flags so impossible to know exactly when its done
            $this->stop();
        }

        return $retval;
    }
    /*
     * Public Methods
     */

    public function increase_failure_count()
    {
        $global = DUP_PRO_Global_Entity::get_instance();

        $this->failure_count++;

        DUP_PRO_LOG::trace("failure count increasing for storage id $this->storage_id to $this->failure_count");

        if ($this->failure_count > $global->max_storage_retries) {
            DUP_PRO_LOG::trace("storage $this->storage_id moving to failed");
            $this->failed = true;
        }
    }
}