<?php
namespace wcf\data\wow\character;
use wcf\data\DatabaseObjectEditor;
use wcf\data\guild\Guild;
use wcf\system\WCF;

/**
 * Provides functions to edit WoW Charackters.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class WowCharacterEditor extends DatabaseObjectEditor {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = WowCharacter::class;



	/**
     * Changes the user Rank
     *
     * @param	integer		$groupIDs

     */
    public function changeRank($newRank) {
        $guild = new Guild();
        $this->removeFromGroups($guild->getGuildGroupIds(true));
        $this->addToGroup($guild->getGroupfromRank($newRank));
    }

	/**
     * Adds a user to the groups he should be in.
     *
     * @param	array		$groupIDs
     * @param	boolean		$deleteOldGroups
     * @param	boolean		$addDefaultGroups
     */
	public function addToGroups(array $groupIDs, $deleteOldGroups = true, $addDefaultGroups = true) {
        if ($addDefaultGroups) {
            $guild = new Guild();
            $defaultGroup = $guild->getGroupfromRank($this->guildRank);
            $groupIDs = array_merge($groupIDs, $defaultGroup);
            $groupIDs = array_unique($groupIDs);
        }

		// remove old groups
		if ($deleteOldGroups) {
			$sql = "DELETE FROM	wcf".WCF_N."_gman_char_to_group
				WHERE		userID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$this->charID]);
		}

		// insert new groups
		if (!empty($groupIDs)) {
			$sql = "INSERT IGNORE INTO	wcf".WCF_N."_gman_char_to_group
							(userID, groupID)
				VALUES			(?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			foreach ($groupIDs as $groupID) {
				$statement->execute([$this->charID, $groupID]);
			}
		}
	}

	/**
     * Adds a user to a user group.
     *
     * @param	integer	$groupID
     */
	public function addToGroup($groupID) {
		$sql = "INSERT IGNORE INTO	wcf".WCF_N."_gman_char_to_group
						(userID, groupID)
			VALUES			(?, ?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->charID, $groupID]);
	}

	/**
     * Removes a user from a user group.
     *
     * @param	integer		$groupID
     */
	public function removeFromGroup($groupID) {
		$sql = "DELETE FROM	wcf".WCF_N."_gman_char_to_group
			WHERE		userID = ?
					AND groupID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->charID, $groupID]);
	}

	/**
     * Removes a user from multiple user groups.
     *
     * @param	array		$groupIDs
     */
	public function removeFromGroups(array $groupIDs) {
		$sql = "DELETE FROM	wcf".WCF_N."_gman_char_to_group
			WHERE		userID = ?
					AND groupID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		foreach ($groupIDs as $groupID) {
			$statement->execute([
				$this->charID,
				$groupID
			]);
		}
	}
	/**
     * Removes a user from multiple user groups.
     *
     * @param	array		$groupIDs
     */
	public function removeFromAllGroups() {
		$sql = "DELETE FROM	wcf".WCF_N."_gman_char_to_group
			    WHERE		userID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->charID]);
	}


}