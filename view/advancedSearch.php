<!doctype html>
<html lang="fr">
<head>
	<title>Advanced Search</title>
	<?php require_once('layouts/header.php') ?>
</head>

<body>

<?php include('layouts/breadcrumb.php'); ?>

<div id="advancedSearch" class="container">

<?php

//Préparation des variables de recherche pour leur utilisation en JS

if(isset($query) and isset($proj) and isset($a_s_coll)){ ?>
	<p id="clé_c" style="display: none">a_s_coll</p>
	<input type=hidden id=valeur_c value=<?php echo $a_s_coll; ?>>
	<p id="clé_q" style="display: none">query</p>
	<input type=hidden id=valeur_q value=<?php echo $query; ?>>
	<p id="clé_p" style="display: none">proj</p>
	<input type=hidden id=valeur_p value=<?php echo $proj; ?>>
	<?php
}

//Fin de la préparation des variables de recherche pour leur utilisation en JS

?>

<!-- Titre de la page -->

<h1 class = "title font-weight-bold" align="center"><i title="title of search" class="fa fa-fw fa-search"></i>
    <?php echo (isset($a_s)) ? 'Search results' : 'Advanced Search' ?>
</h1>

<!-- Fin du titre de la page -->


<!-- Partie recherche -->

<div class="card">
  <div class="card-body">
	<?php if ($flash_error): ?>
	<div class="alert alert-danger">
		<?php echo $flash_error; ?>
	</div>
  <?php endif; ?>
	<form action="<?php echo $link_search ?>">
		<label>Execute a query in a collection:</label>
		<input type="hidden" name="action" value="advancedSearch">
		<input type="hidden" name="serve" value='<?php echo $serve ?>'>
		<input type="hidden" name="db" value='<?php echo $db ?>'>
		<input type="hidden" name="coll" value='<?php echo $coll ?>'>
		<?php if(isset($query) and isset($proj) and isset($a_s_coll)){ ?>
			<div id="form_a_s">
				db.<select id="a_s_coll" name="a_s_coll">
					<option value="<?php echo $a_s_coll ?>" selected="selected"><?php echo $a_s_coll ?></option>
					<?php foreach ($tabcollections as $collection) { ?>
						<option value="<?php echo $collection ?>"><?php echo $collection ?></option>
					<?php } ?>
				</select>.find(<br>
				<input type="text" id="query" name="query" value ="<?php echo htmlspecialchars($query)?>"/>,<input type="text" id="proj" name="proj" value = "<?php echo htmlspecialchars($proj)?>"/>
				)
			</div>
		<?php }
		else{?>
			<div id="form_a_s">
				db.<select id="a_s_coll" name="a_s_coll">
					<option value="<?php echo $a_s_coll ?>" selected="selected"><?php echo $a_s_coll ?></option>
					<?php foreach ($tabcollections as $collection) { ?>
						<option value="<?php echo $collection ?>"><?php echo $collection ?></option>
					<?php } ?>
				</select>.find(
				<br>
				<input type="text" id="query" name="query" value="{}">,<input type="text" id="proj" name="proj" value="{}">
				<br>
				)
			</div>
		<?php } ?>
		<div class="text-right mt-1">
			<input type="submit" class="btn btn-success" value="Execute">
			<a class="btn bg-secondary mr-2 text-light" href="<?php echo $link_reinit; ?>"><i title="reset" class="fa fa-fw fa-remove"></i></a>
		</div>
	</form>
   </div>
</div>

<!-- Fin de la partie recherche -->


<!-- Tableau des résulats -->

<?php if(isset($query) and isset($proj) and isset($a_s_coll)){?>
	<div id="DivContentTable">
			<div id='result'>
				<div id="head_content">
					<h5 align="center">Search results <?php echo (1+(($page-1)*$bypage))?> -
					<?php if(($page*$bypage)<$nbDocs){echo $page*$bypage;}
					else{echo $nbDocs;} ?>
					of <?php echo $nbDocs ?> :</h5>
					<div class="dropdown">
					  <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						  <i class="text-light fa fa-fw fa-download"></i>
					  </button>
					  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
					    <button class="dropdown-item" <?php if (!isset($docs)): ?>disabled=disabled<?php endif; ?> id="export_csv" href="#">CSV</button>
					    <button class="dropdown-item" <?php if (isset($docs)): ?>disabled=disabled<?php endif; ?>  id="export_json" href="#">JSON</button>
					  </div>
					</div>
				</div>

				<!-- tableau des resultats -->
                <?php include('layouts/advancedSearch/tableAdvancedSearch.php'); ?>
                <!-- fin tableau des resultats -->
				
				
				<!-- Lien de téléchargement du JSON -->

				<a id="send_json" href="<?php echo $link_json;?>"></a>

				<!-- Fin du lien de téléchargement du JSON -->


				<!-- Lien de téléchargement du JSON -->

				<a id="send_csv" href="<?php echo $link_csv;?>"></a>

				<!-- Fin du lien de téléchargement du JSON -->


				<div style="width: 100%;">
				

				<div class="row  justify-content-between  mt-3 mx-1">

					<!-- Bouton de retour -->

					<div>
						<a href="index.php?action=getCollection&serve=<?php echo $serve.'&db='.$db.'&coll='.$coll.'&page='.$page ?>"><button class="return btn btn-primary">< list of docs</button></a>
					</div>

					<!-- Fin du bouton de retour -->

					<!-- Pagination -->
				
					<?php include('layouts/advancedSearch/paginationAdvancedSearch.php'); ?>

			    	<!-- Fin de la pagination -->

			    	<!-- Bouton nouveau document -->

					<div class="ml-2">
						<button class="btn btn-dark py-1 font-weight-bold"><a class="text-light" href="index.php?action=createDocument&serve=<?php echo $serve.'&db='.$db.'&coll='.$coll.'&a_s_coll='.$a_s_coll.'&query='.urlencode($query).'&proj='.urlencode($proj)?>"><i title="Create new doc"class="fa fa-fw fa-plus"></i><i title="Create new doc" class="fa fa-file-text-o"></i></a></button>
					</div>

					<!-- Fin du bouton nouveau document -->
			    </div>

			</div>
		</div>
	</div>
	<br>
<?php } ?>


<!-- Fin du tableau des résultats -->


<!-- footer -->

<?php
	require_once('layouts/footer.php')
?>

   <!-- footer -->

</body>
</html>

<script type="text/javascript">

	function download_json()
	{
		var element = document.getElementById('send_json');

	    element.click();
	}

  document.querySelector("#export_csv").addEventListener("click", function () {
   var element = document.getElementById('send_csv');

	element.click();

  });

  document.querySelector("#export_json").addEventListener("click", download_json);

  // Fin de l'export CSV

</script>
