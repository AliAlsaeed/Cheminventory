<?php 
class Db {
    private $hostname = 'localhost';
    private $username = 'cheminve_ali';
    private $password = 't00532799?';
    private $database = 'cheminve_main';
    private $conn = NULL;
    public function __construct() { 
        $this->conn = mysqli_connect($this->hostname, $this->username, $this->password, $this->database); 
        if(!$this->conn) {
            echo 'Database not connected';
        }
    }
    public function getTouristCity(){
        $query = "SELECT location FROM invento_items WHERE is_enabled = '1'";
        $result = mysqli_query($this->conn, $query);
        return $result;
    }
    public function getVisitingPlaces(){
        $query = "SELECT * FROM visiting_places WHERE is_enabled = '1'";
        $result = mysqli_query($this->conn, $query);
        return $result;
    }
    public function getVisitinPlaceData($cityid, $placeid , $keyword){
        $sWhere = '';
        $where = array();
        if($cityid > 0) {
            $where[] = 'V.city_id = '.$cityid.' AND V.is_enabled = "1"';
        }
        if($placeid > 0) {
            $where[] = 'V.vid = '.$placeid;
        }
        if($keyword != '') {
            $keyword = trim($keyword);
            $where[] = "( V.visiting_place LIKE '%$keyword%' OR  V.history LIKE '%$keyword%'  OR  C.city LIKE '%$keyword%' )";
        }
        $sWhere     = implode(' AND ', $where);
        if($sWhere) {
            $sWhere = 'WHERE '.$sWhere;
        } 
        if(($cityid > 0) || ($placeid > 0) || ($keyword != '')) {
            $query = "SELECT * FROM visiting_places AS V JOIN tourist_city AS C ON C.city_id = V.city_id $sWhere ";
            $result = mysqli_query($this->conn, $query);
            return $result;
        }
    }
}
?>