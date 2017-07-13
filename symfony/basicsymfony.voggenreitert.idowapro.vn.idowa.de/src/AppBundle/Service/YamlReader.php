<?php
/**
 * Created by PhpStorm.
 * User: schmidfl
 * Date: 18.04.2017
 * Time: 14:39
 */

namespace AppBundle\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

class YamlReader
{
    private $_yamlParser;

    /**
     * YamlReader constructor.
     * @param $_yamlParser
     */
    public function __construct(Parser $_yamlParser)
    {
        $this->_yamlParser = $_yamlParser;
    }

    public function readArray($file, $paths){
        $fs = new Filesystem();
        if(!$fs->exists($file)){
            // throw new FileNotFoundException("File $file does not exist!");
            return false;
        }

        $aFile = $this->_yamlParser->parse(file_get_contents($file));
        foreach($paths as $path){
            if($aFile[$path]){
                $aFile = $aFile[$path];
            } else {
                return false;
            }
        }

        return $aFile;
    }
}