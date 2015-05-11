#!/usr/bin/env bash

cd ~/src/rt/

files=$(git grep 'const' \
               | awk '{print $1}' \
               | sed -r 's/([^:]+):.*/\1/' \
               | sort -u \
               | grep -E '\.(cc|h)$' \
     )

for file in $files; do
    echo $file
    sed -i 's/const//g' $file
done
