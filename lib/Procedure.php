<?php

class Procedure {
	
	private $num;
	private $title;
	private $seq;
	private $type;
	private $fn;
	
	function __construct($num, $type, $title, $seq, $fn) {
		$this->num = $num;
		$this->type = $type;
		$this->title = $title;
		$this->seq = $seq;
		$this->fn = $fn;
	}
	
	public function getNum() {
		return($this->num);
	}
	
	public function getType() {
		return($this->type);
	}
	
	public function getTitle() {
		return($this->title);
	}
	
	public function getSeq() {
		return($this->seq);
	}
	
	public function getFn() {
		return($this->fn);
	}
	
	public function toArray() {
		return(array("num" => $this->num, "type" => $this->type, "title" => $this->title, "seq" => $this->seq));
	}
	
	public function toJSON() {
		return(json_encode($this->toArray));
	}
}
?>