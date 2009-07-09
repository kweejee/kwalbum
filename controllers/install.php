<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jul 8, 2009
 * @package kwalbum
 * @since 3.0 Jul 8, 2009
 */



class Install_Controller extends Kwalbum_Controller
{
	private $user = array('maxNameLength' => 45);
	function index()
	{
		$this->template->title = 'Install';

		// check if setup should continue
		try
		{
			$user = new User_Model();
			if ($user->get_name(1))
			{
				$view = new View('install/3');
				$this->template->content = $view;
				return;
			}
		}
		catch (Exception $e)
		{
		}

		// setup start
		if (empty($_POST['action']))
		{
			$view = new View('install/1');
			$this->template->content = $view;
			return;
		}

		// setup finish
		$view = new View('install/2');
		$this->template->content = $view;

		$post = Validation::factory($_POST)
			->pre_filter('trim')
			->pre_filter('htmlspecialchars')
			->add_rules('openid', 'required')
			->add_rules('name', 'required')
			->add_rules('name', 'length[2,'.$this->_user['maxNameLength'].']')
			->add_rules('name', 'standard_text');

		$name = $post->('name');
		//$name = $this->input->xss_clean($name);
		$this->template->content = $name;
		return;
		$db = Database::instance();
		//$sql = 'DROP TABLE IF EXISTS '.Kohana::config('kwalbum.dbtables.locations');
		//$db->query($sql);
		$sql = 'CREATE TABLE IF NOT EXISTS '.Kohana::config('kwalbum.dbtables.locations').'(
			  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
			  `name` VARCHAR('.$this->_user['maxNameLength'].') NOT NULL ,
			  `coordinates` POINT NOT NULL ,
			  `count` SMALLINT UNSIGNED NOT NULL DEFAULT 0 ,
			  PRIMARY KEY (`id`) ,
			  FULLTEXT INDEX `location` (`name` ASC) ,
			  SPATIAL INDEX `coordinates` (`coordinates` ASC) )
			ENGINE = MyISAM
			DEFAULT CHARACTER SET = utf8';
		$db->query($sql);

		//$sql = 'DROP TABLE IF EXISTS '.Kohana::config('kwalbum.dbtables.users');
		//$db->query($sql);
		$sql = 'CREATE  TABLE IF NOT EXISTS '.Kohana::config('kwalbum.dbtables.users').'(
			  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
			  `name` TINYTEXT NOT NULL ,
			  `openid` TINYTEXT NOT NULL ,
			  `visit_dt` DATETIME NOT NULL ,
			  `permission_level` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
			  FULLTEXT INDEX `name` (`name` ASC) ,
			  PRIMARY KEY (`id`) )
			ENGINE = MyISAM
			DEFAULT CHARACTER SET = utf8
			PACK_KEYS = DEFAULT;';
		$db->query($sql);
/*
-- -----------------------------------------------------
-- Table `mydb`.`kitems`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`kitems` ;

CREATE  TABLE IF NOT EXISTS `mydb`.`kitems` (
  `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `type_id` TINYINT UNSIGNED NOT NULL ,
  `user_id` SMALLINT UNSIGNED NOT NULL ,
  `location_id` SMALLINT UNSIGNED NOT NULL ,
  `visible_dt` DATETIME NOT NULL ,
  `sort_dt` DATETIME NOT NULL ,
  `description` TEXT NOT NULL ,
  `path` TINYTEXT NOT NULL ,
  `filename` TINYTEXT NOT NULL ,
  `has_comments` TINYINT NOT NULL DEFAULT 0 ,
  `hide_level` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `count` SMALLINT UNSIGNED NOT NULL DEFAULT 0 ,
  `coordinates` POINT NOT NULL ,
  `update_dt` DATETIME NOT NULL ,
  `create_dt` DATETIME NOT NULL ,
  `is_external` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `location_id` (`location_id` ASC) ,
  INDEX `user_id` (`user_id` ASC) ,
  SPATIAL INDEX `coordinates` (`coordinates` ASC) ,
  INDEX `sort_dt` (`sort_dt` ASC) ,
  CONSTRAINT `location_id`
    FOREIGN KEY (`location_id` )
    REFERENCES `mydb`.`klocations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `mydb`.`kusers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`ktags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`ktags` ;

CREATE  TABLE IF NOT EXISTS `mydb`.`ktags` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` TINYTEXT NOT NULL ,
  `count` SMALLINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  FULLTEXT INDEX `tag` (`name` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`kitems_ktags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`kitems_ktags` ;

CREATE  TABLE IF NOT EXISTS `mydb`.`kitems_ktags` (
  `item_id` MEDIUMINT UNSIGNED NOT NULL ,
  `tag_id` SMALLINT UNSIGNED NOT NULL ,
  INDEX `item_id` (`item_id` ASC) ,
  INDEX `tag_id` (`tag_id` ASC) ,
  PRIMARY KEY (`item_id`, `tag_id`) ,
  CONSTRAINT `item_id`
    FOREIGN KEY (`item_id` )
    REFERENCES `mydb`.`kitems` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `tag_id`
    FOREIGN KEY (`tag_id` )
    REFERENCES `mydb`.`ktags` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`kpersons`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`kpersons` ;

CREATE  TABLE IF NOT EXISTS `mydb`.`kpersons` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` TINYTEXT NOT NULL ,
  `count` SMALLINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  FULLTEXT INDEX `person` (`name` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`kitems_kpersons`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`kitems_kpersons` ;

CREATE  TABLE IF NOT EXISTS `mydb`.`kitems_kpersons` (
  `item_id` MEDIUMINT UNSIGNED NOT NULL ,
  `person_id` SMALLINT UNSIGNED NOT NULL ,
  INDEX `item_id` (`item_id` ASC) ,
  INDEX `person_id` (`person_id` ASC) ,
  PRIMARY KEY (`item_id`, `person_id`) ,
  CONSTRAINT `item_id`
    FOREIGN KEY (`item_id` )
    REFERENCES `mydb`.`kitems` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `person_id`
    FOREIGN KEY (`person_id` )
    REFERENCES `mydb`.`kpersons` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`kfavorites`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`kfavorites` ;

CREATE  TABLE IF NOT EXISTS `mydb`.`kfavorites` (
  `user_id` SMALLINT UNSIGNED NOT NULL ,
  `item_id` MEDIUMINT UNSIGNED NOT NULL ,
  `add_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  INDEX `add_ts` (`add_ts` ASC) ,
  PRIMARY KEY (`user_id`, `item_id`) ,
  CONSTRAINT `fk_Users_has_Items_Users`
    FOREIGN KEY (`user_id` )
    REFERENCES `mydb`.`kusers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Users_has_Items_Items`
    FOREIGN KEY (`item_id` )
    REFERENCES `mydb`.`kitems` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`kcomments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`kcomments` ;

CREATE  TABLE IF NOT EXISTS `mydb`.`kcomments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `item_id` MEDIUMINT UNSIGNED NOT NULL ,
  `name` TINYTEXT NULL DEFAULT NULL ,
  `text` TEXT NULL DEFAULT NULL ,
  `added_dt` DATETIME NULL DEFAULT NULL ,
  `ip` CHAR(15) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `item_id` (`item_id` ASC) ,
  CONSTRAINT `item_id`
    FOREIGN KEY (`item_id` )
    REFERENCES `mydb`.`kitems` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`ksites`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`ksites` ;

CREATE  TABLE IF NOT EXISTS `mydb`.`ksites` (
  `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `url` VARCHAR(100) NOT NULL ,
  `key` VARCHAR(45) NOT NULL ,
  `import_dt` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`kexternals`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`kexternals` ;

CREATE  TABLE IF NOT EXISTS `mydb`.`kexternals` (
  `item_id` MEDIUMINT UNSIGNED NOT NULL ,
  `externals_sites` TINYINT UNSIGNED NOT NULL ,
  `external_item_id` MEDIUMINT UNSIGNED NOT NULL ,
  INDEX `PRIMARY` (`item_id` ASC) ,
  PRIMARY KEY (`item_id`) ,
  INDEX `externals_sites` (`externals_sites` ASC) ,
  INDEX `external_item_id` (`external_item_id` ASC) ,
  CONSTRAINT `PRIMARY`
    FOREIGN KEY (`item_id` )
    REFERENCES `mydb`.`kitems` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `externals_sites`
    FOREIGN KEY (`externals_sites` )
    REFERENCES `mydb`.`ksites` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;
*/
		//$user->insert('test name', 'test.open.id');
	}
}