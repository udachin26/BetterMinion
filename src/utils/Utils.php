<?php

declare(strict_types=1);

namespace Mcbeany\BetterMinion\utils;

use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;

/**
 * BetterMinion Utils, store some cool functions
 */
final class Utils{
	/**
	 * Parse Item from string.
	 *
	 * @param string $input Input can be item's name, id:meta.
	 * Example: "diamond", "69:420".
	 */
	public static function parseItem(string $input) : ?Item{
		/** @var StringToItemParser $parser */
		$parser = StringToItemParser::getInstance();
		/** @var LegacyStringToItemParser $legacyParser */
		$legacyParser = LegacyStringToItemParser::getInstance();
		try {
			return $parser->parse($input) ?? $legacyParser->parse($input);
		} catch (LegacyStringToItemParserException) {
			return null;
		}
	}

	/**
	 * Give player item, if player's inventory is full, drop it.
	 *
	 * @return bool True if item is not dropped and false otherwise.
	 */
	public static function giveItem(Player $player, Item $item) : bool{
		if($player->getInventory()->canAddItem($item)){
			$player->getInventory()->addItem($item);
			return true;
		}
		$player->dropItem($item);
		return false;
	}
}
