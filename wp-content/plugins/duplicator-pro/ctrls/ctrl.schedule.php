<?php
defined("ABSPATH") or die("");
if (!class_exists('DUP_PRO_CTRL_Schedule')):
class DUP_PRO_CTRL_Schedule extends DUP_PRO_Web_Services
{
    public function __construct()
    {
        /* Schedule Options */
        $this->add_class_action('wp_ajax_duplicator_pro_schedule_bulk_delete', 'duplicator_pro_schedule_bulk_delete');
        $this->add_class_action('wp_ajax_duplicator_pro_get_schedule_infos', 'get_schedule_infos');
        $this->add_class_action('wp_ajax_duplicator_pro_run_schedule_now', 'run_schedule_now');
    }

    function duplicator_pro_schedule_bulk_delete()
    {
        DUP_PRO_Handler::init_error_handler();
        check_ajax_referer('duplicator_pro_schedule_bulk_delete', 'nonce');
        DUP_PRO_U::hasCapability('export');
        try {
            $json = array();
            $post = stripslashes_deep($_POST);

            $postIDs  = isset($post['duplicator_pro_delid']) ? $post['duplicator_pro_delid'] : null;
            $list     = explode(",", $postIDs);
            $delCount = 0;

            if ($postIDs != null) {
                foreach ($list as $id) {
                    $schedule = DUP_PRO_Schedule_Entity::delete_by_id($id);
                    if( $schedule ) {
                        $delCount++;
                    }
                }
            }


        } catch (Exception $e) {
            $json['error'] = "{$e}";
            die(json_encode($json));
        }

        $json['ids']     = "{$postIDs}";
        $json['removed'] = $delCount;
        exit(json_encode($json));
    }

    // return schedule status'
    // { schedule_id, is_running=true|false, last_ran_string}
    function get_schedule_infos()
    {
        DUP_PRO_Handler::init_error_handler();
        check_ajax_referer('duplicator_pro_get_schedule_infos', 'nonce');
        DUP_PRO_U::hasCapability('export');
        $schedules      = DUP_PRO_Schedule_Entity::get_all();
        $schedule_infos = array();

        if (count($schedules) > 0) {
            $package = DUP_PRO_Package::get_next_active_package();

            foreach ($schedules as $schedule) {
                /* @var $schedule DUP_PRO_Schedule_Entity */
                $schedule_info = new stdClass();

                $schedule_info->schedule_id     = $schedule->id;
                $schedule_info->last_ran_string = $schedule->get_last_ran_string();

                if ($package != null) {
                    $schedule_info->is_running = ($package->schedule_id == $schedule->id);
                } else {
                    $schedule_info->is_running = false;
                }

                array_push($schedule_infos, $schedule_info);
            }
        }

        $json_response = json_encode($schedule_infos);
        die($json_response);
    }

    function run_schedule_now()
    {
        DUP_PRO_Handler::init_error_handler();
        check_ajax_referer('duplicator_pro_run_schedule_now', 'nonce');
        DUP_PRO_U::hasCapability('export');
        
        DUP_PRO_LOG::trace("enter");
        $schedule_id = (int) $_POST['schedule_id'];
        $schedule    = DUP_PRO_Schedule_Entity::get_by_id($schedule_id);

        if ($schedule != null) {
            DUP_PRO_LOG::trace("Inserting new package for schedule $schedule->name due to manual request");
            // Just inserting it is enough since init() will automatically pick it up and schedule a cron in the near future.
            $schedule->insert_new_package(true);
            DUP_PRO_Package_Runner::kick_off_worker();
            $response['status'] = 0;
        } else {
            $message            = DUP_PRO_U::esc_html__("Attempted to queue up a job for non existent schedule $schedule_id");
            DUP_PRO_LOG::trace($message);
            $response['status'] = -1;
        }
        $json_response = json_encode($response);
        die($json_response);
    }
}
endif;