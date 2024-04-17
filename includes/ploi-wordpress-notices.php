<?php

function ploi_admin_notice_warn() {
    if (isset($_GET['clear-ploi'])) {
        if($_GET['clear-ploi'] == 'true'){
            echo '<div class="notice notice-success is-dismissible"><p>Ploi caches are cleared successfully.</p></div>'; 
        }
    }
}
add_action( 'admin_notices', 'ploi_admin_notice_warn' );