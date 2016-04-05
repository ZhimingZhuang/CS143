#include "BTreeNode.h"

using namespace std;

/*
 * Read the content of the node from the page pid in the PageFile pf.
 * @param pid[IN] the PageId to read
 * @param pf[IN] PageFile to read from
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::read(PageId pid, const PageFile& pf)
{ 
	RC rc;
	// check whether the pid is in the valid range
	if(pid < 0 || pid > pf.endPid()) return RC_INVALID_PID;

    // read the page containing the record
    if ((rc = pf.read(pid, buffer)) < 0) return rc;	
    /*cur_pid = pid;*/
	return 0; 
}
    
/*
 * Write the content of the node to the page pid in the PageFile pf.
 * @param pid[IN] the PageId to write to
 * @param pf[IN] PageFile to write to
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::write(PageId pid, PageFile& pf)
{	
	RC rc;
	// check whether the pid is in the valid range
	if(pid < 0) return RC_INVALID_PID;	

	// set a sign to tell whether this node is leaf or not, set 0 if leaf node
	memset(buffer+PageFile::PAGE_SIZE-sizeof(PageId)-sizeof(char),0,sizeof(char));
	// read the page containing the record
    if ((rc = pf.write(pid, buffer)) < 0) return rc; 
	return 0; 	 
}

/*
 * Return the number of keys stored in the node.
 * @return the number of keys in the node
 */
int BTLeafNode::getKeyCount()
{ 
	int keycount;
	memcpy(&keycount,buffer,sizeof(keycount));
	return keycount; 
}

/*
 * Insert a (key, rid) pair to the node.
 * @param key[IN] the key to insert
 * @param rid[IN] the RecordId to insert
 * @return 0 if successful. Return an error code if the node is full.
 */
RC BTLeafNode::insert(int key, const RecordId& rid)
{ 
	int keycount;
	memcpy(&keycount,buffer,sizeof(keycount));
	int rid_bytes = sizeof(RecordId);
	int key_bytes = sizeof(int);
	int total_bytes = rid_bytes + key_bytes;

	/*
	* compute the maximum numbers of key of a node
	*/	
	
	if(keycount >= keymax) return RC_NODE_FULL;

	int i;
	for(i = 0; i < keycount; i++){
		int temp_key;
		memcpy(&temp_key,buffer+sizeof(keycount)+rid_bytes+i*total_bytes,key_bytes);
		if(temp_key > key) break;
	}
	memmove(buffer+sizeof(keycount)+(i+1)*total_bytes,buffer+sizeof(keycount)+i*total_bytes,(keycount-i)*total_bytes);
	memcpy(buffer+sizeof(keycount)+i*total_bytes,&rid,rid_bytes);
	memcpy(buffer+sizeof(keycount)+i*total_bytes+rid_bytes,&key,key_bytes);

	keycount++;
	memcpy(buffer,&keycount,sizeof(keycount));
	return 0; 
}

/*
 * Insert the (key, rid) pair to the node
 * and split the node half and half with sibling.
 * The first key of the sibling node is returned in siblingKey.
 * @param key[IN] the key to insert.
 * @param rid[IN] the RecordId to insert.
 * @param sibling[IN] the sibling node to split with. This node MUST be EMPTY when this function is called.
 * @param siblingKey[OUT] the first key in the sibling node after split.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::insertAndSplit(int key, const RecordId& rid, 
                              BTLeafNode& sibling, int& siblingKey)
{ 
	int keycount;
	memcpy(&keycount,buffer,sizeof(keycount));
	int rid_bytes = sizeof(RecordId);
	int key_bytes = sizeof(int);
	int total_bytes = rid_bytes + key_bytes;

	/*PageId next_pid;
	next_pid = getNextNodePtr(); */

	char buffer_temp[PageFile::PAGE_SIZE+total_bytes];

	/*
	* compute the maximum numbers of key of a node
	*/	

	if(keycount < keymax) return -1;

	memcpy(buffer_temp,buffer,sizeof(buffer)-sizeof(PageId));

	int i;
	for(i = 0; i < keycount; i++){
		int temp_key;
		memcpy(&temp_key,buffer_temp+sizeof(keycount)+rid_bytes+i*total_bytes,key_bytes);
		if(temp_key > key) break;
	}
	memmove(buffer_temp+sizeof(keycount)+(i+1)*total_bytes,buffer_temp+sizeof(keycount)+i*total_bytes,(keycount-i)*total_bytes);
	memcpy(buffer_temp+sizeof(keycount)+i*total_bytes,&rid,rid_bytes);
	memcpy(buffer_temp+sizeof(keycount)+i*total_bytes+rid_bytes,&key,key_bytes);

	int key_fhalf = (keycount+1)/2 + 1;
	int key_shalf = keycount + 1 - key_fhalf;

	memset(buffer,0,sizeof(buffer));
	memcpy(buffer,&key_fhalf,sizeof(key_fhalf));
	memcpy(buffer+sizeof(key_fhalf),buffer_temp+sizeof(keycount),key_fhalf*total_bytes);
	/*setNextNodePtr(sibling.cur_pid);*/

	memcpy(sibling.buffer,&key_shalf,sizeof(key_shalf));
	memcpy(sibling.buffer+sizeof(key_shalf),buffer_temp+sizeof(keycount)+key_fhalf*total_bytes,key_shalf*total_bytes);
	/*memcpy(sibling.buffer+PageFile::PAGE_SIZE-sizeof(PageId),&next_pid,sizeof(next_pid));*/

	memcpy(&siblingKey,sibling.buffer+sizeof(key_shalf)+rid_bytes,sizeof(int));


	return 0; 
}

/**
 * If searchKey exists in the node, set eid to the index entry
 * with searchKey and return 0. If not, set eid to the index entry
 * immediately after the largest index key that is smaller than searchKey,
 * and return the error code RC_NO_SUCH_RECORD.
 * Remember that keys inside a B+tree node are always kept sorted.
 * @param searchKey[IN] the key to search for.
 * @param eid[OUT] the index entry number with searchKey or immediately
                   behind the largest key smaller than searchKey.
 * @return 0 if searchKey is found. Otherwise return an error code.
 */
RC BTLeafNode::locate(int searchKey, int& eid)
{ 
	int keycount;
	memcpy(&keycount,buffer,sizeof(keycount));
	int rid_bytes = sizeof(RecordId);
	int key_bytes = sizeof(int);
	int total_bytes = rid_bytes + key_bytes;

	int i;
	int temp_key;
	for(i = 0; i < keycount; i++){
		memcpy(&temp_key,buffer+sizeof(keycount)+rid_bytes+i*total_bytes,key_bytes);
		if(temp_key == searchKey){
			eid = i+1;
			return 0;
		}else if(temp_key > searchKey){
			eid = i+1;
			return RC_NO_SUCH_RECORD;
		}
	}
	eid = i+1;
	return RC_NO_SUCH_RECORD;
}

/*
 * Read the (key, rid) pair from the eid entry.
 * @param eid[IN] the entry number to read the (key, rid) pair from
 * @param key[OUT] the key from the entry
 * @param rid[OUT] the RecordId from the entry
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::readEntry(int eid, int& key, RecordId& rid)
{ 

	int rid_bytes = sizeof(RecordId);
	int key_bytes = sizeof(int);
	int total_bytes = rid_bytes + key_bytes;

	int keycount;
	memcpy(&keycount,buffer,sizeof(keycount));

	if(eid < 1 && eid > keycount) return -1;

	memcpy(&key,buffer+sizeof(int)+rid_bytes+(eid-1)*total_bytes,key_bytes);
	memcpy(&rid,buffer+sizeof(int)+(eid-1)*total_bytes,rid_bytes);	
	return 0; 
}

/*
 * Return the pid of the next slibling node.
 * @return the PageId of the next sibling node 
 */
PageId BTLeafNode::getNextNodePtr()
{ 
	PageId next_pid;
	memcpy(&next_pid,buffer+PageFile::PAGE_SIZE-sizeof(next_pid),sizeof(next_pid));
	return next_pid; 
}

/*
 * Set the pid of the next slibling node.
 * @param pid[IN] the PageId of the next sibling node 
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::setNextNodePtr(PageId pid)
{ 
	if(pid < 0) return RC_INVALID_PID;
	memcpy(buffer+PageFile::PAGE_SIZE-sizeof(pid),&pid,sizeof(pid));
	return 0; 
}

/*
 * Read the content of the node from the page pid in the PageFile pf.
 * @param pid[IN] the PageId to read
 * @param pf[IN] PageFile to read from
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::read(PageId pid, const PageFile& pf)
{
	RC rc;
	// check whether the pid is in the valid range
	if(pid < 0 || pid > pf.endPid()) return RC_INVALID_PID;

    // read the page containing the record
    if ((rc = pf.read(pid, buffer)) < 0) return rc;	
	return 0; 
}
    
/*
 * Write the content of the node to the page pid in the PageFile pf.
 * @param pid[IN] the PageId to write to
 * @param pf[IN] PageFile to write to
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::write(PageId pid, PageFile& pf)
{ 
	RC rc;
	// check whether the pid is in the valid range
	if(pid < 0) return RC_INVALID_PID;	
	// set a sign to tell whether this node is leaf or not, set 1 if non-leaf node
	memset(buffer+PageFile::PAGE_SIZE-sizeof(PageId)-sizeof(char),1,sizeof(char));
	// read the page containing the record
    if ((rc = pf.write(pid, buffer)) < 0) return rc; 
	return 0; 
}

/*
 * Return the number of keys stored in the node.
 * @return the number of keys in the node
 */
int BTNonLeafNode::getKeyCount()
{
	int keycount;
	memcpy(&keycount,buffer,sizeof(keycount));
	return keycount;  
}


/*
 * Insert a (key, pid) pair to the node.
 * @param key[IN] the key to insert
 * @param pid[IN] the PageId to insert
 * @return 0 if successful. Return an error code if the node is full.
 */
RC BTNonLeafNode::insert(int key, PageId pid)
{ 

	int keycount;
	memcpy(&keycount,buffer,sizeof(keycount));
	int pid_bytes = sizeof(PageId);
	int key_bytes = sizeof(int);
	int total_bytes = pid_bytes + key_bytes;

	/*
	* compute the maximum numbers of key of a node
	*/	
	
	if(keycount >= keymax) return RC_NODE_FULL;

	int i;
	for(i = 0; i < keycount; i++){
		int temp_key;
		memcpy(&temp_key,buffer+sizeof(keycount)+pid_bytes+i*total_bytes,key_bytes);
		if(temp_key > key) break;
	}
	memmove(buffer+sizeof(keycount)+pid_bytes+(i+1)*total_bytes,buffer+sizeof(keycount)+pid_bytes+i*total_bytes,(keycount-i)*total_bytes);
	memcpy(buffer+sizeof(keycount)+pid_bytes+i*total_bytes,&key,key_bytes);
	memcpy(buffer+sizeof(keycount)+pid_bytes+i*total_bytes+key_bytes,&pid,pid_bytes);

	keycount++;
	memcpy(buffer,&keycount,sizeof(keycount));	
	return 0; 
}

/*
 * Insert the (key, pid) pair to the node
 * and split the node half and half with sibling.
 * The middle key after the split is returned in midKey.
 * @param key[IN] the key to insert
 * @param pid[IN] the PageId to insert
 * @param sibling[IN] the sibling node to split with. This node MUST be empty when this function is called.
 * @param midKey[OUT] the key in the middle after the split. This key should be inserted to the parent node.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::insertAndSplit(int key, PageId pid, BTNonLeafNode& sibling, int& midKey)
{ 

	int keycount;
	memcpy(&keycount,buffer,sizeof(keycount));
	int pid_bytes = sizeof(PageId);
	int key_bytes = sizeof(int);
	int total_bytes = pid_bytes + key_bytes;
	char buffer_temp[PageFile::PAGE_SIZE+total_bytes];
	/*
	* compute the maximum numbers of key of a node
	*/	

	if(keycount < keymax) return -1;

	memcpy(buffer_temp,buffer,sizeof(buffer));
	int i;
	for(i = 0; i < keycount; i++){
		int temp_key;
		memcpy(&temp_key,buffer_temp+sizeof(keycount)+pid_bytes+i*total_bytes,key_bytes);
		if(temp_key > key) break;
	}
	memmove(buffer_temp+sizeof(keycount)+pid_bytes+(i+1)*total_bytes,buffer_temp+sizeof(keycount)+pid_bytes+i*total_bytes,(keycount-i)*total_bytes);
	memcpy(buffer_temp+sizeof(keycount)+pid_bytes+i*total_bytes,&key,key_bytes);
	memcpy(buffer_temp+sizeof(keycount)+pid_bytes+i*total_bytes+key_bytes,&pid,pid_bytes);

	int key_fhalf = (keycount+1)/2;
	int key_shalf = keycount - key_fhalf;
	/*
	* Original buffer
	*/
	memset(buffer,0,sizeof(buffer));
	memcpy(buffer,&key_fhalf,sizeof(key_fhalf));
	memcpy(buffer+sizeof(key_fhalf),buffer_temp+sizeof(keycount),pid_bytes+key_fhalf*total_bytes);
	/*
	* Middle key
	*/
	memcpy(&midKey,buffer_temp+sizeof(key_fhalf)+pid_bytes+key_fhalf*total_bytes,key_bytes);
	/*
	* Sibling's buffer
	*/
	memcpy(sibling.buffer,&key_shalf,sizeof(key_shalf));
	memcpy(sibling.buffer+sizeof(key_shalf),buffer_temp+sizeof(keycount)+pid_bytes+key_fhalf*total_bytes+key_bytes,pid_bytes+key_shalf*total_bytes);

	return 0; 
}

/*
 * Given the searchKey, find the child-node pointer to follow and
 * output it in pid.
 * @param searchKey[IN] the searchKey that is being looked up.
 * @param pid[OUT] the pointer to the child node to follow.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::locateChildPtr(int searchKey, PageId& pid)
{ 

	int keycount;
	memcpy(&keycount,buffer,sizeof(keycount));
	int pid_bytes = sizeof(PageId);
	int key_bytes = sizeof(int);
	int total_bytes = pid_bytes + key_bytes;

	if(keycount < 1) return -1;

	int i;
	int temp_key;
	for(i = 0; i < keycount; i++){
		memcpy(&temp_key,buffer+sizeof(keycount)+pid_bytes+i*total_bytes,key_bytes);
		if(temp_key > searchKey){
			memcpy(&pid,buffer+sizeof(keycount)+i*total_bytes,pid_bytes);
			break;
		}else{
			if(i == keycount - 1){
				memcpy(&pid,buffer+sizeof(keycount)+keycount*total_bytes,pid_bytes);
				break;
			}
		}
	}	
	return 0; 
}

/*
 * Initialize the root node with (pid1, key, pid2).
 * @param pid1[IN] the first PageId to insert
 * @param key[IN] the key that should be inserted between the two PageIds
 * @param pid2[IN] the PageId to insert behind the key
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::initializeRoot(PageId pid1, int key, PageId pid2)
{ 

	int keycount;
	memcpy(&keycount,buffer,sizeof(keycount));
	if(keycount != 0) return -1;

	keycount = 1;
	memcpy(buffer,&keycount,sizeof(keycount));
	memcpy(buffer+sizeof(keycount),&pid1,sizeof(PageId));
	memcpy(buffer+sizeof(keycount)+sizeof(PageId),&key,sizeof(int));
	memcpy(buffer+sizeof(keycount)+sizeof(PageId)+sizeof(int),&pid2,sizeof(PageId));
	return 0; 
}
