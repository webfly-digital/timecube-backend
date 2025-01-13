#!/bin/bash

find ./ -name "*.php" -o -name "*.html" -type f |
while read file
do
  if ! file -bi $file | grep -q 'utf-8'; then
    echo " $file"
    mv "$file" "$file".icv
    iconv -f WINDOWS-1251 -t UTF-8 "$file".icv > "$file"
    rm -f "$file".icv
  fi
done