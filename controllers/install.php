<?php
/**
 * Controller for first time installing.
 *
 * It creates tables in the database and adds the first user as an
 * administrator.
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
	private $_user = array('minNameLength' => 2, 'maxNameLength' => 45);

	function index()
	{
		$this->template->title = 'Install';
		$user = new User_Model();
		$db = Database::instance();

		// Uncomment to delete everything and start over
		//$this->_drop_tables($db);

		// Do not continue installation if at least 1 user exists in the database
		try
		{
			if ($user->total > 0)
			{
				$view = new View('install/2');
				$this->template->content = $view;
				return;
			}
		}
		catch (Exception $e){}

		// Continue installation

		$form = array
		(
			'name' => '',
			'openid' => '',
		);

		// Copy the form as errors so the errors will be stored with keys
		// matching the form field names
		$errors = $form;

		if ($_POST)
		{
			$post = Validation::factory($_POST)
				->pre_filter('trim', true)
				->pre_filter('htmlspecialchars')
				->add_rules('openid', 'required')
				->add_rules('name', 'required',
					'standard_text',
					'length['.$this->_user['minNameLength'].','.$this->_user['maxNameLength'].']');

			if ($post->validate())
			{
				$data = $post->as_array();
				$name = $this->input->xss_clean($data['name']);
				$openid = $this->input->xss_clean($data['openid']);

				try
				{
					$this->_create_tables($db);

					$user->insert($name, $openid);
					$this->template->content = new View('install/2');
					return;
				}
				catch (Exception $e)
				{
					$errors = array('db', Kohana::lang("install_form_errors.db"));
				}
			}
			else // Did not validate
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any
				// Pass the error message file name to the errors() method
				// Default error message file is in i18n/en_US/
				$errors = arr::overwrite($errors, $post->errors('install_form_errors'));
			}
		}

		$view = new View('install/1');
		$view->_user = $this->_user;
		$view->form = $form;
		$view->errors = $errors;
		$this->template->content = $view;
		return;
	}

	/** Drop all Kwalbum tables
	 *
	 * @param  Database to connect to
	 * @return void
	 */
	private function _drop_tables($db)
	{
		// Drop order is arranged based on foreign key restraints.
		$sql = 'DROP TABLE IF EXISTS `'.Kohana::config('kwalbum.dbtables.comments').'`';
		$db->query($sql);
		$sql = 'DROP TABLE IF EXISTS `'.Kohana::config('kwalbum.dbtables.favorites').'`';
		$db->query($sql);
		$sql = 'DROP TABLE IF EXISTS `'.Kohana::config('kwalbum.dbtables.items_tags').'`';
		$db->query($sql);
		$sql = 'DROP TABLE IF EXISTS `'.Kohana::config('kwalbum.dbtables.items_persons').'`';
		$db->query($sql);
		$sql = 'DROP TABLE IF EXISTS `'.Kohana::config('kwalbum.dbtables.items_sites').'`';
		$db->query($sql);
		$sql = 'DROP TABLE IF EXISTS `'.Kohana::config('kwalbum.dbtables.tags').'`';
		$db->query($sql);
		$sql = 'DROP TABLE IF EXISTS `'.Kohana::config('kwalbum.dbtables.persons').'`';
		$db->query($sql);
		$sql = 'DROP TABLE IF EXISTS `'.Kohana::config('kwalbum.dbtables.sites').'`';
		$db->query($sql);
		$sql = 'DROP TABLE IF EXISTS `'.Kohana::config('kwalbum.dbtables.items').'`';
		$db->query($sql);
		$sql = 'DROP TABLE IF EXISTS `'.Kohana::config('kwalbum.dbtables.users').'`';
		$db->query($sql);
		$sql = 'DROP TABLE IF EXISTS `'.Kohana::config('kwalbum.dbtables.locations').'`';
		$db->query($sql);
	}

	/** Create new Kwalbum tables
	 *
	 * @param  Database to connect to
	 * @return void
	 */
	private function _create_tables($db)
	{
		// Users
		$sql = 'CREATE  TABLE IF NOT EXISTS `'
		        .Kohana::config('kwalbum.dbtables.users').'`(
		          `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
		          `name` TINYTEXT NOT NULL ,
		          `openid` TINYTEXT NOT NULL ,
		          `visit_dt` DATETIME NOT NULL ,
		          `permission_level` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  		          INDEX `title` (`name` (10) ASC) ,
		          PRIMARY KEY (`id`)
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8
		        PACK_KEYS = DEFAULT;';
		$db->query($sql);

		// Locations
		$sql = 'CREATE TABLE IF NOT EXISTS `'
		        .Kohana::config('kwalbum.dbtables.locations').'`(
		          `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
		          `name` VARCHAR('.$this->_user['maxNameLength'].') NOT NULL ,
		          `latitude` DECIMAL(9,6) NOT NULL DEFAULT 0,
		          `longitude` DECIMAL(9,6) NOT NULL DEFAULT 0,
		          `count` SMALLINT UNSIGNED NOT NULL DEFAULT 0 ,
		          PRIMARY KEY (`id`) ,
		          INDEX `location` (`name`(10) ASC) ,
		          INDEX `coordinates` (`latitude` ASC, `longitude` ASC)
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8';
		$db->query($sql);

		// Items
		$sql = 'CREATE  TABLE IF NOT EXISTS `'
		        .Kohana::config('kwalbum.dbtables.items').'`(
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
		          `latitude` DECIMAL(9,6) NOT NULL DEFAULT 0,
		          `longitude` DECIMAL(9,6) NOT NULL DEFAULT 0,
		          `update_dt` DATETIME NOT NULL ,
		          `create_dt` DATETIME NOT NULL ,
		          `is_external` TINYINT NOT NULL DEFAULT 0 ,
		          PRIMARY KEY (`id`) ,
		          INDEX `location_id` (`location_id` ASC) ,
		          INDEX `user_id` (`user_id` ASC) ,
		          INDEX `coordinates` (`latitude` ASC, `longitude` ASC) ,
		          INDEX `sort_dt` (`sort_dt` ASC) ,
		          CONSTRAINT `location_id`
		            FOREIGN KEY (`location_id` )
		            REFERENCES `mydb`.`locations` (`id` )
		            ON DELETE NO ACTION
		            ON UPDATE NO ACTION,
		          CONSTRAINT `user_id_i`
		            FOREIGN KEY (`user_id` )
		            REFERENCES `mydb`.`users` (`id` )
		            ON DELETE NO ACTION
		            ON UPDATE NO ACTION
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8';
		$db->query($sql);

		// Comments
		$sql = 'CREATE  TABLE IF NOT EXISTS `'
		        .Kohana::config('kwalbum.dbtables.comments').'`(
		          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		          `item_id` MEDIUMINT UNSIGNED NOT NULL,
		          `name` TINYTEXT NOT NULL,
		          `text` TEXT NOT NULL,
		          `create_dt` DATETIME NOT NULL,
		          `ip` INT UNSIGNED NOT NULL,
		          PRIMARY KEY (`id`) ,
		          INDEX `item_id` (`item_id` ASC) ,
		          INDEX `create_dt` (`create_dt` ASC) ,
		          CONSTRAINT `item_id`
		            FOREIGN KEY (`item_id`)
		            REFERENCES `'.Kohana::config('kwalbum.dbtables.items').'` (`id`)
		            ON DELETE CASCADE
		            ON UPDATE CASCADE
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query($sql);

		// Tags
		$sql = 'CREATE  TABLE IF NOT EXISTS `'
		        .Kohana::config('kwalbum.dbtables.tags').'`(
		          `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
		          `name` TINYTEXT NOT NULL ,
		          `count` SMALLINT UNSIGNED NOT NULL ,
		          PRIMARY KEY (`id`) ,
		          INDEX `tag` (`name`(10) ASC)
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query($sql);

		// Items_Tags relationship
		$sql = 'CREATE  TABLE IF NOT EXISTS `'
		        .Kohana::config('kwalbum.dbtables.items_tags').'`(
		          `item_id` MEDIUMINT UNSIGNED NOT NULL ,
		          `tag_id` SMALLINT UNSIGNED NOT NULL ,
		          INDEX `item_id` (`item_id` ASC) ,
		          INDEX `tag_id` (`tag_id` ASC) ,
		          PRIMARY KEY (`item_id`, `tag_id`) ,
		          CONSTRAINT `item_id_t`
		            FOREIGN KEY (`item_id` )
		            REFERENCES `'.Kohana::config('kwalbum.dbtables.items').'` (`id` )
		            ON DELETE CASCADE
		            ON UPDATE CASCADE,
		          CONSTRAINT `tag_id`
		            FOREIGN KEY (`tag_id` )
		            REFERENCES `'.Kohana::config('kwalbum.dbtables.tags').'` (`id` )
		            ON DELETE CASCADE
		            ON UPDATE CASCADE
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query($sql);

		// Persons
		$sql = 'CREATE  TABLE IF NOT EXISTS `'
		        .Kohana::config('kwalbum.dbtables.persons').'`(
		          `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
		          `name` TINYTEXT NOT NULL ,
		          `count` SMALLINT UNSIGNED NOT NULL ,
		          PRIMARY KEY (`id`) ,
		          INDEX `person` (`name`(10) ASC)
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query($sql);

		// Items_Persons relationship
		$sql = 'CREATE  TABLE IF NOT EXISTS `'
		        .Kohana::config('kwalbum.dbtables.items_persons').'`(
		          `item_id` MEDIUMINT UNSIGNED NOT NULL ,
		          `person_id` SMALLINT UNSIGNED NOT NULL ,
		          INDEX `item_id` (`item_id` ASC) ,
		          INDEX `person_id` (`person_id` ASC) ,
		          PRIMARY KEY (`item_id`, `person_id`) ,
		          CONSTRAINT `item_id_p`
		            FOREIGN KEY (`item_id` )
		            REFERENCES `'.Kohana::config('kwalbum.dbtables.items').'` (`id` )
		            ON DELETE CASCADE
		            ON UPDATE CASCADE,
		          CONSTRAINT `person_id`
		            FOREIGN KEY (`person_id` )
		            REFERENCES `'.Kohana::config('kwalbum.dbtables.persons').'` (`id` )
		            ON DELETE CASCADE
		            ON UPDATE CASCADE
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query($sql);

		// Favorites, relationship between Items and Users
		$sql = 'CREATE  TABLE IF NOT EXISTS `'
		        .Kohana::config('kwalbum.dbtables.favorites').'`(
		          `user_id` SMALLINT UNSIGNED NOT NULL ,
		          `item_id` MEDIUMINT UNSIGNED NOT NULL ,
		          `add_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
		          INDEX `add_ts` (`add_ts` ASC) ,
		          PRIMARY KEY (`user_id`, `item_id`) ,
		          CONSTRAINT `user_id_f`
		            FOREIGN KEY (`user_id`)
		            REFERENCES `'.Kohana::config('kwalbum.dbtables.users').'` (`id`)
		            ON DELETE CASCADE
		            ON UPDATE CASCADE,
		          CONSTRAINT `item_id_f`
		            FOREIGN KEY (`item_id`)
		            REFERENCES `'.Kohana::config('kwalbum.dbtables.items').'` (`id`)
		            ON DELETE CASCADE
		            ON UPDATE CASCADE
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query($sql);

		// Sites, external sites to import items from
		$sql = 'CREATE  TABLE IF NOT EXISTS `'
		        .Kohana::config('kwalbum.dbtables.sites').'`(
		          `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT ,
		          `url` VARCHAR(100) NOT NULL ,
		          `key` VARCHAR(45) NOT NULL ,
		          `import_dt` DATETIME NOT NULL ,
		          PRIMARY KEY (`id`)
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query($sql);

		// Items_Sites relationship for imported items
		$sql = 'CREATE  TABLE IF NOT EXISTS `'
		        .Kohana::config('kwalbum.dbtables.items_sites').'`(
		          `item_id` MEDIUMINT UNSIGNED NOT NULL ,
		          `site_id` TINYINT UNSIGNED NOT NULL ,
		          `external_item_id` MEDIUMINT UNSIGNED NOT NULL ,
		          INDEX `item_id` (`item_id` ASC) ,
		          PRIMARY KEY (`item_id`) ,
		          INDEX `site_id` (`site_id` ASC) ,
		          INDEX `external_item_id` (`external_item_id` ASC) ,
		          CONSTRAINT `item_id_s`
		            FOREIGN KEY (`item_id` )
		            REFERENCES `'.Kohana::config('kwalbum.dbtables.items').'` (`id` )
		            ON DELETE CASCADE
		            ON UPDATE CASCADE,
		          CONSTRAINT `site_id`
		            FOREIGN KEY (`site_id` )
		            REFERENCES `'.Kohana::config('kwalbum.dbtables.sites').'` (`id` )
		            ON DELETE RESTRICT
		            ON UPDATE CASCADE
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query($sql);
	}
}