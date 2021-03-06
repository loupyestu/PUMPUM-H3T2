<?php 

class RecipeController extends Controller{

	public function __construct(){
		parent::__construct();
	}

	function conciergerie($f3){
		$toCall = array('ambiances','preparationTimes','difficulties','types','allIngredients');
		foreach ($toCall as $value) {
			$s = "get".ucfirst($value);
			$f3->set($value,$this->model->$s());
		}
		$this->tpl['sync']='conciergerie.html';
	}

	function submitRecipe($f3){
		if(!$f3->exists('SESSION.id_user'))
			$f3->reroute('/user/register?e');
		switch ($f3->get('VERB')) {
			case 'POST':
				$params = $f3->get('POST');
				$params['id_user'] = $f3->get('SESSION.id_user');
				$recipe = $this->model->submitRecipe($params);

				//Si l'enregistrement a réussi on redirige l'user vers sa recette
				if($recipe){						
					$f3->reroute('/recipe/getRecipe/'.$recipe->id_recipe);
				} 
			break;
			
			case 'GET':
				$toCall = array('ambiances','preparationTimes','difficulties','types','allIngredients');
				foreach ($toCall as $value) {
					$s = "get".ucfirst($value);
					$f3->set($value,$this->model->$s());
				}
				$this->tpl['sync']='Recipe/submitRecipe.html';
		}
		
	}

	function getRecipe($f3){
		$f3->set('recipe',$this->model->getRecipe($f3->get('PARAMS')));
		$f3->set('steps',$this->model->getSteps($f3->get('PARAMS')));

		$f3->set('ingredients',$this->model->getIngredients($f3->get('PARAMS')));
		$f3->set('ambiance',$this->model->getAmbiance($f3->get('PARAMS')));
		$f3->set('author',$this->model->getAuthor($f3->get('PARAMS')));

		$f3->set('PARAMS.id_user', $f3->get('SESSION.id_user'));
		$f3->set('isFavorite',$this->model->getIsFavorite($f3->get('PARAMS')));
		$this->tpl['sync']='Recipe/viewRecipe.html';
	}

	function editRecipe($f3){
		if(!$f3->exists('SESSION.id_user'))
			$f3->reroute('/user/register?e');
		
		switch ($f3->get('VERB')) {
			case 'POST':
				if($f3->get('POST.id_user') != $f3->get('SESSION.id_user'))
						$f3->error(403); 

				$recipe = $this->model->editRecipe($f3->get('POST'));
				//Si l'enregistrement a réussi on redirige l'user vers sa recette
				if($recipe){						
					$f3->reroute('/recipe/getRecipe/'.$recipe->id_recipe);
				} 
			break;
			case 'GET':
				$f3->set('author',$this->model->getAuthor($f3->get('PARAMS')));
				if($f3->get('author')->id_user != $f3->get('SESSION.id_user'))
						$f3->error(403); 

				$f3->set('recipe',$this->model->getRecipe($f3->get('PARAMS')));
				$f3->set('steps',$this->model->getSteps($f3->get('PARAMS')));
				$f3->set('ingredients',$this->model->getIngredients($f3->get('PARAMS')));
				
				$f3->set('PARAMS.id_user', $f3->get('SESSION.id_user'));
				$f3->set('isFavorite',$this->model->getIsFavorite($f3->get('PARAMS')));


				$toCall = array('ambiances','preparationTimes','difficulties','types','allIngredients');
				foreach ($toCall as $value) {
					$s = "get".ucfirst($value);
					$f3->set($value,$this->model->$s());
				}
				$this->tpl['sync']='Recipe/editRecipe.html';
		}
	}

	function getRecipes($f3){
		$toCall = array('ambiances','preparationTimes','difficulties','types','allIngredients');
		foreach ($toCall as $value) {
			$s = "get".ucfirst($value);
			$f3->set($value,$this->model->$s());
		}
		$f3->set('recipes',$this->model->getRecipes($f3->get('PARAMS')));
		$this->tpl['sync']='Recipe/viewRecipe.html';
	}

	function getRecipesByFilter($f3){

		$f3->set('recipes',$this->model->getRecipesByFilter($f3->get('PARAMS')));
		$toCall = array('ambiances','preparationTimes','difficulties','types','allIngredients');
		foreach ($toCall as $value) {
			$s = "get".ucfirst($value);
			$f3->set($value,$this->model->$s());
		}
		$f3->set('allparams', $f3->get('PARAMS'));

		switch ($f3->get('VERB')) {
			case 'POST':
				$this->tpl['async']='partials/recipes.html';
			break;
			case 'GET':
				$this->tpl['sync']='Recipe/viewRecipes.html';
		}
	}	

	function getAllIngredientsJSON($f3){
		$f3->set('allIngredients',$this->model->getAllIngredients());
		$this->tpl['async']='Recipe/allIngredients.json';
	}

	function getComments($f3){
		$f3->set('comments',$this->model->getComments($f3->get('PARAMS')));
		$f3->set('step',$f3->get('PARAMS.id_step'));
		$this->tpl['async']='partials/comments.html';
	}
}