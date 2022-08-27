# genAFSkolniIP

This is a tool used to update regex in https://cs.wikipedia.org/wiki/Speci%C3%A1ln%C3%AD:Filtry_zneu%C5%BEit%C3%AD/100. run.sh is binded to Urbanecm's environment, you'd need to adapt it. 

# Files
* script.php: reads ips.txt and saves regex that will match all IPs to re.txt. Authored by Teslaton@skwiki.
* run.sh: runs a query on wiki replicas, saves IPs to ips.txt, executes script.php and echoes path to URL with regex
