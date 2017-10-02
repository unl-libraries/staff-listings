<?php
header ("Pragma: no-cache");
header ("Content-type: application/vnd.ms-excel");
header ("Content-Disposition: attachment; filename=\"phone_list.xls\"" );
header ("Content-Description: Generated Report" );
echo $content_for_layout;?>