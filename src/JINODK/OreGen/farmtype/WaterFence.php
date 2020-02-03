<?php

namespace JINODK\OreGen\farmtype;

use JINODK\OreGen\Main;
use pocketmine\event\Listener;
use pocketmine\block\Block;

class WaterFence implements Listener
{
	private $plugin;
	
	public function __construct($plugin){
		$this->plugin = $plugin;
	}
	
	public function random()
	{
		$chance = array_rand($this->plugin->oreList,1);
            switch($this->plugin->oreList[$chance])
			{
            case "CoalBlock":
				$placeBlock = Block::get(Block::COAL_BLOCK);
				break;
			case "IronBlock":
				$placeBlock = Block::get(Block::IRON_BLOCK);
				break;
			case "GoldBlock":
				$placeBlock = Block::get(Block::GOLD_BLOCK);
				break;
			case "LapisBlock":
				$placeBlock = Block::get(Block::LAPIS_BLOCK);
				break;
			case "RedstoneBlock":
				$placeBlock = Block::get(Block::REDSTONE_BLOCK);
				break;
			case "EmeraldBlock":
				$placeBlock = Block::get(Block::EMERALD_BLOCK);
				break;
			case "DiamondBlock":
				$placeBlock = Block::get(Block::DIAMOND_BLOCK);
				break;
			case "CoalOre":
				$placeBlock = Block::get(Block::COAL_ORE);
				break;
			case "IronOre":
				$placeBlock = Block::get(Block::IRON_ORE);
				break;
			case "GoldOre":
				$placeBlock = Block::get(Block::GOLD_ORE);
				break;
			case "LapisOre":
				$placeBlock = Block::get(Block::LAPIS_ORE);
				break;
			case "RedstoneOre":
				$placeBlock = Block::get(Block::REDSTONE_ORE);
				break;
			case "EmeraldOre":
				$placeBlock = Block::get(Block::EMERALD_ORE);
				break;
			case "DiamondOre":
				$placeBlock = Block::get(Block::DIAMOND_ORE);
				break;
			default:
				$placeBlock = Block::get(Block::COBBLESTONE);
			}
		return $placeBlock;	
	}
}
