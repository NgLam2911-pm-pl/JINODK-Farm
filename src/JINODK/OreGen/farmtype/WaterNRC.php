<?php

namespace JINODK\OreGen\farmtype;

use JINODK\OreGen\Main;
use pocketmine\event\Listener;
use pocketmine\block\Block;

class WaterNRC implements Listener
{
	private $plugin;
	
	public function __construct($plugin)
	{
		$this->plugin = $plugin;
	}
	public function random()
	{
		$chance = array_rand($this->plugin->oreList,1);
            switch($this->plugin->oreList[$chance])
			{
			case "CoalBlock":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			case "IronBlock":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			case "GoldBlock":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			case "LapisBlock":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			case "RedstoneBlock":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			case "EmeraldBlock":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			case "DiamondBlock":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			case "CoalOre":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			case "IronOre":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			case "GoldOre":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			case "LapisOre":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			case "RedstoneOre":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			case "EmeraldOre":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			case "DiamondOre":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			default:
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
			}
		return $placeBlock;	
	}
}