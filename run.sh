#!/bin/bash

echo 'select page_title from templatelinks join page on page_id=tl_from where tl_title="Sdílená_IP_škola" and page_title not like "%:%" and page_namespace=3' | sql cswiki | sed 1d > ips.txt; php script.php ips.txt; cp re.txt ~/tmpPublic/re.txt
