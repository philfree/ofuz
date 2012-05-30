<?php
// HTML and include Footer
    $d = dir($cfg_project_directory."includes/");
    while($entry = $d->read()) {
        if (preg_match("/\.footer\.inc\.php$/i", $entry) && !preg_match("/^\./", $entry)) {
            include_once($entry);
        }
    }
    $d->close();
?>
</body>
</html>
