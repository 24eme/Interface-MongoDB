<!doctype html>
<html lang="fr">
<head>
	<?php echo "<title>".$coll."</title>"?>
	<meta charset="UTF-8">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link href="public/css/breadcrumb.css" rel="stylesheet" type="text/css">
	<link href="public/css/titre.css" rel="stylesheet" type="text/css">
	<link href="public/css/btn_return.css" rel="stylesheet" type="text/css">
	<link href="public/css/pagination.css" rel="stylesheet" type="text/css">
	<link href="public/css/getCollection.css" rel="stylesheet" type="text/css">

 	<script src="public/js/db.js"></script>
 	<script src="public/js/radio.js"></script>
</head>

<?php

//Fil d'Ariane

echo "<div class='container col-lg-8 sticky-top'>";
	echo '<ol class="breadcrumb">';
		echo '<li class="breadcrumb-item"><a href="index.php?"><i class="fa fa-fw fa-home"></i>Home</a></li>';
		if(isset($serve)){
			if($_GET['action']=='getServer'){
				echo '<li class="breadcrumb-item active">'.$serve.'</li>';
			}
			else{
				echo '<li class="breadcrumb-item"><a href="index.php?action=getServer&serve='.$serve.'"><i class="fa fa-fw fa-desktop"></i> '.$serve.'</a></li>';
			}
		}
		if(isset($db)){
			if($_GET['action']=='getDb'){
				echo '<li class="breadcrumb-item active"><i class="fa fa-fw fa-database"></i>'.$db.'</li>';
			}
			else{
				echo '<li class="breadcrumb-item"><a href="index.php?action=getDb&serve='.$serve.'&db='.$db.'"><i class="fa fa-fw fa-database"></i>'.$db.'</a></li>';
			}
		}
		if(isset($coll)){
			if($_GET['action']=='getCollection' or $_GET['action']=='getCollection_search'){
				echo '<li class="breadcrumb-item active"><i class="fa fa-fw fa-server"></i>'.$coll.'</li>';
			}
			else{
				echo '<li class="breadcrumb-item"><a href="index.php?action=getCollection&serve='.$serve.'&db='.$db.'&coll='.$coll.'"><i class="fa fa-fw fa-server"></i>'.$coll.'</a></li>';
			}
		}
		if(isset($doc)){
			echo '<li class="breadcrumb-item active"><i class="icon-book"></i>'.$doc.'</li>';
		}
	echo '</ol>';

echo '</div>';

//Fin fil d'Ariane


//Titre de la page 

echo "<h1 class='title text-center font-weight-bold'><i class='fa fa-fw fa-server'></i>".$coll."</h1>";

//Fin du titre de la page


//Sous-titre

echo '<h2 class="subtitle text-center">Documents '.(1+(($page-1)*$bypage)).'-';
if(($page*$bypage)<$nbDocs){echo $page*$bypage;}
else{echo $nbDocs;}
echo ' of '.$nbDocs.'</h2>';
?>

<!-- Fin du sous-titre -->


<!-- Partie recherche -->

<nav class="mb-2">


	<!-- Formulaire de recherche par id et clé:valeur -->

	<div  class="border col-lg-8 offset-lg-2 bg-light m-auto mb-2">
		<!-- <hr> -->
	
	<div id="options" class="text-center my-2">
	<!-- 	<span>
			<?php /*echo '<button class="btn btn-dark new_doc font-weight-bold mr-5"><a class=text-light href="index.php?action=createDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'"><i class="fa fa-fw fa-plus"></i><i class="fa fa-fw fa-book"></i></a></button>';*/ ?>
		</span> -->
		<?php echo '<a href="?action=advancedSearch&serve='.$serve.'&db='.$db.'&coll='.$coll.'">' ?>
			<button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal2">
				<i class="fa fa-fw fa-search"></i>Advanced Search
			</button>
		</a>
	</div>
	

	<div id="searchId" class="mt-1">
		<?php echo '<form autocomplete="off" method="post" action="index.php?action=getCollection_search&serve='.$serve.'&db='.$db.'&coll='.$coll.'">'; ?>
			<div class="input-group mb-1">
				<input type="search"  list="browsers" placeholder="Search by id or key:value"  class="form-control border border-success" name="recherche_g" id="recherche_g" />

				<!-- Autocomplétion des champs -->

				<datalist id="browsers">
			        <?php 
			        	foreach ($docs[0] as $key => $value) {  
			        		echo  "<option value=".$key.":>";
						}
			        ?> 
		 		</datalist> 

		 		<!-- Fin de l'autocomplétion des champs -->

				<input class="btn bg-success text-light "  type="submit" name="search" id="search" value="Search"/>
			</div>
		</form>
	</div>
		<!-- Fin du formulaire de recherche par id et clé:valeur -->
	</div>
</nav>

<!-- Fin de la partie recherche -->


<!-- Tableau des documents de la collection -->
	
<div id="main" class="border col-lg-8 offset-lg-2 bg-light m-auto ">
	<table class="table table-sm table-striped">
	    <?php 
			echo  	'<h3 class="text-center mb-1 bg-success text-light"><span><strong>Documents of "'.$coll.'"<span>
				 <button class="btn btn-dark align-items-center py-0 float-right new_doc font-weight-bold"><a class="text-light" href="index.php?action=createDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'"><i class="fa fa-fw fa-plus"></i><i class="fa fa-fw fa-book"></i></a></button>
			</span></h3>';

		?>

		<?php 
		if($nbDocs==0){
			echo 'Aucun document ne correspond à votre recherche.';
		}
		else{
			echo '<tr class="bg-dark text-light">';
			echo '<th>Id</th>';
			$keys = array();
			$i=0;
				foreach ($docs[0] as $key => $value) {
				$type = gettype($value);
				if ($key !== '_id' && $type !=='object') {
					 echo '<th>'.$key.'</th>';
					 array_push($keys, $key);
	                 $i++;
	                 if($i==3){
	                 	break;
	                 }
             	}

			}
			echo '<th>Action</th>';
			echo '</tr>';
			foreach ($docs as $doc) {
				echo '<tr class="mr-5">';
				$type_id = gettype($doc['_id']);
				if ($type_id=='object'){
					$id = (string)$doc['_id'];
				}
				else{
					$id = $doc['_id'];
				}

		
			        		// echo  "<option value=".$key.":>";
						

				//Liens des options de gestion des documents

				$link_v = 'index.php?action=viewDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'&doc='.$id.'&type_id='.$type_id.'&page='.$page;
				$link_e = 'index.php?action=editDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'&doc='.$id.'&type_id='.$type_id.'&page='.$page;
				$link_d = 'index.php?action=deleteDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'&doc='.$id.'&type_id='.$type_id.'&page='.$page;

				
				echo "<td id='d'><a class='text-success text-center' href=".$link_v."><i class='text-dark fa fa-fw fa-book'></i>".$id."</a></td>";
				
				//Affichage du tableau
				foreach ($keys as $k) {
					echo '<td>'.(array_key_exists($k, $doc) ? $doc[$k] : '').'</td>';	


				}

				echo "<td><button  class='btn py-0'><a class='text-dark' href=".$link_v."><i class='fa fa-eye'></a></button></td>";	
				echo '</tr>';
			}
				

		}
		?>
	</table>



	<div id="radio" class="text-center font-weight-bold">
		<i class="fa fa-fw fa-book mr-3"></i>
		<input type="radio" name="bypage" value="10" id="10" <?php if($bypage==10){echo 'checked="checked"';}?> onclick="bypage()" /> <label for="10">10</label>
		<input type="radio" name="bypage" value="20" id="20" <?php if($bypage==20){echo 'checked="checked"';}?> onclick="bypage()" /> <label for="20">20</label>
		<input type="radio" name="bypage" value="30" id="30" <?php if($bypage==30){echo 'checked="checked"';}?> onclick="bypage()" /> <label for="30">30</label>
		<input type="radio" name="bypage" value="50" id="50" <?php if($bypage==50){echo 'checked="checked"';}?> onclick="bypage()" /> <label for="50">50</label>
	</div>
          	<!-- Fin de la barre de boutons radio-->

	<!-- Bouton de retour -->

	<?php
	echo '<br><a href="index.php?action=getDb&serve='.$serve.'&db='.$db.'"><button class="return btn btn-primary getCollection font-weight-bold">< Database</button></a>';
	?>

	<!-- Fin du bouton de retour -->

</div>

<!-- Fin du tableau des documents de la collection -->


<!-- Pagination -->

<footer>
	<nav aria-label="pagination" >
        <ul class="pagination">

        <?php
            if($page!=1){
            	echo '<li><a href="index.php?action=getCollection&serve='.$serve.'&db='.$db.'&coll='.$coll.'&page='.($page-1).'" aria-current="page"><span aria-hidden="true">&laquo;</span><span class="visuallyhidden">previous set of pages</span></a></li>';
            }
            else{
            	echo '<li><a href="index.php?action=getCollection&serve='.$serve.'&db='.$db.'&coll='.$coll.'&page='.($page-1).'"><span aria-hidden="true">&laquo;</span><span class="visuallyhidden">previous set of pages</span></a></li>';
            }
            if($page!=$nbPages){
            	echo '<li><a href="index.php?action=getCollection&serve='.$serve.'&db='.$db.'&coll='.$coll.'&page='.($page+1).'" aria-current="page"><span class="visuallyhidden">next set of pages</span><span aria-hidden="true">&raquo;</span></a></li>';
            }
            else{
            	echo '<li><a href="index.php?action=getCollection&serve='.$serve.'&db='.$db.'&coll='.$coll.'&page='.($page+1).'"><span class="visuallyhidden">next set of pages</span><span aria-hidden="true">&raquo;</span></a></li>';
            }
        ?>
        </ul>
    </nav>
</footer>

<!-- Fin de la pagination -->

</body>
</html>