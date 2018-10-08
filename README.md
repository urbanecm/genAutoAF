# genAFSkolniIP

This is a tool used to update regex in https://cs.wikipedia.org/wiki/Speci%C3%A1ln%C3%AD:Filtry_zneu%C5%BEit%C3%AD/100. It is strongly binded to Urbanecm's environment, please check source (especially run.sh) before running it for your own purposes. 

# Files
* script.php: reads ips.txt and saves regex that will match all IPs to re.txt
* run.sh: runs a query on wiki replicas, saves IPs to ips.txt, executes script.php and echoes path to URL with regex
