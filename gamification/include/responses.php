<?php
namespace local\gamification;
require_once('response.php');
class responses {
	private $responses;

	function __construct() {
		$this->responses = array();
	}

	function add($r) {
		if (!$r) return;
		if (is_a($r,__NAMESPACE__.'\responses')) {
			if (count($this->responses)) {
				$this->add($r->get());
			} else {
				$this->responses = $r->get();
			}
		}
		else if (is_array($r)) {
			foreach ($r as $res) {
				$this->add($res);
			}
		} elseif (is_a($r, __NAMESPACE__.'\response')) {
			$this->responses[] = $r;	
		} else {
			//echo "that was generic";
			$this->responses[] = new response('generic',$r);
		}
	}

	function get() {
		return $this->responses;
	}
}