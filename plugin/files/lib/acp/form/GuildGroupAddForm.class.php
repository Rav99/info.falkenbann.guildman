<?php
namespace wcf\acp\form;
use calendar\data\category\CalendarCategory;
use calendar\data\category\CalendarCategoryNodeTree;
use wcf\form\AbstractForm;
use wcf\system\condition\ConditionHandler;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\util\ArrayUtil;
use wcf\data\guild\group\GuildGroup;
use wcf\data\guild\group\GuildGroupAction;
use wcf\data\user\group\UserGroup;
use wcf\data\article\Article;
use wcf\data\article\ArticleList;
use wcf\data\media\Media;
use wcf\data\media\ViewableMediaList;
use wbb\data\thread\Thread;
use wbb\data\board\Board;
use wbb\data\board\BoardList;

/**
 * Gruppen hinzufügen
 * @author	    Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	    GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	    info.falkenbann.guildman
 *
 */

class GuildGroupAddForm extends AbstractForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.gman.guildgroupedit';

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.gman.canAddGroups'];

	/**
	 * @inheritDoc
	 */
	public $neededModules = [];

	/**
	 * Name of the Group
	 * @var	string
	 */
	public $groupName = '';

	/**
	 * Group teaser
	 * @var	string
	 */
	public $groupTeaser = '';


	/**
	 * group ID assigned to WCF group
	 * @var	integer
	 */
	public $groupWcfID = 0;

	/**
     * group assigned to WCF group
     * @var	UserGroup;
     */
	public $groupWcf = null;

	/**
	 * show in calendar?
	 * @var	boolean
	 */
	public $showCalender = false;

	/**
	 * Calendar Title
	 * @var	string
	 */
	public $calendarTitle = '';

	/**
     * Calendar Title
     * @var	string
     */
	public $calendarText = '';

	/**
	 * fetch calendar from wow
	 * @var	boolean
	 */
	public $fetchCalendar = false;

	/**
     * query for calendar
     * @var	string
     */
	public $calendarQuery = '';

	/**
	 * Assign to ingame Rank
	 * @var	integer
	 */
	public $gameRank = 11;

	/**
     * Show in roaster
     * @var	boolean
     */
	public $showRoaster = false;

	/**
     * Article ID
     * @var	integer
     */
	public $articIeID = 0;

	/**
     * Thread ID
     * @var	integer
     */
	public $threadID = 0;

    /**
     * Board ID
     * @var	integer
     */
	public $boardID = 0;

    /**
     * Logo Media ID
     * @var	integer[]
     */
    private $imageID = [];

	/**
     * images
     * @var	Media[]
     */
    private $images = [];

	/**
     * is raidgroup
     * @var	boolean
     */
	public $isRaidgruop = false;
    /**
     * fetch data from Warcraftlogs
     * @var	boolean
     */
	public $fetchWCL = false;

	/**
     * WCF Query
     * @var	string
     */
	public $wclQuery = '';

	/**
     * Order
     * @var	integer
     */
	public $orderNo = 0;

	/**
     * category list
     * @var	\RecursiveIteratorIterator
     */
	public $categoryList;


      /**
     * category id for calendar
     * @var	integer
     */
	public $calendarCategoryID = 0;



	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

        $articleList = new ArticleList;
        $articleList->readObjects();
        $articles = $articleList->getObjects();

        $boardList = new BoardList;
        $boardList->readObjects();
        $boards = $boardList->getObjects();


		WCF::getTPL()->assign([
			'action'            => 'add',
			'groupName'         => $this->groupName,
            'groupTeaser'       => $this->groupTeaser,
            'groupWcfID'        => $this->groupWcfID,
            'showCalender'      => $this->showCalender,
            'calendarCategoryID'=> $this->calendarCategoryID,
            'calendarTitle'     => $this->calendarTitle,
            'calendarText'      => $this->calendarText,
            'calendarQuery'     => $this->calendarQuery,
            'categoryList'      => $this->categoryList,
            'gameRank'          => $this->gameRank,
            'showRoaster'       => $this->showRoaster,
            'articIeID'         => $this->articIeID,
            'articleList'       => $articles,
            'boardID'           => $this->boardID,
            'boardList'         => $boards,
            'mediaID'           => $this->mediaID,
            'threadID'          => $this->threadID,
            'isRaidgruop'       => $this->isRaidgruop,
            'fetchWCL'          => $this->fetchWCL,
            'wclQuery'          => $this->wclQuery,
            'orderNo'           => $this->orderNo,
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();

		// get categories
		$excludedCategoryIDs = array_diff(CalendarCategory::getAccessibleCategoryIDs(), CalendarCategory::getAccessibleCategoryIDs(['canUseCategory']));
		$categoryTree = new CalendarCategoryNodeTree('com.woltlab.calendar.category', 0, false, $excludedCategoryIDs);
		$this->categoryList = $categoryTree->getIterator();

	}

	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_POST['groupName']))     $this->groupName        = StringUtil::trim($_POST['groupName']);
        if (isset($_POST['groupTeaser']))   $this->groupTeaser      = StringUtil::trim($_POST['groupTeaser']);
        if (isset($_POST['groupWcfID']))    $this->groupWcfID       = intval($_POST['groupWcfID']);
        if (isset($_POST['showCalender']))  {
        if (WCF::getSession()->getPermission('admin.content.cms.canUseMedia')) {
                if (isset($_POST['imageID']) && is_array($_POST['imageID'])) $this->imageID = ArrayUtil::toIntegerArray($_POST['imageID']);
                $this->readImages();
            }
        $this->showCalender     = true;
            if (isset($_POST['calendarTitle'])) $this->calendarTitle    = StringUtil::trim($_POST['calendarTitle']);
            if (isset($_POST['calendarText']))  $this->calendarText     = StringUtil::trim($_POST['calendarText']);
            if (isset($_POST['calendarQuery'])) $this->calendarQuery    = StringUtil::trim($_POST['calendarQuery']);
            if (!empty($_POST['calendarCategoryID']))   $this->calendarCategoryID       = ArrayUtil::toIntegerArray($_POST['calendarCategoryID'])[0];
        }
        if (isset($_POST['gameRank']))      $this->gameRank         = intval($_POST['gameRank']);
        if (isset($_POST['showRoaster']))   $this->showRoaster      = true;
        if (isset($_POST['articIeID']))     $this->articIeID        = intval($_POST['articIeID']);
        if (isset($_POST['boardID']))       $this->boardID          = intval($_POST['boardID']);
        if (isset($_POST['threadID']))      $this->threadID         = intval($_POST['threadID']);
        if (isset($_POST['isRaidgruop']))   $this->isRaidgruop      = true;
        if (isset($_POST['fetchWCL'])) {
            $this->fetchWCL         = true;
            if (isset($_POST['wclQuery']))      $this->wclQuery         = StringUtil::trim($_POST['wclQuery']);
        }
        if (isset($_POST['orderNo']))       $this->orderNo          = intval($_POST['orderNo']);
	}


    /**
     * Reads the box images.
     */
	protected function readImages() {
		if (!empty($this->imageID)) {
			$mediaList = new ViewableMediaList();
			$mediaList->setObjectIDs($this->imageID);
			$mediaList->readObjects();

			foreach ($this->imageID as $languageID => $imageID) {
				$image = $mediaList->search($imageID);
				if ($image !== null && $image->isImage) {
					$this->images[$languageID] = $image;
				}
			}
		}
	}

	/**
     * @inheritDoc
     */
	public function validate() {
		parent::validate();

		if (empty($this->groupName)) {
			throw new UserInputException('groupName');
		}
        if (strlen($this->groupName) > 50) {
            throw new UserInputException('groupName', 'toolong');
        }
        if (strlen($this->groupName) < 5) {
            throw new UserInputException('groupName', 'tooshort');
        }
        if (strlen($this->groupTeaser) > 250) {
            throw new UserInputException('groupTeaser', 'toolong');
        }
		if ($this->groupWcfID > 0) {
            $this->groupWcf = new UserGroup($this->groupWcfID);
            if ($this->groupWcf->getObjectID()==0) throw new UserInputException('groupWcfID');
		}

        if ($this->showCalender) {
            if (empty($this->calendarCategoryID)) {
                throw new UserInputException('categoryID');
            }
            $category = CalendarCategory::getCategory($this->calendarCategoryID);
            if ($category === null) {
                throw new UserInputException('categoryID', 'invalid');
            }
            if (!$category->isAccessible() || !$category->getPermission('canUseCategory')) {
                throw new UserInputException('categoryID', 'invalid');
            }
        }

        if ($this->articleID > 0) {
            $artcile = new Article($this->articleID);
            if ($artcile === null) {
                throw new UserInputException('articleID', 'notFound');
            }
        }

        if ($this->threadID > 0) {
            $page = new Thread($this->threadID);
            if ($page===null) {
                throw new UserInputException('threadID', 'notFound');
            }
        }

        if ($this->boardID > 0) {
            $board = new Board($this->boardID);
            if ($board===null) {
                throw new UserInputException('boardID', 'notFound');
            }
        }

	}

	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();

		$this->objectAction = new GuildGroupAction([], 'create', [
			'data' =>  [
			    'groupName'         => $this->groupName,
                'groupTeaser'       => $this->groupTeaser,
                'groupWcfID'        => $this->groupWcfID,
                'showCalender'      => $this->showCalender,
                'calendarTitle'     => $this->calendarTitle,
                'calendarText'      => $this->calendarText,
                'calendarQuery'     => $this->calendarQuery,
                'calendarCategoryID'=> $this->calendarCategoryID,
                'gameRank'          => $this->gameRank,
                'showRoaster'       => $this->showRoaster,
                'articIeID'         => $this->articIeID,
                'boardID'           => $this->boardID,
                'imageID'           => $this->imageID[0],
                'threadID'          => $this->threadID,
                'isRaidgruop'       => $this->isRaidgruop,
                'fetchWCL'          => $this->fetchWCL,
                'wclQuery'          => $this->wclQuery,
                'orderNo'           => $this->orderNo,
                'lastUpdate'        => TIME_NOW
			]
		]);
		$this->objectAction->executeAction();

		$this->saved();

		// reset values
		$this->groupName = '';
        $this->groupTeaser = '';
        $this->groupWcfID = 0;
        $this->showCalender = false;
        $this->calendarTitle = '';
        $this->calendarText = '';
        $this->calendarQuerry = '';
        $this->calendarCategoryID = 0;
        $this->gameRank = 11;
        $this->showRoaster = false;
        $this->articIeID = 0;
        $this->boardID = 0;
        $this->imageID = [];
        $this->threadID = 0;
        $this->isRaidgruop = false;
        $this->fetchWCL = false;
        $this->wclQuery = '';
        $this->orderNo = 0;
		WCF::getTPL()->assign('success', true);
	}
}
