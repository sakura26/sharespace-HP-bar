<?php

include 'config.php';
include 'healthlib.php';
print_r(readHealth(getHealthData(2)));
