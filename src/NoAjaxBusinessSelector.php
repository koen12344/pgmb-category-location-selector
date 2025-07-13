<?php

class PGMB_NoAjaxBusinessSelector extends \PGMB\Premium\Components\MultiAccountBusinessSelector {
	public function generate(){
		if(!$this->selected){
			$this->selected = $this->default_location;
		}
		return "<div class=\"mbp-business-selector\" data-field_name=\"{$this->field_name}\"><table>{$this->account_rows()}</table></div>";
	}
}
