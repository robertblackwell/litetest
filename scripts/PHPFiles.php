<?php
namespace Mkphar;
/**
* This class finds php files recursively and adds them to a collection.
*
* Usage:
*		$finder = new PHPFiles();
*		$finder->add($dir_full_path)->add($another_full_path);
*		...
*		$finder->add($file_full_path);	
*		...
*
*		$finder->add($dirname)->addIgnorePattern("[Test[a-zA-Z]*]")->addIgnorePattern("[[Docs|docs|docs]")
*
*		$array_of_php_file_paths = $finder->getFiles();
* 
*/
class PHPFiles
{
    private $php_files;
    
	/**
	* Constructor - initialize the array that maintains the list of files
	*/
    function __constuct()
	{
        $this->php_files = array();
        $this->ignore_patterns = array();
    }
	/**
	* Add a fully qualified path (file or directory) to the scan
	*
	* @param  $path
	* @returns $this
	*/
    function addPath($path)
    {
		
        $spl = new \SplFileInfo($path);
		$count = -1;
        if( $spl->isDir() ){
            $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
            foreach($objects as $name => $object){
                if( (strtolower($object->getExtension()) === 'php')  ){
                    $this->php_files[] = $object;
                }
            }
        } else if( $spl->isFile() ) {
            $this->php_files[] = $spl;
        }else{
            throw new \InvalidArgumentException(__METHOD__.": path [".$path."] does not exists or is nether file or dir");    
        }
		return $this;
    }
	/**
	* Returns the array of all the files found so far. Does not reset the collection of php files
	* @return array 
	*/
	function getFiles()
	{
		return $this->php_files;
	}

}