<?php
namespace NSAAOptions;
use NSAAOptions\NSAAConfig;
?>

<div class="wrap">
    <h2>North Shore AA Settings</h2>
    <?php settings_errors(); ?>
    <form method="POST" action="options.php">
        <?php
        settings_fields(NSAAConfig::OPTIONS_GROUP);
        do_settings_sections(NSAAConfig::PLUGIN_PAGE);
        submit_button();
        ?>
    </form>
</div>