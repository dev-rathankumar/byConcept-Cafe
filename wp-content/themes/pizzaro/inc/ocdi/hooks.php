<?php

add_filter( 'pt-ocdi/import_files', 'pizzaro_ocdi_import_files' );

add_action( 'pt-ocdi/after_import', 'pizzaro_ocdi_after_import_setup' );

add_action( 'pt-ocdi/before_widgets_import', 'pizzaro_ocdi_before_widgets_import' );