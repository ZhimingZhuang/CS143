I have tested all the test cases in sample tests and all are passed.

When doing project D, I find that I should do some change to project C. 
I simply add two functions 
	PageId getendpid() : get the pid of the last leafnode in the b+ tree
	int getleafkeycount(PageId pid) : get the numbers of key in a leafnode
Using these two functions, I can do project D more efficiently. 

At first, I didn't consider avoiding unnecessary page reading and cannot pass
some test cases. After I realized that problem, I fixed it by skipping some
unnecessary readings of values under certain conditions.

Especially, when dealing the query like "select count(*) from small" which 
use no where conditions and needn't to read table, I simply scan all the 
leafnodes of relevant b+ tree.

And I found that if the index file exists and there is no need to read the table,
this statement " (rc = rf.open(table + ".tbl", 'r'))<=0 " could be skipped. So I 
skip it which help me save one page.
