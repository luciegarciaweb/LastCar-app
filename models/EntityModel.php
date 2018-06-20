<?php
/**
 * Created by PhpStorm.
 * User: padbrain
 * Date: 14/06/18
 * Time: 21:19
 */

namespace BWB\Framework\mvc;

abstract class EntityModel implements Persistable
{
    protected $dao;
    
    public function __construct() {
//        vardump(get_class($this));
        $childClass = explode("\\", get_class($this));
//        vardump($childClass);
        $childClass = end($childClass);
        $daoToLoad = "Dao\Dao".ucfirst($childClass);
        $this->dao = new $daoToLoad();
    }

    public function Create() {
        $response = $this->dao->create($_POST);
//        if($response == 0 ):
//            throw new \Exception("La création n'a pas pu être effectuée !");
//        else:
            return $response;
//        endif;
    }


    public function Retrieve() {
        $result = $this->dao->retrieve($this);
        if(is_bool($result)) throw new \Exception("L'utilisateur recherché n'existe pas !");
        $this->hydrate($result);
        return $this;
    }
    
    public function Update() {
        $aParams = $_POST;
        $aParams['id'] = $this->getId();
        $response = $this->dao->update($aParams);
        if($response == 0 ):
            throw new \Exception("Aucune modification n'a été effectuée !");
        else:
            return $response;
        endif;
    }
    
    public function Delete() {
        $response = $this->dao->delete($this->getId());
        if($response == 0 ) throw new \Exception("L'utilisateur n'a pas pu être supprimé !");
        return $response;
    }
    
    public function getAll(){
        return $this->dao->getAll($this);
    }
    
    public function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value) {
            $method = 'set'.ucfirst($key);
            if (method_exists($this, $method)){
                $this->$method($value);
            }
        }
    }
}