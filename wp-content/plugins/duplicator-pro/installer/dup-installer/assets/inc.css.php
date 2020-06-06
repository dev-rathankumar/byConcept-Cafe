<?php defined("DUPXABSPATH") or die(""); ?>
<style>
    /*******
    HELPER CALSSES
    *******/

    .no-display { 
        display: none; 
    }

    .transparent {
        opacity: 0;
    }

    .display-inline {
        display: inline;
    }

    .display-inline-block {
        display: inline-block;
    }

    .display-block {
        display: block;
    } 

    .auto-updatable button.postfix {
        min-width: 80px;
    }

    .auto-updatable.autoupdate-enabled button.postfix {
        background-color: #13659C;
        color: #fff;
    }

    body {font-family:Verdana,Arial,sans-serif; font-size:13px}
    fieldset {border:1px solid silver; border-radius:5px; padding:10px}
    h3 {margin:1px; padding:1px; font-size:13px;}
    a {color:#222}
    a:hover{color:gray}

    .margin-top {
        margin-top: 20px;
    }

    input:not([type=checkbox]):not([type=radio]):not([type=button]) , select {
        min-width: 0;
        width: 100%;
        border-radius: 2px;
        border: 1px solid silver;
        padding: 4px;
        padding-left: 4px;
        font-family: Verdana,Arial,sans-serif;
        line-height: 20px;
        height: 30px;
        box-sizing: border-box;
        background-color: white;
        color: black;
        border-radius: 4px;
    }

    input:not([type=checkbox]):not([type=radio]):not([type=button]).w30 , select.w30 {
        width: 30%;
    }

    input:not([type=checkbox]):not([type=radio]):not([type=button]).w50 , select.w50 {
        width: 50%;
    }

    input:not([type=checkbox]):not([type=radio]):not([type=button]).w95 , select.w95 {
        width: 95%;
    }

    input[readonly]:not([type=checkbox]):not([type=radio]):not([type=button]) {
        background-color:#efefef;
        cursor: not-allowed;
    }

    select[size]:not([size="1"]) {
        height: auto;
        line-height: 25px;
    }

    select , option {
        color: black;
    }

    select option {
        padding: 5px;
    }

    input:not([type=checkbox]):not([type=radio]):not([type=button]):disabled,
    select:disabled,
    select option:disabled,
    select:disabled option, 
    select:disabled option:focus,
    select:disabled option:active,
    select:disabled option:checked {
        background: #EBEBE4;
        text-decoration: line-through;
        color: #ccc;
        cursor: not-allowed;
    }

    button.no-layout {
        background: none;
        border: none;
    }

    .input-postfix-btn-group {
        display: flex;
        border: 1px solid darkgray;
        border-radius: 4px;
        overflow: hidden;
    }

    .input-postfix-btn-group input:not([type=checkbox]):not([type=radio]):not([type=button]) {
        flex: 1 1 auto;
        border-radius: 0;
        border: 0 none;
        border-right: 1px solid darkgray;
        height: 28px;
    }

    .input-postfix-btn-group .prefix,
    .input-postfix-btn-group .postfix {
        flex: none;
        min-width: 60px;
        box-sizing: border-box;
        padding: 0 10px;
        margin: 0;
        border: 0 none;
        background-color:#CDCDCD;
        line-height: 28px;
    }

    .param-wrapper.small .input-postfix-btn-group .prefix,
    .param-wrapper.small .input-postfix-btn-group .postfix {
        min-width: 0;
    }

    .input-postfix-btn-group button {
        cursor: pointer;
    }

    .input-postfix-btn-group button:hover {
        border: 0 none;
        background-color: #13659C;
        color: white;
    }


    .param-wrapper span .checkbox-switch {
        top: 2px;
    }

    .wpinconf-check-wrapper {
        flex: none;
        width: 100px;
    }

    /* ============================
    COMMON VIEWS
     ============================ */
    body,
    div#content,
    form.content-form {
        line-height: 1.5;
    }

    /*Lets revisit this later.  Right now anything over 900px gives the overall feel of an elongated flow and the
    inputs look too spread out.	If we can iron out some of those issues with multi-columns and the notices view better
    then we can try and work more towards a full fluid layout*/
    #content {
        border:1px solid #CDCDCD; 
        margin: 20px auto; 
        border-radius:4px; 
        box-shadow:0 8px 6px -6px #333;
        font-size:13px;
        width: 850px;
        max-width: calc(100vw - 40px);
    }

    .debug-params div#content {
        margin: 20px; 
    }

    #content-inner {
        min-height: 500px;
        margin: 20px;
        position: relative;
        padding-bottom: 80px;
        box-sizing: border-box;
    }

    #content-loader-wait {        
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
    }

    #body-step4 #content-inner {
        padding-bottom: 0;
    }

    form.content-form {
    }

    div.logfile-link {float:right; font-weight:normal; font-size:11px; font-style:italic}

    /* Header */
    table.header-wizard {border-top-left-radius:5px; border-top-right-radius:5px; width:100%; box-shadow:0 5px 3px -3px #999; background-color:#F1F1F1; font-weight:bold}
    .wiz-dupx-version {white-space:nowrap; color:#777; font-size:11px; font-style:italic; text-align:right;  padding:5px 15px 5px 0; line-height:14px; font-weight:normal}
    .wiz-dupx-version a { color:#999; }
    div.dupx-debug-hdr {padding:5px 0 5px 0; font-size:16px; font-weight:bold}
    div.dupx-branding-header {font-size:26px; padding: 10px 0 7px 15px;}

    .dupx-overwrite {color:black;}
    div.dupx-modes span.mode_standard {color:black}
    div.dupx-modes span.mode_overwrite {color:maroon}
    div.dupx-modes span.mode_restore_bk {color:maroon}

    .dupx-pass {display:inline-block; color:green;}
    .dupx-fail {display:inline-block; color:#AF0000;}
    .dupx-warn {display:inline-block; color:#555;}
    .dupx-notice {display:inline-block; color:#000;}
    i[data-tooltip].fa-question-circle {cursor: pointer; color:#C3C3C3}

    .status-badge {
        float:right; 
        border-radius:4px; 
        color:#fff; 
        padding:0 3px 0 3px;  
        font-size:11px; 
        min-width:30px; 
        text-align:center; 
        font-weight:normal;
    }
    .status-badge.pass,
    .status-badge.good,
    .status-badge.success {
        background-color:#418446
    }
    .status-badge.pass::after {
        content: "Pass"
    }
    .status-badge.good::after {
        content: "Good"
    }
    .status-badge.success::after {
        content: "Success"
    }
    .status-badge.fail {
        background-color:maroon;
    }
    .status-badge.fail::after {
        content: "Fail"
    }
    .status-badge.warn {
        background-color:#555;
    }
    .status-badge.warn::after {
        content: "Warn"
    }

    #validate-area .status-badge {
        margin: 5px 5px 0 0;
    }


    button.default-btn, input.default-btn {
        cursor:pointer; color:#fff; font-size:16px; border-radius:5px;	padding:7px 25px 5px 25px;
        background-color:#13659C; border:1px solid gray;
    }
    button.disabled, input.disabled {background-color:#F4F4F4; color:silver; border:1px solid silver;}

    .log-ui-error {padding-top:2px; font-size:13px}
    #progress-area {padding:5px; margin:150px 0 0 0; text-align:center;}
    .progress-text {font-size:1.7em; margin-bottom:20px}
    #secondary-progress-text { font-size:.85em; margin-bottom:20px }
    #progress-notice:not(:empty) { color:maroon; font-size:.85em; margin-bottom:20px; }

    #ajaxerr-data {
        min-height: 300px;
    }

    #ajaxerr-data .pre-content,
    #ajaxerr-data .html-content {
        padding:6px; 
        box-sizing: border-box;
        width:100%; 
        border:1px solid silver; 
        border-radius:5px; 
        background-color:#F1F1F1; 
        font-size:11px; 
        overflow-y:scroll; 
        line-height:20px
    }

    #ajaxerr-data .pre-content {
        height:300px;
    }

    #header-main-wrapper {
        position: relative;
    }

    #header-main-wrapper .dupx-modes {
        color:#999; 
        font-weight:normal; 
        font-style:italic; 
        font-size:11px; 
        text-align:right;
        position: absolute;
        top: 0;
        right: 0;
    }

    #header-main-wrapper .dupx-logfile-link {
        font-weight:normal; 
        font-style:italic; 
        font-size:11px;
        position: absolute;
        bottom: 2px;
        right: 0;
    }


    .hdr-main {
        font-size:22px; 
        padding:0 0 5px 0; 
        border-bottom:1px solid #D3D3D3; 
        font-weight:bold; 
        margin: 0 0 20px 0;
    }

    div.sub-header {font-size:11px; font-style:italic; font-weight:normal}
    .hdr-main .step { color:#DB4B38  }
    .hdr-sub1 {
        font-size:18px; 
        border:1px solid #D3D3D3;
        padding: 4px 7px;
        background-color:#f9f9f9; 
        font-weight:bold; 
        border-radius:4px 4px 0 0;
    }

    .hdr-sub1.open {
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .hdr-sub1 a {cursor:pointer; text-decoration: none !important}
    .hdr-sub1 i.fa {
        font-size:15px; 
        display:inline-block; 
        margin-right:5px; 
        position: relative;
        bottom: 1px;
    }

    .hdr-sub1 .status-badge {
        margin-top: 4px;
    }

    .hdr-sub1-area {
        border:1px solid #D3D3D3;
        border-top: 0 none;
        border-radius: 0 0 4px 4px;
        padding: 20px;
        margin-bottom: 20px;
        position: relative;
    }

    .hdr-sub1-area.tabs-area {
        padding: 0;
    }

    .hdr-sub1-area.tabs-area .ui-tabs-nav {
        padding: 0 20px;
        border-radius: 0;
        border: 0 none;
        background: #E0E0E0;
    }

    .hdr-sub1-area.tabs-area .ui-tabs {
        margin: 0;
        padding: 0;
        border: 0 none;
    }

    .hdr-sub1-area.tabs-area .ui-tabs-tab {
        margin: 3px 5px 0 0;
    }

    .hdr-sub1-area.tabs-area .ui-tabs-panel {
        position: relative;
        padding: 20px;
    }

    .hdr-sub2 {font-size:15px; padding:2px 2px 2px 0; font-weight:bold; margin-bottom:5px; border:none}
    .hdr-sub3 {font-size:15px; padding:2px 2px 2px 0; border-bottom:1px solid #D3D3D3; font-weight:bold; margin-bottom:5px;}
    .hdr-sub4 {font-size:15px; padding:7px; border:1px solid #D3D3D3;; font-weight:bold; background-color:#e9e9e9;}
    .hdr-sub4:hover  {background-color:#dfdfdf; cursor:pointer}
    .toggle-hdr:hover {cursor:pointer; background-color:#f1f1f1; border:1px solid #dcdcdc; }
    .toggle-hdr:hover a{color:#000}



    [data-type="toggle"] > i.fa,
    i.fa.fa-toggle-empty { min-width: 8px; }

    /* ============================
    NOTICES
    ============================ */
    /* step messages */
    #page-top-messages { 
        padding: 0 20px; 
    }

    .notice {
        background: #fff;
        border:1px solid #dfdfdf;
        border-left: 4px solid #fff;
        margin: 5px 0;
        padding: 5px;
        border-radius: 4px;
        font-size: 12px;
    }

    .section .notice:first-child {
        margin-top: 0;
    }

    .section .notice:last-child {
        margin-bottom: 0;
    }

    .notice.next-step {
        margin: 20px 0;
        padding: 10px;
    }

    .notice-report {
        border-left: 4px solid #fff;
        padding-left: 0;
        padding-right: 0;
        margin-bottom: 4px;
    }

    .next-step .title-separator {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid lightgray;
    }

    .notice .info pre {
        margin: 0;
        padding: 0 0 10px 0;
        overflow: auto;
    }

    .notice-report .title {
        padding: 0 10px;
    }

    .notice-report .info {
        border-top: 1px solid #dedede;
        padding: 10px;
        font-size: 10px;
        background: #FAFAFA;
    }

    .notice.l-info,
    .notice.l-notice {
        border-left-color: #197b19;
    }
    .notice.l-swarning {
        border-left-color: #636363;
    }
    .notice.l-hwarning {
        border-left-color: #636363;
    }
    .notice.l-critical {
        border-left-color: maroon;
    }
    .notice.l-fatal {
        border-left-color: #000000;
    }

    .notice.next-step {
        position: relative;
    }

    .notice.next-step.l-info,
    .notice.next-step.l-notice {
        border-color: #197b19;
    }
    .notice.next-step.l-swarning {
        border-color: #636363;
    }
    .notice.next-step.l-hwarning {
        border-color: #636363;
    }
    .notice.next-step.l-critical {
        border-color: maroon;
    }
    .notice.next-step.l-fatal {
        border-color: #000000;
    }

    .notice.next-step > .title {
        padding-left: 30px;
    }

    .notice.next-step > .fas {
        display: block;
        position: absolute;
        height: 20px;
        width: 20px;
        line-height: 20px;
        text-align: center;
        color: white;
        border-radius: 4px;
    }

    .notice.next-step.l-info > .fas,
    .notice.next-step.l-notice > .fas {
        background-color: #197b19;
    }
    .notice.next-step.l-swarning > .fas {
        background-color: #636363;
    }
    .notice.next-step.l-hwarning > .fas {
        background-color: #636363;
    }
    .notice.next-step.l-critical > .fas {
        background-color: maroon;
    }
    .notice.next-step.l-fatal > .fas{
        background-color: #000000;
    }

    .report-sections-list .section {
        border: 1px solid #DFDFDF;
        margin-bottom: 25px;
        box-shadow: 4px 8px 11px -8px rgba(0,0,0,0.41);
    }

    .report-sections-list .section > .section-title {
        background-color: #efefef;
        padding: 3px;
        font-weight: bold;
        text-align: center;
        font-size: 14px;
    }

    .report-sections-list .section > .section-content {
        padding: 5px;
    }

    .notice-level-status {
        border-radius: 4px;
        padding: 2px;
        margin: 1px;
        font-size: 10px;
        display: inline-block;
        color: #FFF;
        font-weight: bold;
        min-width:55px;
    }

    .notice-level-status.l-info,
    .notice-level-status.l-notice {background: #197b19;}
    .notice-level-status.l-swarning {background: #636363;}
    .notice-level-status.l-hwarning {background: #636363;}
    .notice-level-status.l-critical {background: maroon;}
    .notice-level-status.l-fatal {background: #000000;}

    /*Adv Opts */
    .dupx-opts .param-wrapper {
        padding: 5px 0;
    }
    .dupx-opts .param-wrapper .param-wrapper {
        padding: 0;
    }

    .param-wrapper > .container {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-items: center;
        min-height: 30px;
    }

    .param-wrapper > .container > .main-label {
        flex: none;
        width: 200px;
        font-weight: bold;
        line-height: 1.5;
        box-sizing: border-box;
        padding-right: 5px;
    }

    .param-wrapper.has-main-label > .sub-note {
        margin-left: 200px;
    }

    #tabs-wp-config-file .param-wrapper > .container > .main-label {
        width: 300px;
    }

    #tabs-wp-config-file .param-wrapper.has-main-label > .sub-note {
        margin-left: 300px;
    }

    .param-wrapper > .container .input-container {
        flex: 1 1 auto;
    }

    .param-wrapper.small > .container .input-container {
        max-width: 100px;
    }

    .param-wrapper.medium > .container .input-container {
        max-width: 300px;
    }

    .param-wrapper.large > .container .input-container {
        max-width: 500px;
    }

    .param-wrapper.full > .container .input-container {
        max-width: none;
    }


    /*
    .dupx-opts > .param-wrapper:nth-child(2n+1) {
        background-color: #EAEAEA;
    }

    .dupx-opts > .param-wrapper:nth-child(2n) {
        background-color: #F6F6F6;
    }*/

    .param-form-type-radio .option-group {
        display: inline-block;
        min-width: 140px;
    }

    .param-form-type-radio.group-block .option-group {
        display: block;
        line-height: 30px;
    }

    .param-wrapper .sub-note {
        display: block;
        font-size: 10px;
        margin-top: 8px;
    }

    table.dupx-opts {width:100%; border:0px;}
    table.dupx-opts td{padding:3px;}
    table.dupx-opts td:first-child{width:125px; font-weight: bold}
    table.dupx-advopts td:first-child{width:125px;}
    table.dupx-advopts label.radio {width:50px; display:inline-block}
    table.dupx-advopts label {white-space:nowrap; cursor:pointer}
    table.dupx-advopts-space {line-height:24px}
    table.dupx-advopts tr {vertical-align:top}

    div.error-pane {border:1px solid #efefef; border-left:4px solid #D54E21; padding:0 0 0 10px; margin:2px 0 10px 0}
    div.dupx-ui-error {padding-top:2px; font-size:13px; line-height: 20px}

    .footer-buttons {
        display: flex;
        position: absolute;
        bottom: 0;
        width: 100%;
    }

    .footer-buttons .content-left {
        flex: 1;
    }


    .footer-buttons  input:hover, button:hover {border:1px solid #000}
    .footer-buttons input[disabled=disabled], button[disabled=disabled]{background-color:#F4F4F4; color:silver; border:1px solid silver;}
    form#form-debug {display:block; margin:10px auto; width:750px;}
    form#form-debug a {display:inline-block;}
    form#form-debug pre {margin-top:-2px; display:none}
    small.info {font-style:italic}

    /*Dialog Info */
    div.dlg-serv-info {line-height:22px; font-size:12px}
    div.dlg-serv-info label {display:inline-block; width:200px; font-weight: bold}
    div.dlg-serv-info div.hdr {font-weight: bold; margin-top:5px; padding:2px 5px 2px 0; border-bottom: 1px solid #777; font-size:14px}

    /* ============================
    UI TABS OVERWRITE
     ============================ */

    .ui-tabs .ui-tabs-nav .ui-tabs-anchor {
        display: inline-block;
        width: 100%;
        box-sizing: border-box;
        text-align: center;
    }

    .ui-tabs .ui-tabs-nav li {
        min-width: 150px;
    }

    /* ============================
    INIT 1:SECURE PASSWORD
    ============================ */
    button.pass-toggle {height:26px; width:26px; position:absolute; top:0px; right:0px; border:1px solid silver;  border-radius:0 4px 4px 0;padding:2px 0 0 3px;}
    button.pass-toggle  i { padding:0; display:block; margin:-4px 0 0 -5px}
    div.i1-pass-area {
        width:100%;
        text-align:center;
        max-width: 500px;
        margin: auto;
        position: relative;
    }
    div.i1-pass-data table {width:100%; border-collapse:collapse; padding:0}
    div.i1-pass-data label {
        display: block;
        margin-bottom: 10px;
        font-weight:bold;
    }
    div.i1-pass-errmsg {color:maroon; font-weight:bold}
    div#i1-pass-input {position:relative; margin:2px 0 15px 0}
    input#secure-pass {border-radius:4px 0 0 4px; width:250px}

    /* ============================
    STEP 1 VIEW
     ============================ */
    #s1-area-setup-type label {cursor:pointer}
    .s1-setup-type-sub {padding:5px 0 0 25px; display:none}
    #s1-area-archive-file .ui-widget.ui-widget-content {border: 0px solid #d3d3d3}
    table.s1-archive-local {width:100%}
    table.s1-archive-local td {padding:4px 4px 4px 4px}
    table.s1-archive-local td:first-child {font-weight:bold; width:55px}
    div.s1-err-msg {padding:0 0 80px 0; line-height:20px}
    div.s1-err-msg i {color:maroon}

    div#s1-multisite p.note {font-size:10px; font-style:italic; text-align:center; color:#777; margin:30px 0 0 0}

    #validate-area div.info-top {text-align:center; font-style:italic; font-size:11px; padding:0 5px 5px 5px}
    table.s1-checks-area {width:100%; margin:0; padding:0}
    table.s1-checks-area td.title {font-size:16px; width:100%}
    table.s1-checks-area td.title small {font-size:11px; font-weight:normal}
    table.s1-checks-area td.toggle {font-size:11px; margin-right:7px; font-weight:normal}


    div.s1-reqs {background-color:#efefef; border:1px solid silver; border-radius:4px; padding-bottom:4px}
    div.s1-reqs div.header {background-color:#E0E0E0; color:#000;  border-bottom: 1px solid silver; padding:2px; font-weight:bold }
    div.s1-reqs div.status {float:right; border-radius:4px; color:#fff; padding:0 3px 0 3px; margin:4px 5px 0 0; font-size:11px; min-width:30px; text-align:center;}
    div.s1-reqs div.pass {background-color:green;}
    div.s1-reqs div.fail {background-color:#636363;}
    div.s1-reqs div.title {
        padding:3px 3px 3px 5px; 
        font-size:13px;
        line-height: 20px;
    }
    div.s1-reqs div.title:hover {background-color:#dfdfdf; cursor:pointer}
    div.s1-reqs div.info {padding:8px 8px 20px 8px; background-color:#fff; display:none; line-height:18px; font-size: 12px}
    div.s1-reqs div.info a {color:#485AA3;}
    select#archive_engine {width:90%; cursor:pointer}
    div#wrapper_item_accept-warnings {margin-left:30px}

    /*Terms and Notices*/
    div.s1-accept-check label{cursor:pointer;}
    div#s1-warning-msg {padding:5px;font-size:12px; color:#333; line-height:14px;font-style:italic; overflow-y:scroll; height:460px; border:1px solid #dfdfdf; background:#fff; border-radius:3px}
    div.s1-accept-check {padding:3px; font-size:14px; font-weight:normal;}
    input#accept-warnings, input#accept-perm-error {height: 17px; width:17px}
    div#wrapper_item_accept-warnings {margin-left:30px}

    #tabs-other .param-wrapper .sub-note {
        margin-bottom: 10px;
    }

    /* ============================
    STEP 2 VIEW
    ============================ */
    div.s2-opts label {cursor:pointer}
    textarea#debug-dbtest-json {width:98%; height:200px}
    div.php-chuncking-warning {font-style:italic; font-size:11px; color:maroon; white-space:normal; line-height:16px; padding-left:20px}

    /*Toggle Buttons */
    div.s2-btngrp {text-align:center; margin:0 auto 10px auto}
    div.s2-btngrp input[type=button] {font-size:14px; padding:6px; width:120px; border:1px solid silver;  cursor:pointer}
    div.s2-btngrp input[type=button]:first-child {border-radius:5px 0 0 5px; margin-right:-2px}
    div.s2-btngrp input[type=button]:last-child {border-radius:0 5px 5px 0; margin-left:-4px}
    div.s2-btngrp input[type=button].active {background-color:#13659C; color:#fff;}
    div.s2-btngrp input[type=button].in-active {background-color:#E4E4E4; }
    div.s2-btngrp input[type=button]:hover {border:1px solid #999}

    /*Basic DB */
    select#dbname-select {width:100%; border-radius:3px; height:20px; font-size:12px; border:1px solid silver;}
    div#s2-dbrefresh-basic {float:right; font-size:12px; display:none;  font-weight:bold; margin:5px 5px 1px 0}
    div#s2-dbrefresh-cpnl {float:right; font-size:12px; display:none; font-weight:bold; margin:5px 5px 1px 0}
    div#s2-db-basic-overwrite {border: 1px solid silver; margin:0 0 20px 0; padding:10px; background:#f9f9f9; border-radius:5px}
    div#s2-db-basic-overwrite div.warn-text {font-size:12px; padding:5px 0 5px 0; color:maroon}
    div#s2-db-basic-overwrite div.btn-area {text-align: right; margin:5px 0}
    input.overwrite-btn {
        cursor:pointer; color:#fff; font-size:13px; border-radius:5px;	padding:5px 20px 4px 20px;
        background-color:#989898; border:1px solid #777;
    }

    /*cPanel DB */
    .s2-cpnl-pane {margin-top:5px}
    div.s2-gopro {color: black; margin-top:10px; padding:0 20px 10px 20px; border: 1px solid silver; background-color:#F6F6F6; border-radius: 4px}
    div.s2-gopro h2 {text-align: center; margin:10px}
    div.s2-gopro small {font-style: italic}
    .s2-cpnl-panel-no-support {text-align:center; font-size:18px; font-weight:bold; line-height:30px; margin-top:40px}
    td#cpnl-prefix-dbname {width:10px}
    td#cpnl-prefix-dbuser {width:10px; white-space:normal}
    div#s2-cpnl-area div#cpnl-host-warn {white-space:normal; font-size:11px; display:none; font-style: italic}
    a#s2-cpnl-status-msg {font-size:11px}
    span#s2-cpnl-status-icon {display:none}
    div#s2-cpnl-connect {margin:auto; text-align:center; margin:10px 0 0 0}
    div#s2-cpnl-status-details {
        border: 1px solid #AF0000;
        border-radius: 3px;
        background-color: #f9f9f9;
        padding: 20px;
        margin-top: 20px;
    }
    div#cpnl-dbname-prefix {display:none; float:left; margin-top:3px;}
    span#s2-cpnl-db-opts-lbl {font-size:11px; font-weight:normal; font-style:italic}
    div#s2-cpnl-dbname-area2 table {border-collapse: collapse; width: 100%}
    div#s2-cpnl-dbname-area2 table td {padding:0 !important; margin:0; border:0}
    div#s2-cpnl-dbname-area2 table td:first-child {vertical-align:bottom;}
    div#s2-cpnl-dbname-area2 table td:nth-child(2) {width:100%; padding-right:0 !important}
    div#s2-cpnl-dbuser-area2 table {border-collapse: collapse; width: 100%}
    div#s2-cpnl-dbuser-area2 table td {padding:0 !important; margin:0; border:0}
    div#s2-cpnl-dbuser-area2 table td:first-child {vertical-align:bottom;}
    div#s2-cpnl-dbuser-area2 table td:nth-child(2) {width:100%; padding-right:0 !important}

    /*DATABASE CHECKS */
    .s2-dbtest-area {
        min-height:110px
    }
    .s2-dbtest-area input[type=button] {font-size:11px; height:20px; border:1px solid gray; border-radius:3px; cursor:pointer}
    .s2-dbtest-area small.db-check {color:#000; text-align:center; padding:3px; font-size:11px; font-weight:normal }
    .s2-dbtest-area div.message {padding:10px 10px 10px 10px; margin:5px auto 5px auto; text-align:center; font-style:italic; font-size:15px; line-height:22px; width:100%;}
    .s2-dbtest-area div.sub-message {padding:5px; text-align:center; font-style:italic; color:maroon}
    .s2-dbtest-area div.error-msg {color:maroon}
    .s2-dbtest-area div.success-msg {color:green}
    .s2-dbtest-area pre {font-family:Verdana,Arial,sans-serif; font-size:13px; margin:0; white-space:normal;}

    div.s2-reqs-hdr {border-radius:0; border-top-right-radius:6px; border-top-left-radius:6px; border-bottom:none}
    div.s2-notices-hdr {border-radius:0; border-bottom:1px solid #D3D3D3; }
    div#s2-reqs-all {display:none}
    div#s2-notices-all {display:none}

    div.s2-reqs {background-color:#efefef; border:1px solid #D3D3D3; border-top:none}
    div.s2-reqs div.status {
        margin:4px 7px 0 0;
    }
    div.s2-reqs div.title {padding:3px 8px 3px 20px; font-size:13px; background-color:#f1f1f1; border-top: 1px solid #D3D3D3;}
    div.s2-reqs div.title:hover {background-color:#dfdfdf; cursor:pointer}
    div.s2-reqs div.info {padding:4px 12px 15px 12px;; background-color:#fff; display:none; line-height:18px; font-size: 12px}
    div.s2-reqs div.info a {color:#485AA3;}
    div.s2-reqs div.info ul {padding-left:25px}
    div.s2-reqs div.info ul li {padding:2px}
    div.s2-reqs div.info ul.vids {list-style-type: none;}
    div.s2-reqs div.sub-title{border-bottom: 1px solid #d3d3d3; font-weight:bold; margin:7px 0 3px 0}

    div.s2-reqs10 table {margin-top:5px;}
    div.s2-reqs10 table td {padding:1px;}
    div.s2-reqs10 table td:first-child {font-weight:bold; padding-right:10px}
    div.s2-reqs40 div.db-list {height:70px; width:95%; overflow-y:scroll; padding:2px 5px 5px 5px; border:1px solid #d3d3d3;}
    div.s2-reqs60 div.tbl-list {padding:2px 5px 5px 5px; border:0 }
    div.s2-reqs60 div.tbl-list b {display:inline-block; width:55px; }

    div.s2-notice20 table.collation-list table {padding:2px;}
    div.s2-notice20 table.collation-list td:first-child {font-weight:bold; padding-right:5px }

    /*Warning Area and Message */
    .s2-warning-emptydb {color:maroon; margin:2px 0 0 0; font-size:11px; display: none; white-space:normal; width: 550px}
    .s2-warning-manualdb {color:#1B67FF; margin:2px 0 0 0; font-size:11px; display:none; white-space:normal; width: 550px}
    .s2-warning-renamedb {color:#1B67FF; margin:2px 0 0 0; font-size:11px; display:none; white-space:normal; width: 550px}
    #s2-tryagain {padding-top:50px; text-align:center; width:100%; font-size:16px; color:#444; font-weight:bold;}

    /* ============================
    STEP 3 VIEW
    ============================ */
    table.s3-opts {width:100%; border:0;}
    table.s3-opts i.fa{font-size:16px}
    table.s3-opts td{white-space:nowrap; padding:3px;}
    table.s3-opts td:first-child{width:90px; font-weight: bold}
    div.s3-allnonelinks {font-size:11px; float:right;}
    div.s3-manaual-msg {font-style: italic; margin:-2px 0 5px 0}
    small.s3-warn {color:maroon; font-style:italic}

    .url-mapping-header {
        font-weight: bold;
        margin-bottom: 10px;
    }

    .url-mapping-header .left {
        display: inline-block;
        width: calc(50% + 15px);
    }

    .url-mapping-entry {
        display: block;
        margin-bottom: 10px;
    }

    .url-mapping-entry .site-item {
        margin-top: 10px;
    }

    .url-mapping-entry .from-input-wrapper,
    .url-mapping-entry .to-input-wrapper{
        display: inline-block;
        width: calc(50% - 15px);
    }

    .url-mapping-entry .to-label-wrapper {
        display: inline-block;
        width: 30px;
        text-align: center;
    }

    .main_site .mu_replace {
        border: 2px solid red;
    }

    #plugins-filters {
        list-style: none;
        margin: 8px 0 0;
        padding: 0;
        font-size: 13px;
        float: left;
        color: #666;
    }

    #plugins-filters li {
        display: inline-block;
        margin: 0;
        padding: 0;
        white-space: nowrap;
    }

    #plugins-filters li a {
        color: #0073aa;
        line-height: 2;
        padding: .2em;
        text-decoration: none;
        text-transform: capitalize;
    }

    #plugins-filters li a:hover {
        color: #00a0d2;
    }

    #plugins-filters li a .count {
        color: #555d66;
        font-weight: 400;
    }

    #plugins-filters li a.current {
        font-weight: 600;
        border: none;
        color: #000;
    }

    #plugins-filters li::after {
        content: '|';
    }

    #plugins-filters li:last-child::after {
        content: '';
    }

    #plugins_list_table_selector {
        width: 100%;
    }

    #plugins_list_table_selector th {
        background-color: #cecece;
    }

    #plugins_list_table_selector .plugin-item:nth-child(odd) {
        background-color: #fbfbfb;
    }

    #plugins_list_table_selector .plugin-item:nth-child(even) {
        background-color: #ececec;
    }

    #plugins_list_table_selector .plugin-item td:first-child {
        border-left: 4px solid transparent;
    }

    #plugins_list_table_selector td {
        padding: 10px 5px;
    }

    #plugins_list_table_selector .check_input {
        text-align: center;
    }

    #plugins_list_table_selector .check_input input {
        margin: 0;
    }


    #plugins_list_table_selector .plugin-item.active td:first-child {
        border-left: 4px solid #00a0d2;
    }

    #plugins_list_table_selector .plugin-item.active:nth-child(odd) {
        background-color: #ebfaff;
    }
    #plugins_list_table_selector .plugin-item.active:nth-child(even) {
        background-color: #c5effc;
    }

    /* ============================
    STEP 4 VIEW
    ============================ */
    div.s4-final-msg {height:110px; border:1px solid #CDCDCD; padding:8px;font-size:12px; border-radius:5px;box-shadow:0 4px 2px -2px #777;}
    div.s4-final-title {color:#BE2323; font-size:18px}
    div.s4-connect {font-size:12px; text-align:center; font-style:italic; position:absolute; bottom:10px; padding:10px; width:100%; margin-top:20px}
    table.s4-report-results,
    table.s4-report-errs {border-collapse:collapse;}
    table.s4-report-errs  td {text-align:center; width:33%}
    table.s4-report-results th, table.s4-report-errs th {background-color:#efefef; padding:0; font-size:12px; padding:0}
    table.s4-report-results td, table.s4-report-errs td {padding: 3px; white-space:nowrap; border:1px solid #dfdfdf; text-align:center; font-size:11px}
    table.s4-report-results td:first-child {text-align:left; font-weight:bold; padding-left:5px}
    div.s4-err-title {background-color:#dfdfdf; font-weight: bold; margin:-3px 0 15px 0; padding:5px; border-radius:3px; font-size:13px}

    div.s4-err-msg {padding:8px;  display:none; border:1px dashed #999; margin:10px 0 20px 0; border-radius:5px;}
    div.s4-err-msg div.content{padding:5px; font-size:11px; line-height:17px; max-height:125px; overflow-y:scroll; border:1px solid silver; margin:3px;  }
    div.s4-err-msg div.info-error{padding:7px; background-color:#f9c9c9; border:1px solid silver; border-radius:2px; font-size:12px; line-height:16px }
    div.s4-err-msg div.info-notice{padding:7px; background-color:#FCFEC5; border:1px solid silver; border-radius:2px; font-size:12px; line-height:16px;}
    table.s4-final-step {width:100%;}
    table.s4-final-step td {padding:5px 15px 5px 5px;font-size:13px; }
    table.s4-final-step td:first-child {white-space:nowrap; width:165px}
    div.s4-go-back {border-top:1px dotted #dfdfdf; margin:auto; font-style:italic; font-size:11px; color:#333; padding-top:4px}
    div.s4-go-back ul {line-height:18px}
    button.s4-final-btns {cursor:pointer; color:#fff; font-size:16px; border-radius:5px; padding:7px; background-color:#13659C; border:1px solid gray; width:145px;}
    button.s4-final-btns:hover {background-color: #dfdfdf;}
    div.s4-warn {color:maroon;}

    /* ============================
    STEP 5 HELP
    ============================	*/
    #body-help div#content {
        width: 100%;
        max-width: 1024px;
    }

    .ui-tabs-panel >  .help-target,
    .hdr-sub1-area >  .help-target {
        position: absolute;
        top: -1px;
        right: 20px;
    }

    div.help-target a { 
        font-size:16px; 
        color:#13659C
    }

    div#main-help sup {font-size:11px; font-weight:normal; font-style:italic; color:blue}
    div.help-online {text-align:center; font-size:18px; padding:10px 0 0 0; line-height:24px}
    div.help {color:#555; font-style:italic; font-size:11px; padding:4px; border-top:1px solid #dfdfdf}
    div.help-page fieldset {margin-bottom:25px}
    div#main-help {font-size:13px; line-height:17px}
    div#main-help h3 {border-bottom:1px solid silver; padding:8px; margin:4px 0 8px 0; font-size:20px}
    div#main-help span.step {color:#DB4B38}
    .help-opt {width: 100%; border: none; border-collapse: collapse;  margin:5px 0 0 0;}
    .help-opt .col-opt {
        width: 250px;
    }
    .help-opt td.section {background-color:#dfdfdf;}
    .help-opt td, .help-opt th {padding:15px 10px; border:1px solid silver;}
    .help-opt td:first-child {font-weight:bold; padding-right:10px; white-space:nowrap}
    .help-opt th {background: #333; color: #fff;border:1px solid #333 }

    #main-help section {
        border: 1px solid silver;
        margin-top: 28px;
        border-radius: 4px;
        overflow: hidden;
    }

    #main-help section h2.header {
        background-color:#F1F1F1;
        padding:15px;
        margin:0;
        font-size:20px;
    }

    #main-help section .content {
        padding: 10px;
    }

    /* ============================
    Expandable section
    ============================	*/
    .expandable.close .expand-header {
        cursor: s-resize;
    }

    .expandable.open .expand-header {
        cursor: n-resize;
    }

    .expandable .expand-header::before {
        font-family: "Font Awesome 5 Free";
        margin-right: 10px;
    }

    .expandable.close .expand-header::before {
        content: "\f0fe";
    }

    .expandable.open .expand-header::before {
        content: "\f146";
    }

    .expandable.close .content {
        display: none;
    }

    .expandable.open .content {
        display: block;
    }

    /* ============================
    VIEW EXCEPTION
    ============================	*/
    .exception-trace {
        overflow: auto;
        border: 1px solid lightgray;
        padding: 10px;
        margin: 0;
    }

    /*================================================
    LIB OVERIDES*/
    input.parsley-error, textarea.parsley-error, select.parsley-error {
        color:#B94A48 !important;
        background-color:#F2DEDE !important;
        border:1px solid #EED3D7 !important;
    }
    ul.parsley-errors-list {margin:1px 0 0 -40px; list-style-type:none; font-size:10px}
    .ui-widget {font-size:13px}


    <?php if ($GLOBALS['DUPX_DEBUG']) : ?>
        .dupx-debug {display:block; margin:0 0 25px 0; font-size:11px; background-color:#f5dbda; padding:8px; border:1px solid silver; border-radius:4px}
        .dupx-debug label {font-weight:bold; display:block; margin:4px 0 1px 0}
        .dupx-debug textarea {width:95%; height:100px; font-size:11px}
        .dupx-debug input {font-size:11px; padding:3px}
    <?php else : ?>
        .dupx-debug {display:none}
    <?php endif; ?>

    /**PARAMS MANAGER DEBUG **/


    #params-html-info {
        position: fixed;
        top: 0;
        left: 940px;
        height: 100vh;
        width: calc(100vw - 940px);
        overflow: auto;
        background-color: rgba(255,255,255,.7);
        white-space: nowrap;
        font-family: monospace;
        font-size: 12px;
        line-height: 2;
        box-sizing: border-box;
        padding: 20px;
        border-left: 1px dotted
    }

</style>
<?php
DUPX_U_Html::css();
