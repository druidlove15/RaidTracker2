<?php
/*******************************************************************************
 * time.php
 *******************************************************************************
 * version 2.5
 * -----------------------------------------------------------------------------
 * the time class functions here
 ******************************************************************************/
class TIME {
	private $timestamp=0;
	public function setTime($tString) {
		$this->timestamp=strtotime($tString);
	}
	public function Stamp () { return $this->timestamp; }
	public function showTime($tFormat) {return date($tFormat,$this->timestamp);}
	public function dbTime() {return $this->showTime("Y-m-d H:i:s");}
	public function __construct($tS='now') {$this->setTime($tS);}
	public function add($day=0,$hr=0,$min=0,$sec=0) {
		$tpass=60*(60*(24*$day+$hr)+$min)+$sec;
		$this->timestamp+=$tpass;
	}
	public function subt($day=0,$hr=0,$min=0,$sec=0) {
		$tpass=60*(60*(24*$day+$hr)+$min)+$sec;
		$this->timestamp-=$tpass;
	}
}
class RAID {
	// these are times using TIME class
	protected $tNow;
	protected $tStart;
	protected $tEnd;
	protected $tInv;
	protected $tFNew;
	protected $tFDel;
	//current status on raid 0-open, 1-f. new 2-f. del 3-inv 4-raid 5-finish
	protected $status=0;
	public function __construct() {
		$this->tnow=new TIME();
		$this->tStart=new TIME();
		$this->tEnd=new TIME();
		$this->tInv=new TIME();
		$this->tFNew=new TIME();
		$this->tFDel=new TIME();
		$this->status=-1;
	}
	function audit() {
		if ($this->tStart > $this->tEnd) {
			$this->status=-1;
			return;
		} else if ($this->tInv > $this->tStart) {
			$this->status=-1;
			return;
		} else if ($this->tFNew > $this->tInv) {
			$this->status=-1;
			return;
		} else if ($this->tFDel > $this->tInv) {
			$this->status=-1;
			return;
		}
		if ($this->tnow > $this->tEnd) $this->status=6;
		else if ($this->tnow > $this->tStart) $this->status=5;
		else if ($this->tnow > $this->tInv) $this->status=4;
		else if ($this->tnow > $this->tFNew && $this->now > $this->FDel) $this->status=3;
		else if ($this->tnow > $this->tFDel) $this->status=2;
		else if ($this->tnow > $this->tFNew) $this->status=1;
		else $this->status=0;
	}
	function settime($tFrame, $tStr) {
		if ($tFrame=='end') {
			$this->tEnd->setTime ($tStr);
		} else if ($tFrame=='start') {
			$this->tStart->setTime ($tStr);
		} else if ($tFrame=='inv') {
			$this->tInv->setTime ($tStr);
		} else if ($tFrame=='new') {
			$this->tFNew->setTime ($tStr);
		} else if ($tFrame=='del') {
			$this->tFDel->setTime ($tStr);
		} else if ($tFrame=='now') {
			$this->tnow->setTime ($tStr);
		}
	}
	function getStatus() {
		$this->audit();
		switch ($this->status) {
			case 6: return "Completed"; break;
			case 5: return "In progress"; break;
			case 4: return "Invites started"; break;
			case 3: return "Frozen"; break;
			case 2: return "Frozen to subscriptions"; break;
			case 1: return "Frozen to withdrawls"; break;
			case 0: return "Open"; break;
			case -1: return "Error: Time errors"; break;
		}
	}
	function getColor($min=60) {
		$this->audit();
		if ($this->status==-1) return -1;
		if ($this->status > 2) return 2;
		if ($this->status > 0) return 1;
		$time=($this->tFNew > $this->tFDel)?$this->tFNew:$this->tFDel;
		if ($this->tnow+$min*60>$time) return 1;
		return 0;
	}
	function showtime($timeclass, $format="Y-m-d H:i:s") {
		if ($timeclass=='end') return $this->tEnd->showtime ($format);
		else if ($timeclass=='start') return $this->tStart->showtime ($format);
		else if ($timeclass=='inv') return $this->tInv->showtime ($format);
		else if ($timeclass=='new') return $this->tFNew->showtime ($format);
		else if ($timeclass=='del') return $this->tFDel->showtime ($format);
		else if ($timeclass=='now') return $this->tnow->showtime ($format);
		else die ("Wrong format: $timeclass");
	}
}
class RAIDINFO extends RAID {
	public $icon;
	public $name;
	public $id;
	public $public_note;
	public $private_note;
	public $active;
	public $roles;
	function load ($dbrec) {
		$this->id=$dbrec['id'];
		$this->tStart->settime($dbrec['date']);
		$this->tEnd->settime($dbrec['endtime']);
		$this->tInv->settime($dbrec['inv']);
		$this->tFNew->settime($dbrec['freezenew']);
		$this->tFDel->settime($dbrec['freezedel']);
		$this->name=$dbrec['instance'];
		$this->icon=$dbrec['icon'];
		$this->public_note=$dbrec['note'];
		$this->private_note=$dbrec['offnote'];
		$this->active=$dbrec['required'];
		$this->roles = explode('/',$dbrec['roles']);
	}
	function getLocation ($iconpath, $full=true) {
		$temp="<img src=\"$iconpath/".$this->icon.".png\" alt=\"".$this->icon."\" />";
		if ($full) $temp .=" ". $this->name;
		return $temp;
	}
}
		
?>