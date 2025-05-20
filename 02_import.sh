#!/bin/bash
set -a
source .env
set +a
/usr/bin/php ./console.php import ./import.data
