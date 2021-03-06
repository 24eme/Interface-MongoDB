<?php

    require('model/model.php');

    function editDocument() //Fonction qui gère l'affichage de la page editDocument
    {
        //Création des variables

	    $doc = htmlspecialchars($_GET['doc']);
        if(isset($_GET['type_id'])){
            $type_id = htmlspecialchars($_GET['type_id']);
        }
        else{
            $type_id=null;
        }
        $coll = htmlspecialchars($_GET['coll']);
        $db = htmlspecialchars($_GET['db']);
        $serve = htmlspecialchars($_GET['serve']);
        $page = htmlspecialchars($_GET['page']);
        if(isset($_GET['s_g'])){
            $s_g = htmlspecialchars($_GET['s_g']);
        }
        else{
            $s_g=null;
        }
        if(isset($_GET['query']) and isset($_GET['proj']) and isset($_GET['a_s_coll'])){
            $query = htmlspecialchars($_GET['query'], ENT_NOQUOTES);
            $proj = htmlspecialchars($_GET['proj'], ENT_NOQUOTES);
            $a_s_coll = htmlspecialchars($_GET['a_s_coll']);
        }
        else{
            $query = null;
            $proj = null;
            $a_s_coll = null;
        }
        if(isset($_GET['search_db'])){
            $search_db = htmlspecialchars($_GET['search_db']);
        }
        else{
            $search_db = null;
        }

        $file_json = 'jsoneditor/package.json';
        $data_json = file_get_contents($file_json);
        if($data_json!==false){
            $jsoneditor = (strpos($data_json, 'jsoneditor') !== false);
        }
        else{
            $jsoneditor = false;
        }

        //Récupération du document au format curseur

        $result = getDocument($doc,$type_id,$coll,$db,$serve);

        // Création du lien d'envoi du formulaire

	    $link_doc = getLink_doc($search_db,$a_s_coll,$query,$proj,$s_g,$doc,$type_id,$coll,$db,$serve,$page);

        $link_d = 'index.php?action=deleteDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'&doc='.$doc.'&page='.$page;


        if(isset($type_id)){

           $link_d=$link_d.'&type_id='.$type_id;
        }
        if(isset($query) and isset($proj) and isset($a_s_coll)){
            $link_d=$link_d.'&a_s_coll='.$a_s_coll.'&query='.urlencode($query).'&proj='.urlencode($proj);
        }
        if(isset($s_g)){
            $link_d=$link_d.'&s_g='.$s_g;
        }
        if(isset($search_db)){
            $link_d=$link_d.'&search_db='.$search_db;
        }

	    require('view/editDocument.php');
    }


    function traitement_uD()
    {
    	$doc = htmlspecialchars($_GET['id']);
        if(isset($_GET['type_id'])){
            $type_id = htmlspecialchars($_GET['type_id']);
        }
        $coll = htmlspecialchars($_GET['coll']);
        $db = htmlspecialchars($_GET['db']);
        $serve = htmlspecialchars($_GET['serve']);
        $page = htmlspecialchars($_GET['page']);

        if(isset($_GET['s_g'])){
            $s_g = htmlspecialchars($_GET['s_g']);
        }
        elseif(isset($_GET['query']) and isset($_GET['proj']) and isset($_GET['a_s_coll'])){
            $query = htmlspecialchars($_GET['query'], ENT_NOQUOTES);
            $proj = htmlspecialchars($_GET['proj'], ENT_NOQUOTES);
            $a_s_coll = htmlspecialchars($_GET['a_s_coll']);
        }
        elseif(isset($_GET['search_db'])){
            $search_db = htmlspecialchars($_GET['search_db']);
        }

        $doc_text = strip_tags($_POST['doc_text']);

        $date_array = unserialize($_POST['date_array']);


        try{

           //on Check si le contenu respecte le format json si il reste sur la page avec l'erreur

             if(is_null(json_decode($doc_text)) ) {
                header('Location: index.php?action=editDocument&serve='.$_GET['serve'].'&db='.$_GET['db'].'&coll='.$_GET['coll'].'&doc='.$doc.'&type_id='.$type_id.'&page='.$page.'&msg=Syntax error : your document does not respect JSON syntax rules, example : {  "example_field": "content[...]"}'.'&doc_text='. json_encode(array(data => $doc_text)).'&input=true');
                return;
             }
	    	$update = getUpdate_doc($doc_text,$date_array);

	    	$id = getDoc_id($doc,$type_id);
	    	updateDoc($id,$update,$serve,$db,$coll);

	    	if(isset($s_g)){
                header('Location: index.php?action=editDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'&doc='.$doc.'&type_id='.$type_id.'&s_g='.$s_g.'&page='.$page.'');
            }
            elseif (isset($query) and isset($proj) and isset($a_s_coll)) {
                header('Location: index.php?action=editDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'&doc='.$doc.'&type_id='.$type_id.'&a_s_coll='.$a_s_coll.'&query='.urlencode($query).'&proj='.urlencode($proj).'&page='.$page.'');
            }
            elseif (isset($search_db)) {
                header('Location: index.php?action=editDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'&doc='.$doc.'&type_id='.$type_id.'&search_db='.$search_db.'&page='.$page.'');
            }
            else{
                header('Location: index.php?action=editDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'&doc='.$doc.'&type_id='.$type_id.'&page='.$page.'');
            }
	    }
        catch(Exception $e){
           echo "<script>alert(\"Le champ '_id' n'est pas modifiable\");document.location.href = index.php?action=getCollection;</script>";
        }
    }

    function getCollection()
    {
    	try{
            if(isset($_GET['coll']) and isset($_GET['serve']) and isset($_GET['db'])){
            	$coll=htmlspecialchars($_GET['coll']);
                $serve=htmlspecialchars($_GET['serve']);
                $db=htmlspecialchars($_GET['db']);
            }
        	else{
        		header('Location: index.php?action=error');
        	}

            if(isset($_GET['bypage'])){
                $bypage = intval($_GET['bypage']);
            }
            else{
                $bypage = 20;
            }

            if(isset($_GET['page'])){
                $page = htmlspecialchars($_GET['page']);
            }
            else{
                $page = 1;
            }

        	$nbDocs = countDocs($serve,$db,$coll);
        	$nbPages = getNbPages($nbDocs,$bypage);

    	    $docs = getDocs($page,$bypage,$serve,$db,$coll);

            if ($page > 1 && empty($docs)) {
                header("Location: ?action=getCollection&serve=$serve&db=$db&coll=$coll&page=$nbPages&bypage=$bypage");
                return;
            }

        	require('view/getCollection.php');
        }
        catch(Exception $e){
            echo $e;
        }
    }

    function createDocument()
    {
    	$serve = htmlspecialchars($_GET['serve']);
        $db = htmlspecialchars($_GET['db']);
        $coll = htmlspecialchars($_GET['coll']);

        $file_json = 'jsoneditor/package.json';
        $data_json = file_get_contents($file_json);
        if($data_json!==false){
            $jsoneditor = (strpos($data_json, 'jsoneditor') !== false);
        }
        else{
            $jsoneditor = false;
        }

        if(isset($_GET['s_g'])){
            $s_g = htmlspecialchars($_GET['s_g']);
        }

        if(isset($_GET['query']) and isset($_GET['proj']) and isset($_GET['a_s_coll'])){
            $query = htmlspecialchars($_GET['query'], ENT_NOQUOTES);
            $proj = htmlspecialchars($_GET['proj'], ENT_NOQUOTES);
            $a_s_coll = htmlspecialchars($_GET['a_s_coll']);
        }


        require('view/createDocument.php');
    }

    function traitement_nD()
    {
        $serve = htmlspecialchars($_GET['serve']);
        $db = htmlspecialchars($_GET['db']);
        $coll = htmlspecialchars($_GET['coll']);

        if(isset($_GET['query']) and isset($_GET['proj']) and isset($_GET['a_s_coll'])){
            $query = htmlspecialchars($_GET['query'], ENT_NOQUOTES);
            $proj = htmlspecialchars($_GET['proj'], ENT_NOQUOTES);
            $a_s_coll = htmlspecialchars($_GET['a_s_coll']);
        }

        $doc_text = strip_tags($_POST['doc_text']);

        try{

            //on Check si le contenu respecte le format json si il reste sur la page avec l'erreur

          if(is_null(json_decode($doc_text))) {
            header('Location: index.php?action=createDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'&msg=Syntax error : your document does not respect JSON syntax rules, example : {  "example_field": "content[...]"}'.'&doc_text='. json_encode(array(data => $doc_text)).'&input=true');
            return;

          }
        	$doc = getNew_doc($doc_text);

        	insertDoc($doc,$serve,$db,$coll);

            if(isset($query) and isset($proj) and isset($a_s_coll)){
                header('Location: index.php?action=advancedSearch&serve='.$serve.'&db='.$db.'&coll='.$coll.'&a_s_coll='.$a_s_coll.'&query='.urlencode($query).'&proj='.urlencode($proj).'&page=1');
            }
            else{
            	header('Location: index.php?action=getCollection&serve='.$serve.'&db='.$db.'&coll='.$coll.'');
            }
        }
        catch(Exception $e){
            echo $e;
        }
    }

    function deleteDocument()
    {
    	$serve = htmlspecialchars($_GET['serve']);
        $db = htmlspecialchars($_GET['db']);
        $coll = htmlspecialchars($_GET['coll']);
        $doc = htmlspecialchars($_GET['doc']);
        if(isset($_GET['page'])){
            $page = htmlspecialchars($_GET['page']);
        }
        else{
            $page = 1;
        }
        if(isset($_GET['type_id'])){
            $type_id = htmlspecialchars($_GET['type_id']);
        }
        else{
            $type_id=null;
        }
        if(isset($_GET['s_g'])){
            $s_g = htmlspecialchars($_GET['s_g']);
        }
        if(isset($_GET['query']) and isset($_GET['proj']) and isset($_GET['a_s_coll'])){
            $query = htmlspecialchars($_GET['query'], ENT_NOQUOTES);
            $proj = htmlspecialchars($_GET['proj'], ENT_NOQUOTES);
            $a_s_coll = htmlspecialchars($_GET['a_s_coll']);
        }
        if(isset($_GET['search_db'])){
            $search_db = htmlspecialchars($_GET['search_db']);
        }

        deleteDoc($serve,$db,$coll,$doc,$type_id);

    	if(isset($s_g)){
            header('Location: index.php?action=getCollection_search&serve='.$serve.'&db='.$db.'&coll='.$coll.'&s_g='.$s_g.'&page='.$page.'');
        }
        elseif(isset($search_db)){
            header('Location: index.php?action=getDb_search&serve='.$serve.'&db='.$db.'&search_db='.$search_db.'');
        }
        elseif(isset($query) and isset($proj) and isset($a_s_coll)){
            header('Location: index.php?action=advancedSearch&serve='.$serve.'&db='.$db.'&coll='.$coll.'&a_s_coll='.$a_s_coll.'&query='.urlencode($query).'&proj='.urlencode($proj).'&page='.$page.'');
        }
        else{
            header('Location: index.php?action=getCollection&serve='.$serve.'&db='.$db.'&coll='.$coll.'&page='.$page.'');
        }
    }

    function viewDocument()
    {
    	try{
            $doc = htmlspecialchars($_GET['doc']);
            if(isset($_GET['s_g'])){
                $s_g = htmlspecialchars($_GET['s_g']);
            }
            if(isset($_GET['a_s'])){
                $a_s = htmlspecialchars($_GET['a_s']);
            }
            if(isset($_GET['search_db'])){
                $search_db = htmlspecialchars($_GET['search_db']);
            }
            if(isset($_GET['type_id'])){
                $type_id = htmlspecialchars($_GET['type_id']);
            }
            else{
                $type_id = null;
            }
            $coll = htmlspecialchars($_GET['coll']);
            $db = htmlspecialchars($_GET['db']);
            $serve = htmlspecialchars($_GET['serve']);
            $page = htmlspecialchars($_GET['page']);

            $result = getDocument($doc,$type_id,$coll,$db,$serve);;
            require('view/viewDocument.php');
        }
        catch(Exception $e){
            header("Location: index.php?error=1&serve=".$serve."&db=".$db);
        }
    }

    function getCollection_search()
    {
        $serve = htmlspecialchars($_GET['serve']);
        $db = htmlspecialchars($_GET['db']);
        $coll = htmlspecialchars($_GET['coll']);

        try{
        	if(isset($_GET['bypage'])){
                $bypage = intval($_GET['bypage']);
            }
            else{
                $bypage = 20;
            }

            if(isset($_GET['page'])){
                $page = htmlspecialchars($_GET['page']);
            }
            else{
                $page = 1;
            }

            if(isset($_GET['s_g'])){
                $recherche_g = htmlspecialchars(urldecode($_GET['s_g']));
            }
            else{
                header('Location: index.php?action=getCollection&serve='.$serve.'&db='.$db.'&coll='.$coll.'');
            }

            if(isset($recherche_g)){
                if($recherche_g==""){
                    header('Location: index.php?action=getCollection&serve='.$serve.'&db='.$db.'&coll='.$coll.'');
                }
                else{
                    $docs = getSearch($recherche_g,$page,$bypage,$serve,$db,$coll);
                }
            }
            else{
                header('Location: index.php?action=getCollection&serve='.$serve.'&db='.$db.'&coll='.$coll.'');
                return;
            }
            $nbDocs = countSearch($recherche_g,$serve,$db,$coll);
            if($nbDocs==1){
                $type_id = gettype($docs[0]['_id']);
                if ($type_id=='object'){
                    $id = (string)$docs[0]['_id'];
                }
                else{
                    $id = $docs[0]['_id'];
                }
                header('Location: index.php?action=editDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'&doc='.$id.'&type_id='.$type_id.'&page=1');
            }
        	$nbPages = getNbPages($nbDocs,$bypage);

            if ($page > 1 && empty($docs)) {
                header("Location: ?action=getCollection_search&serve=$serve&db=$db&coll=$coll&page=$nbPages&bypage=$bypage&s_g=".urlencode($recherche_g));
                return;
            }

        	require('view/getCollection_search.php');
        }
        catch(Exception $e){
            echo $e;
        }
    }

    function advancedSearch()
    {
        $serve = htmlspecialchars($_GET['serve']);
        $db = htmlspecialchars($_GET['db']);
        $coll = htmlspecialchars($_GET['coll']);
        $current_query = htmlspecialchars($_SERVER['QUERY_STRING']);

        if(isset($_GET['bypage'])){
            $bypage = intval($_GET['bypage']);
        }
        else{
            $bypage = 20;
        }

        if(isset($_GET['page'])){
            $page = htmlspecialchars($_GET['page']);
        }
        else{
            $page = 1;
        }

        if(isset($_GET['query']) and isset($_GET['proj']) and isset($_GET['a_s_coll'])){
            $query = htmlspecialchars($_GET['query'], ENT_NOQUOTES);
            $proj = htmlspecialchars($_GET['proj'], ENT_NOQUOTES);
            $a_s_coll = htmlspecialchars($_GET['a_s_coll']);
        }
        // elseif(isset($_GET['a_s'])){
        //     $a_s =htmlspecialchars(urldecode($_GET['a_s']), ENT_NOQUOTES);
        // }
        elseif (isset($_GET['s_g'])) {
            $proj = '{}';
            $a_s_coll = $coll;
            $s_g = htmlspecialchars($_GET['s_g'], ENT_NOQUOTES);
            if(!strpos($s_g, ':')){
                try{
                    new MongoDB\BSON\ObjectId($s_g);
                    $query = '"'.$s_g.'"';
                }
                catch (Exception $e){
                    $query = '{_id:"'.$s_g.'"}';
                }
            }
            else{
                $s_g = str_replace(' ', '', $s_g);
                $s_g_array = explode(':', $s_g);
                $query = '{'.$s_g_array[0].':"'.$s_g_array[1].'"}';
            }
        }
        else{
            $query = '{}';
            $proj = '{}';
            $a_s_coll = $coll;
        }

        if (isset($_COOKIE['flash_error'])) {
          $flash_error = $_COOKIE['flash_error'];
          setcookie('flash_error');
        }

        $link_reinit = '?action=advancedSearch&serve='.$serve.'&db='.$db.'&coll='.$coll.'';

        if(isset($query) and isset($proj) and isset($a_s_coll)){
            try {
                $result = getAdvancedSearch($query,$proj,$a_s_coll,$page,$bypage,$serve,$db,$coll);

                if(testProjection($proj,$serve,$db)){
                    $docs = $result;
                    $fields = array();
                    foreach ($docs as $entry) {
                        foreach ($entry as $key => $value) {
                            if(!(in_array($key, $fields))){
                                array_push($fields, $key);
                            }
                        }
                    }
                }

                $nbDocs = countAdvancedSearch($query,$a_s_coll,$serve,$db,$coll);
                if($nbDocs==1){
                    $type_id = gettype($result[0]['_id']);
                    if ($type_id=='object'){
                        $id = (string)$result[0]['_id'];
                    }
                    else{
                        $id = $result[0]['_id'];
                    }
                    $search = 'db.'.$coll.'.find({})';
                    header('Location: index.php?action=editDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'&doc='.$id.'&type_id='.$type_id.'&a_s='.$search.'&page=1');
                }
                $nbPages = getNbPages($nbDocs,$bypage);
                $collections = getCollections($serve,$db);
                $tabcollections= array();
                foreach ($collections as $collection) {
                    $name = $collection->getName();
                    if($name != $a_s_coll){
                        array_push($tabcollections, $name);
                    }
                }
            }
            catch(Exception $e){
                $flash_error = "Failed to execute query: ".$e->getMessage();
            }
        }

        if (empty($result) && $page > 1) {
            header('Location: index.php?action=advancedSearch&serve='.$serve.'&db='.$db.'&coll='.$coll.'&page='.($nbPages).'&bypage='.$bypage.'&a_s='.urlencode($a_s));
            return;
        }

        $link_search = '?'.$_SERVER['QUERY_STRING'];

        require('view/advancedSearch.php');

    }

    function renameCollection()
    {
        $serve = htmlspecialchars($_GET['serve']);
        $db = htmlspecialchars($_GET['db']);
        $coll = htmlspecialchars($_GET['coll']);

        try{
            $newname = str_replace(' ', '_', htmlspecialchars($_GET['newname']));
            renameCollec($newname,$serve,$db,$coll);
            header('Location: index.php?action=editCollection&serve='.$serve.'&db='.$db.'&coll='.$newname.'');
        }
        catch(Exception $e){
            echo "<script>alert(\"Le nouveau nom est identique à l'ancien\");document.location.href = 'index.php?action=editCollection&serve=".$serve."&db=".$db."&coll=".$coll."';</script>";
        }
    }

    function editCollection()
    {
        $serve = htmlspecialchars($_GET['serve']);
        $db = htmlspecialchars($_GET['db']);
        $coll = htmlspecialchars($_GET['coll']);

        require('view/editCollection.php');
    }

    function createCollection()
    {
        $serve = htmlspecialchars($_GET['serve']);
        $db = htmlspecialchars($_GET['db']);

        try{
            $newname = str_replace(' ', '_', htmlspecialchars($_GET['name']));
            createCollec($newname,$serve,$db);
            header('Location: index.php?action=getDb&serve='.$serve.'&db='.$db.'');
        }
        catch(Exception $e){
            echo "<script>alert(\"Cette collection existe déjà\");document.location.href = 'index.php?action=getDb&serve=".$serve."&db=".$db."';</script>";
        }
    }

    function deleteCollection()
    {
        $serve = htmlspecialchars($_GET['serve']);
        $db = htmlspecialchars($_GET['db']);
        $coll = htmlspecialchars($_GET['coll']);

        deleteColl($serve,$db,$coll);
        header('Location: index.php?action=getDb&serve='.$serve.'&db='.$db.'');
    }

    function moveCollection()
    {
        $serve = htmlspecialchars($_GET['serve']);
        $db = htmlspecialchars($_GET['db']);
        $coll = htmlspecialchars($_GET['coll']);

        $newdb = htmlspecialchars($_GET['newdb']);

        moveCollec($newdb,$serve,$db,$coll);
        header('Location: index.php?action=getDb&serve='.$serve.'&db='.$db.'');
    }

    function getDb()
    {
    	if(isset($_GET['serve'])){
            $serve=htmlspecialchars($_GET['serve']);
        }
    	else{
    		header('Location: index.php?action=error');
    	}
        if(isset($_GET['db'])){
            $db=htmlspecialchars($_GET['db']);
        }
        elseif (isset($_GET['newdb'])) {
            $db=htmlspecialchars($_GET['newdb']);
        }
        else{
            header('Location: index.php?action=error');
        }
    	$collections = getCollections($serve,$db);
        $tabcollections= array();
        foreach ($collections as $collection) {
            $name = $collection->getName();
            $size = getCollectionSize($serve,$db,$name);
            $tabcollections[$name] = $size;
        }
        uksort($tabcollections, 'strcasecmp');
    	require('view/getDb.php');
    }

    function getDb_search()
    {
        if(isset($_GET['db']) and isset($_GET['serve'])){
            $db = htmlspecialchars($_GET['db']);
            $serve=htmlspecialchars($_GET['serve']);
            
            if(isset($_GET['search_db'])){
                $search = urldecode(htmlspecialchars($_GET['search_db']));
            }
            else{
                header('Location: index.php?action=getDb&serve='.$serve.'&db='.$db.'');
            }
        }

        else{
            header('Location: index.php?action=error');
            return;
        }

        $collections = getSearch_db($search,$db,$serve);

        $nbDocs = 0;

        foreach ($collections as $coll => $docs) {
            foreach($docs as $doc) {
                header('Location: index.php?action=editDocument&serve='.strip_tags($serve).'&db='.$db.'&coll='.$coll.'&doc='.$doc->_id.'&search_db=1&page=1');
                return;
            }

        }

        header('Location: index.php?action=getDb&serve='.$serve.'&db='.$db.'');
    }

    function error()
    {
    	require('view/error.php');
    }

    function getServer()
    {
        $host = htmlspecialchars($_POST['host']);
        $user = htmlspecialchars($_POST['user']);
        $port = htmlspecialchars($_POST['port']);
        if(isset($_GET['serve'])){
            $serve=htmlspecialchars($_GET['serve']);
        }
        elseif(isset($_POST['serve'])){
            $serve=htmlspecialchars($_POST['serve']);
        }
        if (!$serve) {
            $serve = $host.":".$port;
        }
        $server_info = explode(":", $serve);
        $host = $server_info[0];
        $port = $server_info[1];
        if (!$port) {
            $port = 27017;
        }

        $passwd = htmlspecialchars($_POST['passwd']);
        $auth_db = htmlspecialchars($_POST['auth_db']);

        if($user){
            if (!$auth_db) {
                $auth_db = 'admin';
//              setcookie('flash_error', "user / password : an authentication database is required");
//              header("Location: index.php?error=3&host=".$host."&user=".$user."&port=".$port."&auth_db=".$auth_db);
//              return;
            }
            try{
                $cookie_option = array('path'=> $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);

                setcookie('user', $user, $cookie_option);
                setcookie('passwd',$passwd, $cookie_option);
                setcookie('auth_db',$auth_db, $cookie_option);

                $dbs = getDbs($serve,$user,$passwd,$auth_db);
                require('view/getServer.php');
            }
            catch(Exception $e){
                setcookie('flash_error', $e->getMessage());
                header("Location: index.php?error=1&host=".$host."&user=".$user."&port=".$port."&auth_db=".$auth_db);
            }
            return;
        }

        $serve_list=json_decode($_COOKIE['serve_list']);
    	try{
        	if(!in_array($serve, $serve_list)){
        		array_push($serve_list, $serve);
                setcookie('serve_list',json_encode($serve_list));
        	}
        	$dbs = getDbs($serve);
        	require('view/getServer.php');
            return;
        }
        catch(Exception $e){
            if (($key = array_search($serve, $serve_list)) !== false) {
                unset($serve_list[$key]);
                setcookie('serve_list',json_encode($serve_list));
                $serve='localhost:27017';
            }
            setcookie('flash_error', $e->getMessage());
            header("Location: index.php?error=2&host=".$host."&user=".$user."&port=".$port."&auth_db=".$auth_db);
        }
    }

    function home()
    {
        $file_compopser = 'composer.json';
        $data_composer = file_get_contents($file_compopser);
        if(strpos($data_composer, 'mongodb/mongodb') == false){
            header('Location: index.php?action=install');
        }

        $modal_opened = isset($_GET['modal_opened']);
        $modal_error = isset($_GET['error']);

        $modal_host = htmlspecialchars($_GET['host']);
        $modal_port = htmlspecialchars($_GET['port']);
        $modal_user = htmlspecialchars($_GET['user']);
        $modal_auth_db = htmlspecialchars($_GET['auth_db']);

        if(isset($_GET['serve'])){
            $serve=htmlspecialchars($_GET['serve']);
            $server_info = explode(":", $serve);
            $modal_host = $server_info[0];
            $modal_port = $server_info[1];
        }

        if ($modal_host || $modal_db || $modal_port || $modal_user || $modal_error) {
            $modal_opened = 1 ;
        }
        if(!$modal_port) {
            $modal_port = 27017;
        }

        setcookie('user', "", time() - 3600);
        setcookie('passwd',"", time() - 3600);
        setcookie('auth_db',"", time() - 3600);

        if(extension_loaded('mongodb') == false){
            header('Location: index.php?action=install');
        }

        $flash_message = htmlspecialchars($_COOKIE['flash_message']);
        setcookie('flash_message');

        if ($modal_error) {
            $flash_error = "Unable to connect to server";
        }
        if (isset($_COOKIE['flash_error'])) {
            $flash_error = $_COOKIE['flash_error'];
            setcookie('flash_error');
        }

        require('view/home.php');
    }

    function removeServer()
    {
        $serve = htmlspecialchars($_GET['serve']);
        $serve_list=json_decode($_COOKIE['serve_list']);

        $key = array_search($serve, $serve_list);

        unset($serve_list[$key]);
        setcookie('serve_list',json_encode($serve_list));

        setcookie('flash_message',"Server $serve removed from the bookmark");
        header("Location: index.php\n");
        return;

    }

    function install()
    {
        $system = "Other";
        if (preg_match('/debian/i', php_uname())) {
            $system = "Debian";
        }elseif (preg_match('/redhat/i', php_uname())) {
            $system = "RedHat";
        }

        $php_mongo = (extension_loaded('mongodb') !== false);

        $file_compopser = 'composer.json';
        $data_composer = file_get_contents($file_compopser);
        $composer_mongo = (strpos($data_composer, 'mongodb/mongodb') !== false);

        $file_json = 'jsoneditor/package.json';
        $data_json = file_get_contents($file_json);
        $jsoneditor = (strpos($data_json, 'jsoneditor') !== false);

        require('view/install.php');
    }

    function export()
    {
        $serve = htmlspecialchars($_GET['serve']);
        $db = htmlspecialchars($_GET['db']);
        $form = htmlspecialchars($_GET['form']);
        $query = htmlspecialchars($_GET['query'], ENT_NOQUOTES);
        $proj = htmlspecialchars($_GET['proj'], ENT_NOQUOTES);
        $a_s_coll = htmlspecialchars($_GET['a_s_coll']);
        $return = htmlspecialchars($_GET['ret']);
        $return = str_replace('amp;', '', $return);

        $link_return = '?'.$return;

        if($form == 'json'){
            $name = 'json_'.rand().'.json';

            header('content-type:application/json');
            header('Content-Disposition: attachment; filename="'.$name.'"');
            
            $result = getDocs_export_j($serve,$db,$a_s_coll,$query);
            
            foreach ($result as $entry) {
                $content = array();
                foreach ($entry as $x => $x_value) {
                    if(gettype($x_value)=='object' and get_class($x_value)=='MongoDB\BSON\ObjectId'){
                        $value = $x_value;
                    }
                    elseif(gettype($x_value)=='object' and get_class($x_value)=='MongoDB\BSON\UTCDateTime'){
                        $value = $x_value->toDateTime();
                    }
                    else{
                        $value = printable($x_value);
                    }
                    $content[$x] =  improved_var_export($value);
                }
                $content = init_json($content);
                $json = stripslashes(json_encode($content,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                echo $json;
            }
        }
        elseif($form == 'csv'){
            $name = 'csv_'.rand().'.csv';

            $docs = getDocs_export($serve,$db,$a_s_coll,$query,$proj);

            $output = fopen("php://output",'w') or die("Can't open php://output");
            header("Content-Type:application/csv"); 
            header("Content-Disposition:attachment;filename=$name");
            $fields = array();
            foreach ($docs as $entry) {
                foreach ($entry as $key => $value) {
                    if(!(in_array($key, $fields))){
                        array_push($fields, $key);
                    }
                }
            }
            fputcsv($output, $fields);
            $content=array();
            foreach ($docs as $entry) {
                foreach ($entry as $key => $value) {
                    $content[$key]=$value;
                }
                fputcsv($output, $content);
            }
            fclose($output) or die("Can't close php://output");
        }
        else{
            header('Location: '.$link_return);
        }
    }