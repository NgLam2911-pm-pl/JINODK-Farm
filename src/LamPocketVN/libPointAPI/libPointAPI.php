<?php

namespace LamPocketVN\libPointAPI;

use onebone\PointAPI\PointAPI;
use doramine\economyapi\EconomyAPI;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class libPointAPI extends PluginBase
{
	private $mode;
	
	private $rgs = false;
	
	public static $static;
	
	public static function getStatic()
	{
		return self::$static;
	}
	
	public function Load()
	{
		$author = $this->getServer()->getPluginManager()->getPlugin("PointAPI")->getDescription()->getAuthors()[0];
		if ($author = "onebone#editPhuongaz")
		{
			$this->mode = "money";
		}
		else
		{
			$this->mode = "point";
		}
		$this->rgs = true;
		
	}
	
	public function getPointAPI()
	{
		if ($this->rgs == false)
		{
			$this->Load();
		}
		$author = $this->getServer()->getPluginManager()->getPlugin("PointAPI")->getDescription()->getAuthors()[0];
		if ($author = "onebone#editPhuongaz")
		{
			$this->mode = "money";
			return EconomyAPI;
		}
		else
		{
			$this->mode = "point";
			return PointAPI;
		}
	}
	public function myPoint(Player $player)
	{
		if ($this->mode = "money")
		{
			return $this->getPointAPI()->getInstance()->myMoney($player);
		}
		else
		{
			return $this->getPointAPI()->getInstance()->myPoint($player);
		}
	}
	public function addPoint(Player $player, $point)
	{
		if ($this->mode = "money")
		{
			$this->getPointAPI()->getInstance()->addMoney($player, $point);
		}
		else
		{
			return $this->getPointAPI()->getInstance()->addPoint($player, $point);
		}
	}
	public function reducePoint(Player $player, $point)
	{
		if ($this->mode = "money")
		{
			$this->getPointAPI()->getInstance()->reduceMoney($player, $point);
		}
		else
		{
			return $this->getPointAPI()->getInstance()->reducePoint($player, $point);
		}
	}
	public function setPoint(Player $player, $point)
	{
		if ($this->mode = "money")
		{
			$this->getPointAPI()->getInstance()->setMoney($player, $point);
		}
		else
		{
			return $this->getPointAPI()->getInstance()->setPoint($player, $point);
		}
	}
}