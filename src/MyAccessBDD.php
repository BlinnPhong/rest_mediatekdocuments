<?php
include_once("AccessBDD.php");

/**
 * Classe de construction des requêtes SQL
 * hérite de AccessBDD qui contient les requêtes de base
 * Pour ajouter une requête :
 * - créer la fonction qui crée une requête (prendre modèle sur les fonctions 
 *   existantes qui ne commencent pas par 'traitement')
 * - ajouter un 'case' dans un des switch des fonctions redéfinies 
 * - appeler la nouvelle fonction dans ce 'case'
 */
class MyAccessBDD extends AccessBDD {
	    
    /**
     * constructeur qui appelle celui de la classe mère
     */
    public function __construct(){
        try{
            parent::__construct();
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * demande de recherche
     * @param string $table
     * @param array|null $champs nom et valeur de chaque champ
     * @return array|null tuples du résultat de la requête ou null si erreur
     * @override
     */	
    protected function traitementSelect(string $table, ?array $champs) : ?array{
        switch($table){  
            case "livre" :
                return $this->selectAllLivres();
            case "dvd" :
                return $this->selectAllDvd();
            case "revue" :
                return $this->selectAllRevues();
            case "exemplaire" :
                return $this->selectExemplairesRevue($champs);
            case "commandedocument" :
                return $this->selectAllCommandeDocument($champs);
            case "abonnementrevue" :
                return $this->selectAllAbonnementsRevue($champs);
            case "abonnementfin" :
                return $this->selectAllAbonnementsFin();
            case "utilisateur" :
                return $this->selectUtilisateur($champs);
            case "genre" :
            case "public" :
            case "rayon" :
            case "etat" :
                // select portant sur une table contenant juste id et libelle
                return $this->selectTableSimple($table);
            case "" :
                // return $this->uneFonction(parametres);
            default:
                // cas général
                return $this->selectTuplesOneTable($table, $champs);
        }	
    }

    /**
     * demande d'ajout (insert)
     * @param string $table
     * @param array|null $champs nom et valeur de chaque champ
     * @return int|null nombre de tuples ajoutés ou null si erreur
     * @override
     */	
    protected function traitementInsert(string $table, ?array $champs) : ?int{
        switch($table){
            case "livre" :
                return $this->insertLivre($champs);
            case "dvd" :
                return $this->insertDvd($champs);
            case "revue" :
                return $this->insertRevue($champs);
            case "commandedocument" :
                return $this->insertCommandeDocument($champs);
            case "abonnement" :
                return $this->insertAbonnement($champs);
            default:                    
                // cas général
                return $this->insertOneTupleOneTable($table, $champs);	
        }
    }
    
    /**
     * demande de modification (update)
     * @param string $table
     * @param string|null $id
     * @param array|null $champs nom et valeur de chaque champ
     * @return int|null nombre de tuples modifiés ou null si erreur
     * @override
     */	
    protected function traitementUpdate(string $table, ?string $id, ?array $champs) : ?int{
        switch($table){
            case "livre" :
                return $this->updateLivre($id, $champs);
            case "dvd" :
                return $this->updateDvd($id, $champs);
            case "revue" :
                return $this->updateRevue($id, $champs);
            case "commandedocument" :
                return $this->updateCommandeDocument($id, $champs);
            default:                    
                // cas général
                return $this->updateOneTupleOneTable($table, $id, $champs);
        }	
    }  
    
    /**
     * demande de suppression (delete)
     * @param string $table
     * @param array|null $champs nom et valeur de chaque champ
     * @return int|null nombre de tuples supprimés ou null si erreur
     * @override
     */	
    protected function traitementDelete(string $table, ?array $champs) : ?int{
        switch($table){
            case "livre" :
                return $this->deleteLivre($champs);
            case "dvd" :
                return $this->deleteDvd($champs);
            case "revue" :
                return $this->deleteRevue($champs);
            case "commandedocument" :
                return $this->deleteCommandeDocument($champs);
            case "abonnement" :
                return $this->deleteAbonnement($champs);
            default:                    
                // cas général
                return $this->deleteTuplesOneTable($table, $champs);	
        }
    }	    
        
    /**
     * récupère les tuples d'une seule table
     * @param string $table
     * @param array|null $champs
     * @return array|null 
     */
    private function selectTuplesOneTable(string $table, ?array $champs) : ?array{
        if(empty($champs)){
            // tous les tuples d'une table
            $requete = "select * from $table;";
            return $this->conn->queryBDD($requete);  
        }else{
            // tuples spécifiques d'une table
            $requete = "select * from $table where ";
            foreach ($champs as $key => $value){
                $requete .= "$key=:$key and ";
            }
            // (enlève le dernier and)
            $requete = substr($requete, 0, strlen($requete)-5);	          
            return $this->conn->queryBDD($requete, $champs);
        }
    }	

    /**
     * demande d'ajout (insert) d'un tuple dans une table
     * @param string $table
     * @param array|null $champs
     * @return int|null nombre de tuples ajoutés (0 ou 1) ou null si erreur
     */	
    private function insertOneTupleOneTable(string $table, ?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        
        // construction de la requête
        $requete = "insert into $table (";
        foreach ($champs as $key => $value){
            $requete .= "$key,";
        }
        // (enlève la dernière virgule)
        $requete = substr($requete, 0, strlen($requete)-1);
        $requete .= ") values (";
        foreach ($champs as $key => $value){
            $requete .= ":$key,";
        }
        // (enlève la dernière virgule)
        $requete = substr($requete, 0, strlen($requete)-1);
        $requete .= ");";
        return $this->conn->updateBDD($requete, $champs);
    }
    
    /**
     * demande d'ajout (insert) d'un livre
     * @param array|null $champs
     * @return int|null
     * @throws Exception
     */
    private function insertLivre(?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        
        try {
            $this->conn->beginTransaction();
            
            $champs = array_change_key_case($champs, CASE_LOWER);
            
            $champsDocument = [ 'id'=>$champs['id'], 'titre'=>$champs['titre'], 'image'=>$champs['image'], 
                                'idrayon'=>$champs['idrayon'], 'idpublic'=>$champs['idpublic'], 'idgenre'=>$champs['idgenre']];
            $resultDocument = $this->insertOneTupleOneTable("document", $champsDocument);
            
            $champsLivreDvd = ['id'=>$champs['id']];
            $resultLivreDvd = $this->insertOneTupleOneTable("livres_dvd", $champsLivreDvd);
            
            $champsLivre = ['id'=>$champs['id'], 'isbn'=>$champs['isbn'], 'auteur'=>$champs['auteur'], 'collection'=>$champs['collection']];
            $resultLivre = $this->insertOneTupleOneTable("livre", $champsLivre);
            
            if(is_null($resultDocument) || is_null($resultLivreDvd) || is_null($resultLivre)){
                throw new Exception();
            }
            
            $this->conn->commit();
            return $resultLivre;
        } catch (Exception $ex) {
            $this->conn->rollback();
            return null;
        }
    }
    
    /**
     * demande d'ajout (insert) d'un dvd
     * @param array|null $champs
     * @return int|null
     * @throws Exception
     */
    private function insertDvd(?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }

        try {
            $this->conn->beginTransaction();

            $champs = array_change_key_case($champs, CASE_LOWER);

            $champsDocument = [ 'id'=>$champs['id'], 'titre'=>$champs['titre'], 'image'=>$champs['image'], 
                                'idrayon'=>$champs['idrayon'], 'idpublic'=>$champs['idpublic'], 'idgenre'=>$champs['idgenre']];
            $resultDocument = $this->insertOneTupleOneTable("document", $champsDocument);

            $champsLivreDvd = ['id'=>$champs['id']];
            $resultLivreDvd = $this->insertOneTupleOneTable("livres_dvd", $champsLivreDvd);

            $champsDvd = ['id'=>$champs['id'], 'synopsis'=>$champs['synopsis'], 'realisateur'=>$champs['realisateur'], 'duree'=>$champs['duree']];
            $resultDvd = $this->insertOneTupleOneTable("dvd", $champsDvd);

            if(is_null($resultDocument) || is_null($resultLivreDvd) || is_null($resultDvd)){
                throw new Exception();
            }

            $this->conn->commit();
            return $resultDvd;
        } catch (Exception $ex) {
            $this->conn->rollback();
            return null;
        }
    }
        
    /**
     * demande d'ajout (insert) d'une revue
     * @param array|null $champs
     * @return int|null
     * @throws Exception
     */
    private function insertRevue(?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        
        try {
            $this->conn->beginTransaction();
            
            $champs = array_change_key_case($champs, CASE_LOWER);
            
            $champsDocument = [ 'id'=>$champs['id'], 'titre'=>$champs['titre'], 'image'=>$champs['image'], 
                                'idrayon'=>$champs['idrayon'], 'idpublic'=>$champs['idpublic'], 'idgenre'=>$champs['idgenre']];
            $resultDocument = $this->insertOneTupleOneTable("document", $champsDocument);
                        
            $champsRevue = ['id'=>$champs['id'], 'periodicite'=>$champs['periodicite'], 'delaimiseadispo'=>$champs['delaimiseadispo']];
            $resultRevue = $this->insertOneTupleOneTable("revue", $champsRevue);
            
            if(is_null($resultDocument) || is_null($resultRevue)){
                throw new Exception();
            }
            
            $this->conn->commit();
            return $resultRevue;            
        } catch (Exception $ex) {
            $this->conn->rollback();
            return null;
        }
    }

    /**
     * demande d'ajout (insert) d'une commande d'un document (livre/dvd)
     * @param array|null $champs
     * @return int|null
     * @throws Exception
     */
    private function insertCommandeDocument(?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        
        try {
            $this->conn->beginTransaction();
            
            $champs = array_change_key_case($champs, CASE_LOWER);
            
            $champsCommande = [ 'id'=>$champs['id'], 'datecommande'=>$champs['datecommande'], 'montant'=>$champs['montant'] ];
            $resultCommande = $this->insertOneTupleOneTable("commande", $champsCommande);
            
            $champsCommandeDocument = ['id'=>$champs['id'], 'nbexemplaire'=>$champs['nbexemplaire'], 'idlivredvd'=>$champs['idlivredvd'], 'idsuivi'=>$champs['idsuivi']];
            $resultCommandeDocument = $this->insertOneTupleOneTable("commandedocument", $champsCommandeDocument);
            
            if(is_null($resultCommande) || is_null($resultCommandeDocument)){
                throw new Exception();
            }
            
            $this->conn->commit();
            return $resultCommandeDocument;
        } catch (Exception $ex) {
            $this->conn->rollback();
            return null;
        }
    }
    
    /**
     * demande d'ajout (insert) d'un abonnement
     * @param array|null $champs
     * @return int|null
     * @throws Exception
     */
    private function insertAbonnement(?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        
        try {
            $this->conn->beginTransaction();
            
            $champs = array_change_key_case($champs, CASE_LOWER);
            
            $champsCommande = [ 'id'=>$champs['id'], 'datecommande'=>$champs['datecommande'], 'montant'=>$champs['montant'] ];
            $resultCommande = $this->insertOneTupleOneTable("commande", $champsCommande);
            
            $champsAbonnement = [ 'id'=>$champs['id'], 'datefinabonnement'=>$champs['datefinabonnement'], 'idrevue'=>$champs['idrevue'] ];
            $resultAbonnement = $this->insertOneTupleOneTable("abonnement", $champsAbonnement);
            
            if(is_null($resultCommande) || is_null($resultAbonnement)){
                throw new Exception();
            }
            
            $this->conn->commit();
            return $resultAbonnement;
        } catch (Exception $ex) {
            $this->conn->rollback();
            return null;
        }
    }
    
    /**
     * demande de modification (update) d'un tuple dans une table
     * @param string $table
     * @param string\null $id
     * @param array|null $champs 
     * @return int|null nombre de tuples modifiés (0 ou 1) ou null si erreur
     */	
    private function updateOneTupleOneTable(string $table, ?string $id, ?array $champs) : ?int {
        if(empty($champs)){
            return null;
        }
        if(is_null($id)){
            return null;
        }
        // construction de la requête
        $requete = "update $table set ";
        foreach ($champs as $key => $value){
            $requete .= "$key=:$key,";
        }
        // (enlève la dernière virgule)
        $requete = substr($requete, 0, strlen($requete)-1);				
        $champs["id"] = $id;
        $requete .= " where id=:id;";		
        return $this->conn->updateBDD($requete, $champs);	        
    }
        
    /**
     * demande de modification (update) d'un livre
     * @param array|null $champs
     * @return int|null
     * @throws Exception
     */
    private function updateLivre(?string $id, ?array $champs) : ?int {
        if(empty($champs)){
            return null;
        }
        if(is_null($id)){
            return null;
        }
        
        try {
            $this->conn->beginTransaction();
            
            $champs = array_change_key_case($champs, CASE_LOWER);
            
            $champsDocument = [ 'titre'=>$champs['titre'], 'image'=>$champs['image'], 
                                'idrayon'=>$champs['idrayon'], 'idpublic'=>$champs['idpublic'], 'idgenre'=>$champs['idgenre']];
            $resultDocument = $this->updateOneTupleOneTable("document", $id, $champsDocument);
            
            $champsLivre = [ 'isbn'=>$champs['isbn'], 'auteur'=>$champs['auteur'], 'collection'=>$champs['collection']];
            $resultLivre = $this->updateOneTupleOneTable("livre", $id, $champsLivre);
            
            if(is_null($resultDocument) || is_null($resultLivre)){
                throw new Exception();
            }
            
            $this->conn->commit();
            return $resultLivre;
        } catch (Exception $ex) {
            $this->conn->rollback();
            return null;
        }
    }
       
    /**
     * demande de modification (update) d'un dvd
     * @param array|null $champs
     * @return int|null
     * @throws Exception
     */
    private function updateDvd(?string $id, ?array $champs) : ?int {
        if(empty($champs)){
            return null;
        }
        if(is_null($id)){
            return null;
        }
        
        try {
            $this->conn->beginTransaction();
            
            $champs = array_change_key_case($champs, CASE_LOWER);
            
            $champsDocument = [ 'titre'=>$champs['titre'], 'image'=>$champs['image'], 
                                'idrayon'=>$champs['idrayon'], 'idpublic'=>$champs['idpublic'], 'idgenre'=>$champs['idgenre']];
            $resultDocument = $this->updateOneTupleOneTable("document", $id, $champsDocument);
            
            $champsDvd = [ 'synopsis'=>$champs['synopsis'], 'realisateur'=>$champs['realisateur'], 'duree'=>$champs['duree']];
            $resultDvd = $this->updateOneTupleOneTable("dvd", $id, $champsDvd);
            
            if(is_null($resultDocument) || is_null($resultDvd)){
                throw new Exception();
            }
            
            $this->conn->commit();
            return $resultDvd;
        } catch (Exception $ex) {
            $this->conn->rollback();
            return null;
        }
    }
    
    /**
     * demande de modification (update) d'une revue
     * @param array|null $champs
     * @return int|null
     * @throws Exception
     */
    private function updateRevue(?string $id, ?array $champs) : ?int {
        if(empty($champs)){
            return null;
        }
        if(is_null($id)){
            return null;
        }
        
        try {
            $this->conn->beginTransaction();
            
            $champs = array_change_key_case($champs, CASE_LOWER);
            
            $champsDocument = [ 'titre'=>$champs['titre'], 'image'=>$champs['image'], 
                                'idrayon'=>$champs['idrayon'], 'idpublic'=>$champs['idpublic'], 'idgenre'=>$champs['idgenre']];
            $resultDocument = $this->updateOneTupleOneTable("document", $id, $champsDocument);
            
            $champsRevue = [ 'periodicite'=>$champs['periodicite'], 'delaimiseadispo'=>$champs['delaimiseadispo']];
            $resultRevue = $this->updateOneTupleOneTable("revue", $id, $champsRevue);
            
            if(is_null($resultDocument) || is_null($resultRevue)){
                throw new Exception();
            }
            
            $this->conn->commit();
            return $resultRevue;
        } catch (Exception $ex) {
            $this->conn->rollback();
            return null;
        }
    }
    
    /**
     * demande de modification (update) d'une commande d'un document (livre/dvd)
     * @param array|null $champs
     * @return int|null
     * @throws Exception
     */
    private function updateCommandeDocument(?string $id, ?array $champs) : ?int {
        if(empty($champs)){
            return null;
        }
        if(is_null($id)){
            return null;
        }
        
        try {
            $this->conn->beginTransaction();
            
            $champs = array_change_key_case($champs, CASE_LOWER);
            
            $champsCommande = [ 'datecommande'=>$champs['datecommande'], 'montant'=>$champs['montant'] ];
            $resultCommande = $this->updateOneTupleOneTable("commande", $id, $champsCommande);
            
            $champsCommandeDocument = [ 'nbexemplaire'=>$champs['nbexemplaire'], 'idsuivi'=>$champs['idsuivi'] ];
            $resultCommandeDocument = $this->updateOneTupleOneTable("commandedocument", $id, $champsCommandeDocument);
            
            if(is_null($resultCommande) || is_null($resultCommandeDocument)){
                throw new Exception();
            }
            
            $this->conn->commit();
            return $resultCommandeDocument;
        } catch (Exception $ex) {
            $this->conn->rollback();
            return null;
        }
    }
    
    /**
     * demande de suppression (delete) d'un ou plusieurs tuples dans une table
     * @param string $table
     * @param array|null $champs
     * @return int|null nombre de tuples supprimés ou null si erreur
     */
    private function deleteTuplesOneTable(string $table, ?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        // construction de la requête
        $requete = "delete from $table where ";
        foreach ($champs as $key => $value){
            $requete .= "$key=:$key and ";
        }
        // (enlève le dernier and)
        $requete = substr($requete, 0, strlen($requete)-5);   
        return $this->conn->updateBDD($requete, $champs);	        
    }
    /**
     * demande de suppression (delete) d'un livre
     * @param array|null $champs
     * @return int|null
     * @throws Exception
     */
    private function deleteLivre(?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        
        try {
            $this->conn->beginTransaction();
            
            $resultLivre = $this->deleteTuplesOneTable("livre", $champs);            
            $resultLivreDvd = $this->deleteTuplesOneTable("livres_dvd", $champs);
            $resultDocument = $this->deleteTuplesOneTable("document", $champs);
            
            if(is_null($resultLivre) || is_null($resultLivreDvd) || is_null($resultDocument)){
                throw new Exception();
            }
            
            $this->conn->commit();
            return $resultLivre;
        } catch (Exception $ex) {
            $this->conn->rollback();
            return null;
        }
    }
    
    /**
     * demande de suppression (delete) d'un dvd
     * @param array|null $champs
     * @return int|null
     * @throws Exception
     */
    private function deleteDvd(?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        
        try {
            $this->conn->beginTransaction();
            
            $resultDvd = $this->deleteTuplesOneTable("dvd", $champs);            
            $resultLivreDvd = $this->deleteTuplesOneTable("livres_dvd", $champs);
            $resultDocument = $this->deleteTuplesOneTable("document", $champs);
            
            if(is_null($resultDvd) || is_null($resultLivreDvd) || is_null($resultDocument)){
                throw new Exception();
            }
            
            $this->conn->commit();
            return $resultDvd;
        } catch (Exception $ex) {
            $this->conn->rollback();
            return null;
        }
    }
    
    /**
     * demande de suppression (delete) d'une revue
     * @param array|null $champs
     * @return int|null
     * @throws Exception
     */
    private function deleteRevue(?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        
        try {
            $this->conn->beginTransaction();
            
            $resultRevue = $this->deleteTuplesOneTable("revue", $champs);           
            $resultDocument = $this->deleteTuplesOneTable("document", $champs);
            
            if(is_null($resultRevue) || is_null($resultDocument)){
                throw new Exception();
            }
            
            $this->conn->commit();
            return $resultRevue;
        } catch (Exception $ex) {
            $this->conn->rollback();
            return null;
        }
    }
    
    /**
     * demande de suppression (delete) d'une commande de document
     * @param array|null $champs
     * @return int|null
     * @throws Exception
     */
    private function deleteCommandeDocument(?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        
        try {
            $this->conn->beginTransaction();
            
            $resultCommandeDocument = $this->deleteTuplesOneTable("commandedocument", $champs);           
            $resultCommande = $this->deleteTuplesOneTable("commande", $champs);
            
            if(is_null($resultCommandeDocument) || is_null($resultCommande)){
                throw new Exception();
            }
            
            $this->conn->commit();
            return $resultCommandeDocument;
        } catch (Exception $ex) {
            $this->conn->rollback();
            return null;
        }
    }
    
    /**
     * demande de suppression (delete) d'un abonnement
     * @param array|null $champs
     * @return int|null
     * @throws Exception
     */
    private function deleteAbonnement(?array $champs) : ?int {
        if(empty($champs)){
            return null;
        }
        
        try {
            $this->conn->beginTransaction();
            
            $resultAbonnement = $this->deleteTuplesOneTable("abonnement", $champs);           
            $resultCommande = $this->deleteTuplesOneTable("commande", $champs);
            
            if(is_null($resultAbonnement) || is_null($resultCommande)){
                throw new Exception();
            }
            
            $this->conn->commit();
            return $resultAbonnement;
        } catch (Exception $ex) {
            $this->conn->rollback();
            return null;
        }
    }
 
    /**
     * récupère toutes les lignes d'une table simple (qui contient juste id et libelle)
     * @param string $table
     * @return array|null
     */
    private function selectTableSimple(string $table) : ?array{
        $requete = "select * from $table order by libelle;";		
        return $this->conn->queryBDD($requete);	    
    }
    
    /**
     * récupère toutes les lignes de la table Livre et les tables associées
     * @return array|null
     */
    private function selectAllLivres() : ?array{
        $requete = "Select l.id, l.ISBN, l.auteur, d.titre, d.image, l.collection, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from livre l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";		
        return $this->conn->queryBDD($requete);
    }	

    /**
     * récupère toutes les lignes de la table DVD et les tables associées
     * @return array|null
     */
    private function selectAllDvd() : ?array{
        $requete = "Select l.id, l.duree, l.realisateur, d.titre, d.image, l.synopsis, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from dvd l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";	
        return $this->conn->queryBDD($requete);
    }	

    /**
     * récupère toutes les lignes de la table Revue et les tables associées
     * @return array|null
     */
    private function selectAllRevues() : ?array{
        $requete = "Select l.id, l.periodicite, d.titre, d.image, l.delaiMiseADispo, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from revue l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";
        return $this->conn->queryBDD($requete);
    }	

    /**
     * récupère tous les exemplaires d'une revue
     * @param array|null $champs 
     * @return array|null
     */
    private function selectExemplairesRevue(?array $champs) : ?array{
        if(empty($champs)){
            return null;
        }
        if(!array_key_exists('id', $champs)){
            return null;
        }
        $champNecessaire['id'] = $champs['id'];
        $requete = "Select e.id, e.numero, e.dateAchat, e.photo, e.idEtat ";
        $requete .= "from exemplaire e join document d on e.id=d.id ";
        $requete .= "where e.id = :id ";
        $requete .= "order by e.dateAchat DESC";
        return $this->conn->queryBDD($requete, $champNecessaire);
    }		    
        
    /**
     * récupère tous les commandes d'un document
     * @param array|null $champs
     * @return array|null
     */
    private function selectAllCommandeDocument(?array $champs) : ?array {
        if(empty($champs)){
            return null;
        }
        if(!array_key_exists('id', $champs)){
            return null;
        }
        
        $champNecessaire['id'] = $champs['id'];
        $requete = "Select c.dateCommande, c.montant, cd.nbExemplaire, s.libelle as suivi, ";
        $requete .= "c.id, cd.idLivreDvd, cd.idSuivi ";
        $requete .= "from commande c join commandedocument cd on c.id = cd.id ";
        $requete .= "join suivi s on s.id = cd.idSuivi ";
        $requete .= "where cd.idLivreDvd = :id ";
        $requete .= "order by c.dateCommande DESC";		
        return $this->conn->queryBDD($requete, $champNecessaire);
    }
    
    /**
     * récupère tous les abonnements d'une revue
     * @param array|null $champs
     * @return array|null
     */
    private function selectAllAbonnementsRevue(?array $champs) : ?array {
        if(empty($champs)){
            return null;
        }
        if(!array_key_exists('id', $champs)){
            return null;
        }
        
        $champNecessaire['id'] = $champs['id'];
        $requete = "Select c.dateCommande, c.montant, a.dateFinAbonnement, d.titre as titreRevue, ";
        $requete .= "c.id, a.idRevue ";
        $requete .= "from commande c join abonnement a on c.id = a.id join document d on a.idRevue = d.id ";
        $requete .= "where a.idRevue = :id ";
        $requete .= "order by c.dateCommande DESC ";		
        return $this->conn->queryBDD($requete, $champNecessaire);
    }
    
    /**
     * récupère tous les abonenemnts arrivant bientôt à échéance
     * @return array|null
     */
    private function selectAllAbonnementsFin() : ?array { 
        $requete = "Select c.dateCommande, c.montant, a.dateFinAbonnement, d.titre as titreRevue, ";
        $requete .= "c.id, a.idRevue ";
        $requete .= "from commande c join abonnement a on c.id = a.id join document d on a.idRevue = d.id ";
        $requete .= "where a.dateFinAbonnement BETWEEN CURDATE() AND date_add(CURDATE(), INTERVAL 30 DAY)";
        $requete .= "order by a.dateFinAbonnement DESC ";		
        return $this->conn->queryBDD($requete);
    }
    
    /**
     * récupère un utilisateur selon les identifiants passer dans le paramètre champs
     * @param array|null $champs
     * @return array|null
     */
    private function selectUtilisateur(?array $champs) : ?array { 
        
        if(empty($champs)){
            return null;
        }
        if(!array_key_exists('login', $champs) || !array_key_exists('pwd', $champs)){
            return null;
        }       
        
        $champNecessaire['login'] = $champs['login'];        
        $champNecessaire['pwd'] = $champs['pwd'];
        $requete = "SELECT u.login, u.idService, s.nom as service ";
        $requete .= "FROM utilisateur u JOIN service s on u.idService = s.id ";
        $requete .= "WHERE u.login = :login AND u.pwd = SHA2(:pwd, 256)";		
        return $this->conn->queryBDD($requete, $champs);
    }
}
