#!/bin/bash

echo 'select page_title from templatelinks join page on page_id=tl_from join linktarget on lt_id=tl_target_id where lt_namespace=10 and lt_title="Sdílená_IP_škola" and page_title not like "%:%" and page_namespace=3' | sql cswiki | sed 1d > ips.txt
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
