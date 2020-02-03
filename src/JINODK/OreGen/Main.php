<?php
// Tier Farm are in dev ...

namespace JINODK\OreGen;

use pocketmine\plugin\PluginBase;
use pocketmine\block\{Block,Water,Lava,Fence,Bedrock,Diamond,NETHERREACTOR};
use pocketmine\utils\config;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\Listener;

use JINODK\OreGen\farmtype\WaterFence;
use JINODK\OreGen\farmtype\WaterLava;
use JINODK\OreGen\farmtype\BedrockDiamond;
use JINODK\OreGen\farmtype\WaterNRC;

class Main extends PluginBase implements Listener{
    
    private $config;
    public $oreList = [];
	private $wf, $wl, $bd, $wn;

	public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $this->config->getAll();
		
		$this->wf = new WaterFence($this);
		$this->wl = new WaterLava($this);
		$this->bd = new BedrockDiamond($this);
		$this->wn = new WaterNRC($this);
		
        $this->getLogger()->info("OreGen has been enabled!");
        if($this->config->get("World-List") !== null && $this->config->get("World-List") !== []){
            if($this->config->get("List-Mode") == null || $this->config->get("List-Mode") == ""){
                $this->getLogger()->error("The list mode cannot be left null! Please choose either 'Blacklist' or 'Whitelist'! Disabling plugin...");
                $this->getServer()->getPluginManager()->disablePlugin($this);
                return false;
            }
        }
        $this->buildProbability();
	}

    public function buildProbability() : bool{
        $list = [];
        $cobbleProb = $this->config->get("CobbleChance");
        if(!is_numeric($cobbleProb)){
            $this->getLogger()->error("Cobblestone chance must be numerical! Disabling plugin...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return false;
        }
        for($i=0;$i<$cobbleProb;$i++){
            $this->oreList = array_push($list,'Cobble');
        }
        $sum = $cobbleProb;
        $ores = ['CoalBlock','IronBlock','GoldBlock','LapisBlock','RedstoneBlock','EmeraldBlock','DiamondBlock','CoalOre','IronOre','GoldOre','LapisOre','RedstoneOre','EmeraldOre','DiamondOre'];
        foreach($ores as $ore){
            $enabled = $this->config->getNested($ore.".Enabled");
            if($enabled === true){
                $chance = $this->config->getNested("$ore.Chance");
                if(is_numeric($chance)){
                    $sum = $sum + $chance;
                    for($i=0;$i<$chance;$i++){
                        $this->oreList = array_push($list,$ore);
                    }
                }
                else{
                    $this->getLogger()->warning("'".$ore."' has an invalid value, it will be disabled!");
                }
            }
            elseif($enabled !== false){
                $this->getLogger()->warning("'".$ore."' has an invalid value, it will be disabled!");
            }
        }
        $this->oreList = $list;
        if($sum != 100000){
            $this->getLogger()->error("Chance has a sum of ".$sum);
            $this->getLogger()->error("Chance must have a sum equal to 100000! Disabling plugin...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return false;
        }
        return true;
    } 

    public function onBlockSet(BlockUpdateEvent $event){
        if($this->config->get("World-List") !== false){
            if($this->config->get("List-Mode") == "Whitelist"){
                if(!in_array($event->getBlock()->getLevel()->getName(), $this->config->get("World-List"))){
                    return;
                }
            }
            elseif($this->config->get("List-Mode") == "Blacklist"){
                if(in_array($event->getBlock()->getLevel()->getName(), $this->config->get("World-List"))){
                    return;
                }
            }
        }
        $this->blockSet($event);
    }

    public function blockSet($event)
	{
        $block = $event->getBlock();
        $waterPresent = false;
        $lavaPresent = false;
		$fencePresent = false;
		$NetherreactorPresent = false;
		$bedrockPresent = false;
		$diamondPresent = false;
        if ($block->getId() == 4){
            for ($target = 2; $target <= 5; $target++) {
                $blockSide = $block->getSide($target);
                if ($blockSide instanceof Water) {
                    $waterPresent = true;
                }
				if ($blockSide instanceof Flowing_Water) {
                    $waterPresent = true;
                }
                if ($blockSide instanceof Flowing_Lava) {
                    $lavaPresent = true;
                }
				if ($blockSide instanceof Lava) {
                    $lavaPresent = true;
                }
                if ($waterPresent && $lavaPresent) {
                    $block->getLevel()->setBlock($block, $this->wl->random(), false, false);
                    return true;
                }
            }
        }
		if ($block->getId() == 8){
            for ($target = 0; $target <= 5; $target++) {
                $blockSide = $block->getSide($target);
				
                if ($blockSide instanceof Fence) 
				{
                    $fencePresent = true;
                }
				if ($blockSide instanceof Bedrock) {
                    $bedrockPresent = true;
                }
				if ($blockSide instanceof Diamond) {
					$diamondPresent = true;
				}
				if ($blockSide instanceof NETHERREACTOR) {
					$NetherreactorPresent = true;
				}
                if ($fencePresent) 
				{
                    $block->getLevel()->setBlock($block, $this->wf->random(), false, false);
                    return true;
                }
				if ($bedrockPresent && $diamondPresent) {
                    $block->getLevel()->setBlock($block, $this->bd->random(), false, false);
                    return true;
                }
				if ($NetherreactorPresent) 
				{
                    $block->getLevel()->setBlock($block, $this->wn->random(), false, false);
                    return true;
                }
			}
        }
    }
}
