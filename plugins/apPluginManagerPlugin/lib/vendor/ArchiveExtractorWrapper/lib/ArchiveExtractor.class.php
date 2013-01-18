<?php
/**

  +----------------------------------------------------------+
  |                                                          |
  |                    Archive Extractor Wrapper             |
  |                           Version: 1.0.0 (2005)          |
  |                    License - GNU/GPL                     |
  |                                                          |
  +----------------------------------------------------------+
  |        ZIP, TAR, GZIP type archive extractor (Wrapper)   |
  +----------------------------------------------------------+
  |        Author: NHM Tanveer Hossain Khan (Hasan)          |
  |        Email: admin@we4tech.com                          |
  |        Web: http://we4tech.com                           |
  +----------------------------------------------------------+
  |     Core Libraries:                                      |
  |         PhpConcept Library - Tar Module 1.3              |
  |         PhpConcept Library - Zip Module 2.1              |
  |                                                          |
  +----------------------------------------------------------+

*/

class ArchiveExtractor {


  /**

    Extract Archive(TarGzip, Zip) based on file suffix

    @param string Archive file
    @param string extraction path

    @return array

  */
  function extractArchive($archFile, $extractPath=".") {
    //echo "ARCH: ".$archFile."<br />ExtPath: $extractPath";
    $result="";
    
    if(preg_match("/tar|gz|tar.gz|tgz/",$archFile)) {
      $result=$this->extractTarGzip($archFile,$extractPath);
    }
    else if(preg_match("/zip/",$archFile)) {
      $result=$this->extractZip($archFile,$extractPath);
    }
    
    /* Return result */
    return $result;
  }

  /**

    TarGzip file extractor function
    
    @param string Archive file
    @param string extraction path
    
    @return string
    
  */
  function extractTarGzip($archFile, $extractPath=".") {
    /* include TAR library */
    require_once 'pcltar.func.php';
    
    /* extract and return list of extracted files */
    return PclTarExtract($archFile,$extractPath);
  }
  
  
  /**

    Zip file extractor function

    @param string Archive file
    @param string extraction path

    @return array

  */
  function extractZip($archFile, $extractPath=".") {
    /* include Zip Library file */
    require 'pclzip.class.php';
    
    
    /* Extract */
  	$zip=new PclZip($archFile);
  	
  	/* list of extracted files */
	  return $zip->extract($extractPath);
  }
  
  /**
    Get Tar/Gzip archive's file list
    @param string
    @return array
  */
  function getTarGzipList($archFile) {
    /* include TAR library */
    require_once 'pcltar.func.php';
    
    /* return list */
    return PclTarList($archFile);
  }
  
  /**
    Get Zip archive's file list
    @param string
    @return array
  */
  function getZipList($archFile) {
    /* include Zip Library file */
    require 'pclzip.class.php';


    /* Extract */
  	$zip=new PclZip($archFile);

    /* return */
    return $zip->listContent();
  }
  
  
}
?>
