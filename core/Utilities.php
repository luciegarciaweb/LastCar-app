<?php
/**
 * Created by PhpStorm.
 * User: padbrain
 * Date: 06/06/18
 * Time: 21:20
 */

namespace BWB\Framework\mvc;

trait Utilities
{

    /**
     *      SEARCHING FOR VIEWS FILES IN VIEWS'S FOLDER AND SUBFOLDERS
     * @param $pFileName VIEW.FILE TO FIND
     * @param $pROOT Root from which to search
     * @return string
     */
    private function lycos($pFileName, $pRoot){
        //  Find all paths in $pRoot
        $paths = $this->rFindPaths($pRoot);
//        vardump($paths);
        $file = null;
        foreach ($paths as $pos => $folder){
//            if(is_dir($pRoot . $paths[$pos]) || $folder !== "." || $folder !== ".."){
                if(file_exists( $paths[$pos] . $pFileName) && is_readable($paths[$pos] . $pFileName)){
                    if(is_null($file)){
                        $file = $paths[$pos] . $pFileName;
                    }else{
                        echo "$pFileName may be in two paths \n";
                    }
                }
//            }
        }
//        vardump($file . " on file and line => " . __FILE__.__LINE__);
        return($file);
    }

    /**
     * @param $pRoot    FOLDER TO SEARCH PATHS IN
     * @return array    LINEAR ARRAY WITH ALL PATHS IN ROOT/VIEWS
     */
    private function rFindPaths($pRoot){
        //  Make sure to have a slash at the end of the root path
        $pRoot = rtrim($pRoot, "/") . "/";

        //  Array of possible paths
        $aPaths = [];
        //  First path is $pRoot
        $aPaths[] = $pRoot;
        //  Scan for elements in $pRoot
        $aElements = scandir($pRoot);

        //  ARRAY OF PATHS' ARRAY
        foreach ($aElements as $pos => $element){
            //  If the element is a folder
            if(is_dir($pRoot . $element) && $element != "." && $element != ".."){
                //  Have to stock it
                $aPaths[] = $pRoot . $element . "/";
                //  And look for subfolders in it
                $aPaths[] = $this->rFindPaths($pRoot . $element . "/");
            }
        }
//vardump($aPaths);
        /*
         *  $aPaths is an array of arrays of paths
         *  it's better to linear it to an array of paths
         */
        $laPath = [];
        foreach ($aPaths as $paths){
            //  Search for array in $aPaths
            if(is_array($paths)){
            	//	Search for paths
                foreach ($paths as $path){
                	//	Each path is memorized
                    $laPath[] = $path;
                }
            }else{
            	//	If you're not an array
            	//	you are a path
                $laPath[] = $paths;
            }
        }
        return array_values(array_unique($laPath));
    }

}
