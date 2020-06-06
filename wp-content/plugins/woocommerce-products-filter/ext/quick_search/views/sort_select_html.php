<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

   <span class='woof_qt_sort_wraper'>
       <select class='woof_qt_sort_select'>
           <?php foreach($sort as $key=>$title):?>
           <option value="<?php echo $key ?>"><?php echo $title ?></option> 
           <?php endforeach; ?>
        </select>
   </span>