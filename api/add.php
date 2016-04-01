<?php

include 'config.php';
include 'healthlib.php';
print_r(getHealthData(2));
echo "---";
addHealth(getHealthData(2),1000);
print_r(getHealthData(2));
