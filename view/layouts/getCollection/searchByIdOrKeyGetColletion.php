<div class="card mb-1">
  <div class="card-body">


	<!-- Formulaire de recherche par id et clé:valeur -->
		
		<div id="searchId">
			<form class="col-md-12" autocomplete="off" action="index.php?action=getCollection_search&serve=<?php echo $serve.'&db='.$db.'&coll='.$coll ?>">
				<div class="input-group mb-1">
					<input type="hidden" name="action" value="getCollection_search">
					<input type="hidden" name="serve" value='<?php echo $serve ?>'>
					<input type="hidden" name="db" value='<?php echo $db ?>'>
					<input type="hidden" name="coll" value='<?php echo $coll ?>'>
					<input type="search" autofocus="autofocus"  list="browsers" placeholder="Search by document id or key:value" required="required"  class="flexdatalist form-control border border-success" name="s_g" id="recherche_g" />

					<!-- Autocomplétion des champs -->

					<datalist id="browsers">
				        <?php foreach ($docs[0] as $key => $value) {
				        		echo  "<option value=".$key.":>";
				        	}
							foreach ($docs as $key => $value) {
			        			echo  "<option value=".$value['_id'].">";
						    } ?>
			 		</datalist>

			 		<!-- Fin de l'autocomplétion des champs -->


					<div class="input-group-append">
					<input class="btn bg-success text-light "  type="submit" id="search" value="Search"/>
					</div>
				</div>
				<div class="text-right">
				<a class="btn btn-link btn-sm" href="?action=advancedSearch&serve=<?php echo $serve ?>&db=<?php echo $db ?>&coll=<?php echo $coll ?>"><i class="fa fa-fw fa-search"></i>Advanced Search</a>
				</div>
			</form>
		</div>

		<!-- Fin du formulaire de recherche par id et clé:valeur -->

  </div>
</div>
