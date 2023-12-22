#!/bin/bash
set -e 
RESULT=$(find ./tests -type f -name '*Test.php')

for item in $RESULT; { 
    composer test $item
}