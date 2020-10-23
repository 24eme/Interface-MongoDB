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

echo "<div class='container border-top  border-success bg-success col-lg-8 sticky-top'>";
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

?>

<!-- Partie recherche -->

<nav class="mb-2">

	<!-- Formulaire de recherche par id et clé:valeur -->

	<div  class="border col-lg-8 offset-lg-2 bg-light m-auto mb-2">
		<div id="options" class="text-center my-2">

		</div>
		<div id="searchId" class="mt-1">
			<?php echo '<form autocomplete="off" method="post" action="index.php?action=getCollection_search&serve='.$serve.'&db='.$db.'&coll='.$coll.'">'; ?>
				<div class="input-group mb-1">
					<input type="search" autofocus="autofocus"  list="browsers" placeholder="Search by document id or key:value" required="required" class="form-control border border-success" name="recherche_g" id="recherche_g" />

					<!-- Autocomplétion des champs -->

					<datalist id="browsers">
				        <?php 
				        	foreach ($docs[0] as $key => $value) {  
				        		echo  "<option value=".$key.":>";
							}
				        ?> 
			 		</datalist> 

			 		<!-- Fin de l'autocomplétion des champs -->
					<div class="input-group-append">
					<input class="btn bg-success text-light "  type="submit" name="search" id="search" value="Search"/>
					</div>
				</div>
				<div class="text-right">
				<a class="btn btn-link btn-sm" href="?action=advancedSearch&serve=<?php echo $serve ?>&db=<?php echo $db ?>&coll=<?php echo $coll ?>"><i class="fa fa-fw fa-search"></i>Advanced Search</a>
				</div>
			</form>
		</div>

		<!-- Fin du formulaire de recherche par id et clé:valeur -->

	</div>
</nav>

<!-- Fin de la partie recherche -->


<!-- Tableau des documents de la collection -->
	
<div id="DivContentTable">
	<div id="main" class="border col-lg-8 offset-lg-2 bg-light m-auto getCollDiv">

		<table class="table table-sm table-striped">
		    <?php 

				echo '<h3 class="text-center mb-1 bg-success text-light"><span><strong>Documents '.(1+(($page-1)*$bypage)).'-';
					if(($page*$bypage)<$nbDocs){echo $page*$bypage;}
					else{echo $nbDocs;}
					echo ' of '.$nbDocs.'
					<span>
						 <button class="btn btn-dark align-items-center py-1 float-right new_doc font-weight-bold"><a class="text-light" href="index.php?action=createDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'"><i class="fa fa-fw fa-plus"></i><i class="fa fa-fw fa-book"></i></a></button>
					</span>

				</h3>';


			?>

			<?php 
			if($nbDocs==0){
				echo 'Aucun document ne correspond à votre recherche.';
			}
			else{
				foreach ($docs as $doc) {
					echo '<tr class="mr-5">';
					$type_id = gettype($doc['_id']);
					if ($type_id=='object'){
						$id = (string)$doc['_id'];
					}
					else{
						$id = $doc['_id'];
					}
					$content = array();
					foreach($doc as $x => $x_value) {
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
				 	unset($content['_id']);
				 	$json = stripslashes(json_encode($content));
				 	$jsonView = stripslashes(json_encode($content,JSON_PRETTY_PRINT));


				 	// $docs = stripslashes(json_encode($doc,JSON_PRETTY_PRINT));
							

					//Liens des options de gestion des documents

					$link_v = 'index.php?action=viewDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'&doc='.$id.'&type_id='.$type_id.'&page='.$page;
					$link_e = 'index.php?action=editDocument&serve='.$_GET['serve'].'&db='.$_GET['db'].'&coll='.$_GET['coll'].'&doc='.$id.'&type_id='.$type_id.'&page='.$page;

					echo "<td id='d'><a class='text-success text-center'  data-toggle='tooltip' title='".$json."' href=".$link_e."><i class='text-dark fa fa-fw fa-book'></i>".$id."</a></td>";
					echo '<td id="json">'.substr($json, 0, 100).'';
					if(strlen($json)>100){echo ' [...] }';}
					echo '</td>';
					echo '</tr>';
				}
					

			}
			?>
		</table>
	    <hr>
		<div class="row  justify-content-between m-1">

				<!-- Bouton de retour -->

				<div>
					<?php
					echo '<a href="index.php?action=getDb&serve='.$serve.'&db='.$db.'"><button class="return btn btn-primary getCollection font-weight-bold">< Database</button></a>';
					?>
				</div>

				<!-- Fin du bouton de retour -->


				<!-- Pagination -->
				<div class="row mr-2">
					<div >
						<?php

						echo '<h6 class="mr-2 pt-2">Documents '.(1+(($page-1)*$bypage)).'-';
							if(($page*$bypage)<$nbDocs){echo $page*$bypage;}
							else{echo $nbDocs;}
							echo ' of '.$nbDocs.'</h2>';
							?>
					</div>
					<div aria-label="pagination" >
				        <ul class="pagination">

				        <?php
				            if($page!=1){
				            	echo '<a href="index.php?action=getCollection&serve='.$serve.'&db='.$db.'&coll='.$coll.'&page='.($page-1).'&bypage='.$bypage.'" id="prev" aria-current="page"><span aria-hidden="true">&laquo;</span></a>';
				            }
				            else{
				            	echo '<span id="prev"><span aria-hidden="true">&laquo;</span></span>';
				            } ?>

				            <span  class="text-center bg-light font-weight-bold mr-1">
								<select name="bypage" onchange="bypage()">
								    <option value="10" id="10" <?php if($bypage==10){echo 'selected="selected"';}?>>10</option>
								    <option value="20" id="20" <?php if($bypage==20){echo 'selected="selected"';}?>>20</option>
								    <option value="30" id="30" <?php if($bypage==30){echo 'selected="selected"';}?>>30</option>
								    <option value="50" id="50" <?php if($bypage==50){echo 'selected="selected"';}?>>50</option>
								</select>
							</span>

				            <?php if($page!=$nbPages){
				            	echo '<a href="index.php?action=getCollection&serve='.$serve.'&db='.$db.'&coll='.$coll.'&page='.($page+1).'&bypage='.$bypage.'" id="next" aria-current="page"><span aria-hidden="true">&raquo;</span></a>';
				            }
				            else{
				            	echo '<span id="next"><span aria-hidden="true">&raquo;</span></span>';
				            }
				        ?>
				        </ul>
				    </div>
		       
			    <!-- Fin de la pagination -->

				</div>
			   		<!-- Bouton nouveau document -->
				<div class="ml-2">
					    <?php echo '<button class="btn btn-dark py-1 font-weight-bold"><a class="text-light" href="index.php?action=createDocument&serve='.$serve.'&db='.$db.'&coll='.$coll.'"><i class="fa fa-fw fa-plus"></i><i class="fa fa-fw fa-book"></i></a></button>'; ?>
				</div>
			  <!-- Fin du bouton nouveau document -->

		</div>
	</div> 
</div>

<!-- Fin du tableau des documents de la collection -->

<!-- footer -->

<?php 
	require_once('footer.php')
?>

   <!-- footer -->


<!-- Fin du tableau des documents de la collection -->

</body>
</html>