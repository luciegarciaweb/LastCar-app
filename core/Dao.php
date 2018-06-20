<?php
namespace BWB\Framework\mvc;

use BWB\Framework\mvc\DatabaseConnect;
use BWB\Framework\mvc\Models\EntityModel;

/**
 * Cette classe sert de conteneur aux objets gérant l'accès aux données
 * Les implémentations concrètes implémenterons les interfaces
 * la connexion a la base de données est initialisée à la création
 * de l'objet via le fichier de configuration 
 * @link config/database.json fichier de configuration de l'accès axu données
 *
 * @author beweb-loic
 */
abstract class Dao implements CrudInterface, RepositoryInterface{
    /**
     * Cette propriété est une variable partagée entre toutes les instances
     * DAO ce qui evitera d'avoir plusieurs objets PDO tentant d'acceder 
     * en base de données.
     * Elle est donc privée avec un getter protected
     * @var PDO 
     */
    protected $pdo;
    
    /**
     * Tous les objets DAO doivent avoir access à l'objet PDO, 
     * L'instanciation du premier objet, initialise le PDO avec les données
     * du fichier database.json
     * 
     */
    function __construct()
    {
        $this->pdo = DatabaseConnect::getInstance();
//        if(is_null(DAO::$pdo)){
//            $config = json_decode(file_get_contents("./config/database.json"), true);
//            $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
//            DAO::$pdo = new PDO(
//                $config['driver'] . ":"
//                . "host=" . $config['host']
//                . ((empty($config['port'])) ? $config['port'] : (";port=" . $config['port']) )
//                . ";dbname=" . $config['dbname'],
//                $config['username'],
//                $config['password'],
//                $options
//            );
//            DAO::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
//        }
    }

    /*
     *      MÉTHODES PUBLIQUES
     */

    /**
     * CRUD - Create
     * @param array $aPropVal
     */
    public function create(array $aPropVal){
        try
        {
            $requete = $this->insert().$this->keys($aPropVal).$this->values($aPropVal);
            $req = $this->pdo->prepare($requete);

//                echo $requete;
//            vardump($req->execute());
            $req->execute($aPropVal);
            return $req->rowCount();
        }
        catch (\PDOException $e)
        {
            if($e->errorInfo[1] == 1062)
                throw new \Exception("L'élément que vous souhaitez créer existe déjà !");
//            return false;
        }

    }

    /**
     * CRUD - Retrieve
     * @param EntityModel $oModelEntity
     * @return mixed
     */
    public function retrieve(EntityModel $oModelEntity){
        try
        {
            $this->modelObj = $oModelEntity;
            return $this->getById($this->modelObj->getId());
        }
        catch (\Exception $e)
        {
            return false;
        }

    }

    /**
     * CRUD - Update
     * @param array $aPropVal
     * @return bool
     */
    public function update(array $aPropVal){
        try
        {
            $requete = $this->updateMysql().$this->set($aPropVal).$this->whereId();
            $req = $this->pdo->prepare($requete);
            $req->execute($aPropVal);
            return $req->rowCount();
        }
        catch (\PDOException $e)
        {
            return false;
        }
    }

    /**
     * CRUD - Delete
     * @param int $pId
     * @return bool
     */
    public function delete(int $pId){
        try
        {
            $requete = "DELETE FROM `" . $this->table . "` WHERE `id`=:id";
            $req = $this->pdo->prepare($requete);
            $req->bindValue(":id", $pId, \PDO::PARAM_INT);

            $req->execute();
            return $req->rowCount();
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    /**
     * @param EntityModel $oModelEntity
     * @return array
     */
    public function getAll(EntityModel $oModelEntity){
        try
        {
            $this->modelObj = $oModelEntity;
            $list = [];
            $req = $this->pdo->query("SELECT * FROM " . $this->table);
            $req->execute();

            foreach($req->fetchAll() as $data){
                $newEntity = clone $this->modelObj;
                $newEntity->hydrate($data);
                $list[] = $newEntity;
            }

            return $list;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    /**
     * @param $pId
     * @return mixed
     */
    public function getById($pId){
        try
        {
            $requete = "SELECT * FROM " . $this->table . " WHERE id= :id" ;
            $req = $this->pdo->prepare($requete);
            $req->bindParam(':id', $pId, \PDO::PARAM_INT);
            $req->execute();
            return($req->fetch());
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    /**
     * @param array $aPropVal
     */
    public function getAllBy(array $aPropVal){

    }

    /*
     *      MÉTHODES PRIVÉES
     */

    /**
     * @return string
     */
    private function insert(){
        return "INSERT INTO " . $this->table;
    }

    /**
     * @param $pArray Tableau associant Clef => Valeur
     * @return bool|string
     */
    private function keys($pArray){
        $req = " (";
        foreach($pArray as $key => $value){
            $req.= $key . ", ";
        }
        $req =substr ( $req ,  0 , -2 );
        $req.= ") ";
        return $req;
    }

    /**
     * @param $pArray Tableau associant Clef => Valeur
     * @return bool|string
     */
    private function values($pArray){
        $req = " VALUES (";
        foreach($pArray as $key => $value){
            $req.= "'" . $value . "', ";
//            $req.= ":" . $key . ", ";
        }
        $req =substr ( $req ,  0 , -2 );
        $req.= ") ";
        return $req;
    }

    /**
     * @return string
     */
    private function updateMysql(){
        return "UPDATE " . $this->table;
    }

    /**
     * @param $pArray
     * @return string
     */
    private function set($pArray){
        $req = " SET ";

        foreach ($pArray as $key => $value){
//            $req.= $key . "= '" . $value . "', ";
            $req.= $key . "= :" . $key . ", ";
        }

        $req = rtrim($req, ", ");

        $req.= " ";

        return $req;
    }

    /**
     * @return string
     */
    private function whereId(){
        return " WHERE id=:id";
    }
    
    /**
     * 
     * Ici l'accesseur est protected car l'objet PDO doit être accessible 
     * aux instances du DAO pour effectuer les requêtes 
     * sur le serveur de base de données.
     *  
     * @return DAO l'objet pdo stocké en variable de classe. 
     */
    protected function getPdo(){
        return $this->pdo;
    }

}
