<?php

    require('model/model.php');

    function editDocument() //Fonction qui gère l'affichage de la page editDocument
    {
	    $doc = htmlspecialchars($_GET['doc']);
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
        elseif(isset($_GET['a_s'])){
            $a_s = htmlspecialchars($_GET['a_s']);
        }

        $result = getDocument($doc,$type_id,$coll,$db,$serve);
	    $link_doc = getLink_doc($a_s,$s_g,$doc,$type_id,$coll,$db,$serve,$page);

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
        elseif(isset($_GET['a_s'])){
            $a_s = htmlspecialchars($_GET['a_s']);
        }

        $doc_text = strip_tags($_POST['doc_text']);
        $date_array = unserialize($_POST['date_array']);
        $up_date_array = unserialize($_POST['up_date_array']);

        try{
	    	$update = getUpdate_doc($doc_text,$date_array,$up_date_array);
	    	$id = getDoc_id($doc,$type_id);
	    	updateDoc($id,$update,$serve,$db,$coll);

	    	if(isset($s_g)){
                header('Location: index.php?action=getCollection_search&serve='.$serve.'&db='.$db.'&coll='.$coll.'&s_g='.$s_g.'&page='.$page.'');
            }
            elseif (isset($a_s)) {
                header('Location: index.php?action=advancedSearch&serve='.$serve.'&db='.$db.'&coll='.$coll.'&a_s='.urlencode($a_s).'&page='.$page.'');
            }
            else{
                header('Location: index.php?action=getCollection&serve='.$serve.'&db='.$db.'&coll='.$coll.'&page='.$page.'');
            }
	    }
        catch(Exception $e){
           echo "<script>alert(\"Le champ '_id' n'est pas modifiable\");document.location.href = index.php?action=getCollection;</script>";
        }
    }

    function getCollection()
    {
    	try{
            if(isset($_GET['coll'])){
            	$coll=htmlspecialchars($_GET['coll']);
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
        	$nbDocs = countDocs();
        	$nbPages = getNbPages($nbDocs,$bypage);

        	if(isset($_GET['page'])){
        		$page = htmlspecialchars($_GET['page']);
        	}
        	else{
        		$page = 1;
        	}

    	    $docs = getDocs($page,$bypage);

        	require('view/getCollection.php');
        }
        catch(Exception $e){
            echo $e;
        }
    }

    function createDocument()
    {
    	require('view/createDocument.php');
    }

    function traitement_nD()
    {
        try{
        	$doc = getNew_doc();
        	insertDoc($doc);
        	header('Location: index.php?action=getCollection&serve='.htmlspecialchars($_GET['serve']).'&db='.htmlspecialchars($_GET['db']).'&coll='.htmlspecialchars($_GET['coll']).'');
        }
        catch(Exception $e){
            echo $e;
        }
    }

    function deleteDocument()
    {
    	deleteDoc();
    	if(isset($_GET['search'])){
            header('Location: index.php?action=getCollection_search&serve='.htmlspecialchars($_GET['serve']).'&db='.htmlspecialchars($_GET['db']).'&coll='.htmlspecialchars($_GET['coll']).'&s_id='.htmlspecialchars($_GET['s_id']).'&s_g='.htmlspecialchars($_GET['s_g']).'&page='.htmlspecialchars($_GET['search']).'');
        }
        elseif(isset($_GET['search_db'])){
            header('Location: index.php?action=getDb_search&serve='.htmlspecialchars($_GET['serve']).'&db='.htmlspecialchars($_GET['db']).'&search_db='.htmlspecialchars($_GET['search_db']).'');
        }
        else{
            header('Location: index.php?action=getCollection&serve='.htmlspecialchars($_GET['serve']).'&db='.htmlspecialchars($_GET['db']).'&coll='.htmlspecialchars($_GET['coll']).'&page='.htmlspecialchars($_GET['page']).'');
        }
    }

    function viewDocument()
    {
    	try{
            $doc = htmlspecialchars($_GET['doc']);
            $type_id = htmlspecialchars($_GET['type_id']);
            $coll = htmlspecialchars($_GET['coll']);
            $db = htmlspecialchars($_GET['db']);
            $serve = htmlspecialchars($_GET['serve']);

            $result = getDocument($doc,$type_id,$coll,$db,$serve);
            require('view/viewDocument.php');
        }
        catch(Exception $e){
            echo "<script>alert(\"Le serveur n'autorise pas la connexion\");document.location.href = 'index.php';</script>";
        }
    }

    function getCollection_search()
    {
        try{
        	if(isset($_GET['bypage'])){
                $bypage = intval($_GET['bypage']);
            }
            else{
                $bypage = 20;
            }

        	if(isset($_POST['special_search'])){
                if(isset($_GET['page'])){
                    $page = htmlspecialchars($_GET['page']);
                }
                else{
                    $page = 1;
                }
                $s_search = htmlspecialchars($_POST['special_search']);
                $docs = getSpecialSearch($s_search,$page,$bypage);
                $nbDocs = countSpecialSearch($s_search);

            }
            elseif (isset($_GET['s_s'])) {
                if(isset($_GET['page'])){
                    $page = htmlspecialchars($_GET['page']);
                }
                else{
                    $page = 1;
                }
                $s_search = htmlspecialchars(urldecode($_GET['s_s']));
                $docs = getSpecialSearch($s_search,$page,$bypage);
                $nbDocs = countSpecialSearch($s_search);
            }
            else
            {
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
                   $recherche_g = htmlspecialchars($_POST['recherche_g']); 
                }
                if(isset($recherche_g)){
                     if($recherche_g=="field : content[...]"){
                        header('Location: index.php?action=getCollection&serve='.htmlspecialchars($_GET['serve']).'&db='.htmlspecialchars($_GET['db']).'&coll='.htmlspecialchars($_GET['coll']).'');
                    }
                    else{
                        $docs = getSearch($recherche_g,$page,$bypage);
                    }
                }
                else{
                    $docs = getDocs($page,$bypage);
                }
                $nbDocs = countSearch($recherche_g);
            }

        	$nbPages = getNbPages($nbDocs,$bypage);

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

        if(isset($_POST['a_s'])){
            $a_s = htmlspecialchars($_POST['a_s'],ENT_NOQUOTES);
        }
        elseif(isset($_GET['a_s'])){
            $a_s = htmlspecialchars(urldecode($_GET['a_s']),ENT_NOQUOTES);
        }

        if(isset($a_s)){
            $result = getAdvancedSearch($a_s,$page,$bypage,$serve,$db,$coll);
            
            if(testProjection($a_s,$serve,$db)){
                $docs = toJSON($result);
            }
            $nbDocs = countAdvancedSearch($a_s,$serve,$db,$coll);

            $nbPages = getNbPages($nbDocs,$bypage);
        }

        $link_search = '?'.$_SERVER['QUERY_STRING'];

        $link_reinit = '?action=advancedSearch&serve='.$serve.'&db='.$db.'&coll='.$coll.'';

        require('view/advancedSearch.php');

    }

    function renameCollection()
    {
        try{
            $newname = str_replace(' ', '_', htmlspecialchars($_POST['newname']));
            renameCollec($newname);
            header('Location: index.php?action=editCollection&serve='.htmlspecialchars($_GET['serve']).'&db='.htmlspecialchars($_GET['db']).'&coll='.$newname.'');
        }
        catch(Exception $e){
            echo "<script>alert(\"Le nouveau nom est identique à l'ancien\");document.location.href = 'index.php?action=getDb&db_id=".htmlspecialchars($_GET['db'])."';</script>";
        }
    }

    function editCollection()
    {
        require('view/editCollection.php');
    }

    function createCollection()
    {
        try{
            $newname = str_replace(' ', '_', htmlspecialchars($_POST['name']));
            createCollec($newname);
            header('Location: index.php?action=getDb&serve='.htmlspecialchars($_GET['serve']).'&db='.htmlspecialchars($_GET['db']).'');
        }
        catch(Exception $e){
            echo "<script>alert(\"Cette collection existe déjà\");document.location.href = 'index.php?action=getDb&serve=".htmlspecialchars($_GET['serve'])."&db=".htmlspecialchars($_GET['db'])."';</script>";
            echo $e;
        }
    }

    function deleteCollection()
    {
        deleteColl();
        header('Location: index.php?action=getDb&serve='.htmlspecialchars($_GET['serve']).'&db='.htmlspecialchars($_GET['db']).'');
    }

    function moveCollection()
    {
        $db = htmlspecialchars($_POST['newdb']);
        moveCollec($db);
        header('Location: index.php?action=getDb&serve='.htmlspecialchars($_GET['serve']).'&db='.htmlspecialchars($_GET['db']).'');
    }

    function getDb()
    {
    	if(isset($_GET['db']))
    	$db=htmlspecialchars($_GET['db']);

    	else{
    		header('Location: index.php?action=error');
    	}
    	$collections = getCollections($db);
    	require('view/getDb.php');
    }

    function getDb_search()
    {
        if(isset($_GET['db'])){
            if(isset($_POST['recherche_db'])){
                $search = htmlspecialchars($_POST['recherche_db']);
            }
            elseif(isset($_GET['search_db'])){
                $search = urldecode(htmlspecialchars($_GET['search_db']));
            }
            $db = htmlspecialchars($_GET['db']);
        }

        else{
            header('Location: index.php?action=error');
        }

        $docs = getSearch_db($search,$db);

        $nbDocs = 0;        

        foreach ($docs as $key => $value) {
            if(sizeof($value)!=0){
                $nbDocs =+sizeof($value);
            }
        }

        require('view/getDb_search.php');

    }

    function error()
    {
    	require('view/error.php');
    }

    function getServer()
    {
        $serve_list=json_decode($_COOKIE['serve_list']);
    	try{
            if(isset($_GET['serve'])){
        		$serve=htmlspecialchars($_GET['serve']);
        	}
        	elseif(isset($_POST['serve'])){
        		$serve=htmlspecialchars($_POST['serve']);
        		if(!in_array(htmlspecialchars($_POST['serve']), $serve_list)){
        			array_push($serve_list, $serve);
                    setcookie('serve_list',json_encode($serve_list));
        		}
        	}
        	elseif(!isset($_SESSION['serve'])){
        		header('Location: index.php?action=error');
        	}
        	$dbs = getDbs($serve);
        	require('view/getServer.php');
        }
        catch(Exception $e){
            if (($key = array_search(htmlspecialchars($_POST['serve']), $serve_list)) !== false) {
                unset($serve_list[$key]);
                setcookie('serve_list',json_encode($serve_list));
                $serve='localhost';
            }
            echo "<script>alert(\"Le serveur n'autorise pas la connexion\");document.location.href = 'index.php';</script>";
        }
    }

    function home()
    {
    	require('view/home.php');
    }

    function thread()
    {
        try{
            $link_thread = getLink_thread();
                header('Location: '.$link_thread.'');
        }
        catch(Exception $e){
            echo "<script>alert(\"Le serveur n'autorise pas la connexion\");document.location.href = 'index.php';</script>";
        }
    }