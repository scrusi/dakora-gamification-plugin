<?php
namespace local\gamification;
class response {
	
	function __construct($type,$content=false,$id=0) {
		$this->type = $type;
		$this->content = $content;
		$this->id = $id;
	}
}