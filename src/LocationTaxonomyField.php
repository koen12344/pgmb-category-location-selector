<?php

use PGMB\Taxonomy\TaxonomyField;

class LocationTaxonomyField extends TaxonomyField {

	protected $field_types = [
		'singlecheck',
		'business_selector'
	];

	protected function draw_input($value = null){
		$selector = new \PGMB\Components\BusinessSelector(\MBP_api::getInstance());
		$selector->set_field_name($this->field_id);
		if($value){
			$selector->set_selected_locations($value);
		}

		echo $selector->generate();
	}


}
