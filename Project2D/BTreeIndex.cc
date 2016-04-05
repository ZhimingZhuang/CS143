/*
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */
 
#include "BTreeIndex.h"
#include "BTreeNode.h"

using namespace std;

/*
 * BTreeIndex constructor
 */
BTreeIndex::BTreeIndex()
{
	rootPid = -1;
}

/*
 * Open the index file in read or write mode.
 * Under 'w' mode, the index file should be created if it does not exist.
 * @param indexname[IN] the name of the index file
 * @param mode[IN] 'r' for read, 'w' for write
 * @return error code. 0 if no error
 */
RC BTreeIndex::open(const string& indexname, char mode)
{
   RC rc;

    if(mode == 'w' && (rc = pf.open(indexname,'r'))<0){
        close();
        pf.open(indexname, mode);
        PageId pid0 = 0;
        PageId pid1 = 1;
        int keycount = 0;
        treeHeight = 1;
        char temp[PageFile::PAGE_SIZE];
        memcpy(temp,&pid1,sizeof(PageId));
        memcpy(temp+sizeof(PageId), &treeHeight, sizeof(int));
        pf.write(pid0,temp);
        
        memset(temp,0,PageFile::PAGE_SIZE);
        memcpy(temp,&keycount,sizeof(int));
        pf.write(pid1,temp);
        
    }else{
        close();
        if((rc = pf.open(indexname, mode)) < 0) return rc;
        if(mode == 'r'){
            char rootInf[PageFile::PAGE_SIZE];
            pf.read(0,rootInf);
            memcpy(&rootPid,rootInf,sizeof(PageId));
            memcpy(&treeHeight,rootInf+sizeof(PageId),sizeof(int));
        }
    }
    return 0;
}

/*
 * Close the index file.
 * @return error code. 0 if no error
 */
RC BTreeIndex::close()
{
    return pf.close();
}

/*
 * Insert (key, RecordId) pair to the index.
 * @param key[IN] the key for the value inserted into the index
 * @param rid[IN] the RecordId for the record being inserted into the index
 * @return error code. 0 if no error
 */
RC BTreeIndex::insert(int key, const RecordId& rid)
{
	char rootInf[PageFile::PAGE_SIZE];
	int keycount;
    PageId leafendpid = 1;
	pf.read(0,rootInf);
	memcpy(&rootPid,rootInf,sizeof(PageId));
	memcpy(&treeHeight,rootInf+sizeof(PageId),sizeof(int));

	if(rootPid == 1){
		BTLeafNode root1;

		root1.read(rootPid,pf);
		keycount=root1.getKeyCount();

		if(keycount < root1.keymax){
			root1.insert(key,rid);
			root1.write(rootPid,pf);
            
            memcpy(rootInf+sizeof(PageId)+sizeof(int),&leafendpid,sizeof(PageId));
            pf.write(0,rootInf);
            
		}else{
			PageId nextpid1 = rootPid + 1;
			int sibling1_key;
			BTLeafNode sibling1;
			root1.insertAndSplit(key,rid,sibling1,sibling1_key);
			root1.setNextNodePtr(nextpid1);
			root1.write(rootPid,pf);
			sibling1.write(nextpid1,pf);
            
            leafendpid = 2;
            memcpy(rootInf+sizeof(PageId)+sizeof(int),&leafendpid,sizeof(PageId));
            
			BTNonLeafNode newroot;
			newroot.initializeRoot(rootPid,sibling1_key,nextpid1);
			newroot.write(nextpid1+1,pf);

			rootPid = nextpid1 +1 ;
			treeHeight = 2;
			memcpy(rootInf,&rootPid,sizeof(PageId));
			memcpy(rootInf+sizeof(PageId),&treeHeight,sizeof(int));
			pf.write(0,rootInf);
		}
	}else{
		// locate the key and write down the traversing path
		PageId traverse[treeHeight];
		IndexCursor cursor;
		cursor = recurFindleaf(rootPid,key,traverse,0);
		// insert the key according to cursor
		BTLeafNode cursor_leaf;
		cursor_leaf.read(cursor.pid,pf);

		if(cursor_leaf.getKeyCount() < cursor_leaf.keymax){
			//if the leaf node is not full
			cursor_leaf.insert(key,rid);
			cursor_leaf.write(cursor.pid,pf);
		}else{
			//if the leaf node is full
			BTLeafNode cursor_sibling;
			int cursor_siblingkey;	
			int level = treeHeight - 1;
			PageId nextpid = cursor_leaf.getNextNodePtr();
			PageId siblingpid = pf.endPid();
            
			cursor_leaf.insertAndSplit(key,rid,cursor_sibling,cursor_siblingkey);
			cursor_leaf.setNextNodePtr(siblingpid);
			cursor_sibling.setNextNodePtr(nextpid);
            
            if(nextpid == 0){
                leafendpid = siblingpid;
                memcpy(rootInf+sizeof(PageId)+sizeof(int),&leafendpid,sizeof(PageId));
                pf.write(0,rootInf);
            }
            
			cursor_leaf.write(cursor.pid,pf);
			cursor_sibling.write(siblingpid,pf);

			recurinsert(traverse,level,cursor_siblingkey,siblingpid);

		}

	}
    
    return 0;
}

RC BTreeIndex::recurinsert(PageId traverse[], int& level, int key, PageId pid)
{
	
	if(level == 1){
		BTNonLeafNode root;
		BTNonLeafNode sibling;
		BTNonLeafNode newroot;
		char rootInf[PageFile::PAGE_SIZE];

		int midkey;
		PageId siblingpid;

		root.read(traverse[0],pf);
		if(root.getKeyCount() < root.keymax){
			root.insert(key,pid);
			root.write(traverse[0],pf);
		}else{
			root.insertAndSplit(key,pid,sibling,midkey);
			root.write(traverse[0],pf);
			siblingpid = pf.endPid();
			sibling.write(siblingpid,pf);

			rootPid = siblingpid + 1;
			treeHeight = treeHeight + 1;
			newroot.initializeRoot(traverse[0],midkey,siblingpid);
			newroot.write(rootPid,pf);
			// update rootPid and treeHeight in pagefile
			memcpy(rootInf,&rootPid,sizeof(PageId));
			memcpy(rootInf+sizeof(PageId),&treeHeight,sizeof(int));
			pf.write(0,rootInf);
		}

	}else{
		BTNonLeafNode sibling;
		BTNonLeafNode nonleaf;
		int midkey;
		PageId siblingpid;

		nonleaf.read(traverse[level-1],pf);
		if(nonleaf.getKeyCount()<nonleaf.keymax){
			nonleaf.insert(key,pid);
			nonleaf.write(traverse[level-1],pf);
		}else{
			nonleaf.insertAndSplit(key,pid,sibling,midkey);
			nonleaf.write(traverse[level-1],pf);
			siblingpid = pf.endPid();
			sibling.write(siblingpid,pf);
			level = level - 1;
			return recurinsert(traverse,level,midkey,siblingpid);			
		}
	}
	return 0;
}
/*
* Using recursive algorithm to locate the leafnode with searchKey
*/
IndexCursor BTreeIndex::recurFindleaf(PageId pid, int searchKey, PageId traverse[], int num)
{
	char buffer[PageFile::PAGE_SIZE];
	pf.read(pid,buffer);
	char flag;
	IndexCursor key_cursor;

	memcpy(&flag,buffer+PageFile::PAGE_SIZE-sizeof(PageId)-sizeof(char),sizeof(char));
	if((int)flag == 0){
	// we find the leafnode
		BTLeafNode leaf;
		leaf.read(pid,pf);
		leaf.locate(searchKey,key_cursor.eid);
		key_cursor.pid = pid;
		return key_cursor;
	}else{
	// search down level by level through nonleafnode
		BTNonLeafNode nonleaf;
		PageId childpid;
		nonleaf.read(pid,pf);

		traverse[num] = pid;
		num++;

		nonleaf.locateChildPtr(searchKey,childpid);

		return recurFindleaf(childpid,searchKey,traverse,num);
	}
}
/**
 * Run the standard B+Tree key search algorithm and identify the
 * leaf node where searchKey may exist. If an index entry with
 * searchKey exists in the leaf node, set IndexCursor to its location
 * (i.e., IndexCursor.pid = PageId of the leaf node, and
 * IndexCursor.eid = the searchKey index entry number.) and return 0.
 * If not, set IndexCursor.pid = PageId of the leaf node and
 * IndexCursor.eid = the index entry immediately after the largest
 * index key that is smaller than searchKey, and return the error
 * code RC_NO_SUCH_RECORD.
 * Using the returned "IndexCursor", you will have to call readForward()
 * to retrieve the actual (key, rid) pair from the index.
 * @param key[IN] the key to find
 * @param cursor[OUT] the cursor pointing to the index entry with
 *                    searchKey or immediately behind the largest key
 *                    smaller than searchKey.
 * @return 0 if searchKey is found. Othewise an error code
 */
RC BTreeIndex::locate(int searchKey, IndexCursor& cursor)
{
    int key;
    RecordId rid;
    int num = 0;
    char buffer[PageFile::PAGE_SIZE];
    IndexCursor temp_cursor;
    pf.read(0,buffer);
    memcpy(&rootPid,buffer,sizeof(PageId));
    memcpy(&treeHeight,buffer+sizeof(PageId),sizeof(int));

    PageId traverse[treeHeight];
    cursor = recurFindleaf(rootPid, searchKey,traverse,num);
    temp_cursor = cursor;
    readForward(temp_cursor, key, rid);
    if (key != searchKey) return RC_NO_SUCH_RECORD;
    return 0;
}

/*
 * Read the (key, rid) pair at the location specified by the index cursor,
 * and move foward the cursor to the next entry.
 * @param cursor[IN/OUT] the cursor pointing to an leaf-node index entry in the b+tree
 * @param key[OUT] the key stored at the index cursor location.
 * @param rid[OUT] the RecordId stored at the index cursor location.
 * @return error code. 0 if no error
 */
RC BTreeIndex::readForward(IndexCursor& cursor, int& key, RecordId& rid)
{

	if(cursor.pid < 0 || cursor.eid < 0) return RC_INVALID_CURSOR;

	BTLeafNode leaf;
	leaf.read(cursor.pid,pf);
	if(leaf.readEntry(cursor.eid,key,rid) < 0) return RC_NO_SUCH_RECORD;
	cursor.eid = cursor.eid + 1;
    return 0;
}

PageId BTreeIndex::getleafnextpid(PageId pid)
{
    PageId nextpid;
    BTLeafNode leaf;
    leaf.read(pid, pf);
    nextpid = leaf.getNextNodePtr();
    return nextpid;
}

int BTreeIndex::getleafkeycount(PageId pid)
{
    int keycount;
    BTLeafNode leaf;
    leaf.read(pid, pf);
    keycount = leaf.getKeyCount();
    return keycount;
}

PageId BTreeIndex::getendpid(){
    PageId leafendpid;
    char rootInf[PageFile::PAGE_SIZE];
    pf.read(0,rootInf);
    
    memcpy(&leafendpid,rootInf+sizeof(PageId)+sizeof(int),sizeof(PageId));
    return leafendpid;
}
