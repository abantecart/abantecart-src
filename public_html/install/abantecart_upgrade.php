<?php

/*
	1.2 Upgrade notes:
	- Updated all libraries to new versions. 
	JQuery 1.10 update notes:
		- replace depricated .live() method with .on()

	Bottstrap 3 update notes:


*/

       					
//clear cache after upgrade       					
$this->cache->delete('*');
