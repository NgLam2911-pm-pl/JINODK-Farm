<?php 

namespace LamPocketVN\Farm;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\block\Fence;
use pocketmine\block\Block;
use pocketmine\item\Item;

class EventListener implements Listener
{
	private $plugin;
	
	public function __construct (Farm $plugin)
	{
		$this->plugin = $plugin;
	}
	public function onJoin (PlayerJoinEvent $event)
	{
		$this->plugin->setTapFence($event->getPlayer(), false);
		$this->plugin->view[$event->getPlayer()->getName()] = false;
	}
	public function onTap (PlayerInteractEvent $event)
	{
		$player = $event->getPlayer();
		if ($this->plugin->isTapFence($event->getPlayer()) == true)
		{
			$block = $event->getBlock();
			if ($block instanceof Fence)
			{
				$this->plugin->UpgradeFence($block, $player);
				$this->plugin->setTapFence($player, false);
			}
			else
			{
				$event->getPlayer()->sendMessage($this->plugin->getSetting()['msg']['not-tap-fence']);
			}
		}
		if ($this->plugin->view[$event->getPlayer()->getName()] == true)
		{
			$block = $event->getBlock();
			if ($block instanceof Fence)
			{
				$msg = str_replace("{tier}", $this->plugin->getTier($block), $this->plugin->getSetting()['msg']['tier-result']);
				$event->getPlayer()->sendMessage($msg);
				$this->plugin->view[$event->getPlayer()->getName()] = false;
			}
			else
			{
				$event->getPlayer()->sendMessage($this->plugin->getSetting()['msg']['not-tap-fence']);
			}
		}
	}
	public function onBlockSet(BlockUpdateEvent $event)
	{
		$fencePresent = false;
        $block = $event->getBlock();
		if ($block->getId() == 8)
		{
			for ($target = 0; $target <= 5; $target++) {
                $blockSide = $block->getSide($target);
				
                if ($blockSide instanceof Fence) 
				{
                    $fencePresent = true;
					$tier = $this->plugin->getTier($blockSide);
                }
				if ($fencePresent)
				{
					$list = $this->plugin->chance[$tier];
					$ore = array_rand($list,1);
					$block->getLevel()->setBlock($block, Block::get($list[$ore]), false, false);
				}
			}
		}
    }
	public function onPlace(BlockPlaceEvent $event)
	{
		$block = $event->getBlock();
		$item = $event->getItem();
		if ($block instanceof Fence)
		{
			if (isset($item->getLore()[0]))
			{
				$lore = explode(" ", $item->getLore()[0]);
				$tier = $lore[1];
				$this->plugin->setTier($block, $tier);
			}
		}
	}
	public function onBreak(BlockBreakEvent $event)
	{
		$block = $event->getBlock();
		$player = $event->getPlayer();
		if ($block instanceof Fence)
		{
			if ($this->plugin->getTier($block) > 0)
			{
				$tier = $this->plugin->getTier($block);
				$item = Item::get(85,0,1);
				$setlore = str_replace("{tier}", $tier, $this->plugin->getSetting()['item']['lore']);
				$item->setLore([$setlore]);
				$player->getInventory()->addItem($item);
				$event->setDrops([]);
				$this->plugin->setTier($block, null);
			}
		}
	}
}