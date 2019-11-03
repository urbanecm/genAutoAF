#!/bin/bash

curl 'https://cs.wikipedia.org/w/index.php?title=MediaWiki:Problematick%C3%A9_IP_adresy&action=raw' | grep -ve '^.*#' -e 'pre' > ips.txt
split -l 2500 ips.txt ips-
for ips in ips-*; do
	re=$(echo $ips | sed 's/ips/re/g')
	php script.php $ips $re
done
rm ips*
for re in re-*; do
	cp $re ~/tmpPublic/$re.txt
	echo https://tools.wmflabs.org/urbanecmbot/test/$re.txt
done
rm re*
