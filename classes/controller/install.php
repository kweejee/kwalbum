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

class Controller_Install extends Controller_Kwalbum
{
	private $_user = array('minNameLength' => 2, 'maxNameLength' => 45);

	public function action_index()
	{
		$this->template->title = 'Install';

		// Uncomment to delete everything and start over
		//$this->_drop_tables();

		// Do not continue installation if at least 1 user exists in the database
		try
		{
			$user = Model::factory('kwalbum_user');
			if ($user->load(1)->loaded == true)
			{
				$view = View::factory('install/2');
				$this->template->bind('content', $view);
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
			$post = Validate::factory($_POST)
				->filter(true, 'trim')
				->filter(true, 'htmlspecialchars')
				->rule('name', 'not_empty')
				->rule('openid', 'not_empty')
				->rule('name', 'min_length', array($this->_user['minNameLength']))
				->rule('name', 'max_length', array($this->_user['maxNameLength']));

			if ($post->check())
			{
				$data = $post->as_array();
				$name = $data['name'];
				$openid = $data['openid'];

				try
				{
					$this->_create_tables();

					$user = Model::factory('kwalbum_user');
					$user->name = $name;
					$user->openid = $openid;
					$user->permission_level = 5;
					$user->save();
					$user->clear();
					$user->name = 'Deleted User';
					$user->openid = '';
					$user->permission_level = 0;
					$user->save();
					$location = Model::factory('kwalbum_location');
					$location->name = 'Unknown Location';
					$location->save();
					$this->template->content = new View('install/2');
					return;
				}
				catch (Exception $e)
				{
					$errors = array('db', 'There was an error creating the database tables.');
				}
			}
			else // Did not validate
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any
				// Pass the error message file name to the errors() method
				// Default error message file is in i18n/en_US/
				$errors = arr::overwrite($errors, $post->errors('install_form/errors'));
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
	private function _drop_tables()
	{
		$db = Database::instance();

		// Drop order is arranged based on foreign key restraints.
		$sql = 'DROP TABLE IF EXISTS `kwalbum_comments`';
		$db->query(null, $sql);
		$sql = 'DROP TABLE IF EXISTS `kwalbum_favorites`';
		$db->query(null, $sql);
		$sql = 'DROP TABLE IF EXISTS `kwalbum_items_tags`';
		$db->query(null, $sql);
		$sql = 'DROP TABLE IF EXISTS `kwalbum_items_persons`';
		$db->query(null, $sql);
		$sql = 'DROP TABLE IF EXISTS `kwalbum_items_sites`';
		$db->query(null, $sql);
		$sql = 'DROP TABLE IF EXISTS `kwalbum_tags`';
		$db->query(null, $sql);
		$sql = 'DROP TABLE IF EXISTS `kwalbum_persons`';
		$db->query(null, $sql);
		$sql = 'DROP TABLE IF EXISTS `kwalbum_sites`';
		$db->query(null, $sql);
		$sql = 'DROP TABLE IF EXISTS `kwalbum_items`';
		$db->query(null, $sql);
		$sql = 'DROP TABLE IF EXISTS `kwalbum_users`';
		$db->query(null, $sql);
		$sql = 'DROP TABLE IF EXISTS `kwalbum_locations`';
		$db->query(null, $sql);
	}

	/** Create new Kwalbum tables
	 *
	 * @param  Database to connect to
	 * @return void
	 */
	private function _create_tables()
	{
		$db = Database::instance();

		// Users
		$sql = 'CREATE  TABLE IF NOT EXISTS `kwalbum_users`(
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
		$db->query(null, $sql);

		// Locations
		$sql = 'CREATE TABLE IF NOT EXISTS `kwalbum_locations`(
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
		$db->query(null, $sql);

		// Items
		$sql = 'CREATE  TABLE IF NOT EXISTS `kwalbum_items`(
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
		          CONSTRAINT `user_id_i`
		            FOREIGN KEY (`user_id` )
		            REFERENCES `kwalbum_users` (`id` ),
		          CONSTRAINT `location_id_i`
		            FOREIGN KEY (`location_id` )
		            REFERENCES `kwalbum_locations` (`id` )
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8';
		$db->query(null, $sql);

		// Comments
		$sql = 'CREATE  TABLE IF NOT EXISTS `kwalbum_comments`(
		          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		          `item_id` MEDIUMINT UNSIGNED NOT NULL,
		          `name` TINYTEXT NOT NULL,
		          `text` TEXT NOT NULL,
		          `create_dt` DATETIME NOT NULL,
		          `ip` INT SIGNED NOT NULL,
		          PRIMARY KEY (`id`) ,
		          INDEX `item_id` (`item_id` ASC) ,
		          INDEX `create_dt` (`create_dt` ASC) ,
		          CONSTRAINT `item_id`
		            FOREIGN KEY (`item_id`)
		            REFERENCES `kwalbum_items` (`id`)
		            ON DELETE CASCADE
		            ON UPDATE CASCADE
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query(null, $sql);

		// Tags
		$sql = 'CREATE  TABLE IF NOT EXISTS `kwalbum_tags`(
		          `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
		          `name` TINYTEXT NOT NULL ,
		          `count` SMALLINT UNSIGNED NOT NULL ,
		          PRIMARY KEY (`id`) ,
		          INDEX `tag` (`name`(10) ASC)
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query(null, $sql);

		// Items_Tags relationship
		$sql = 'CREATE  TABLE IF NOT EXISTS `kwalbum_items_tags`(
		          `item_id` MEDIUMINT UNSIGNED NOT NULL ,
		          `tag_id` SMALLINT UNSIGNED NOT NULL ,
		          INDEX `item_id` (`item_id` ASC) ,
		          INDEX `tag_id` (`tag_id` ASC) ,
		          PRIMARY KEY (`item_id`, `tag_id`) ,
		          CONSTRAINT `item_id_t`
		            FOREIGN KEY (`item_id` )
		            REFERENCES `kwalbum_items` (`id` )
		            ON DELETE CASCADE
		            ON UPDATE CASCADE,
		          CONSTRAINT `tag_id`
		            FOREIGN KEY (`tag_id` )
		            REFERENCES `kwalbum_tags` (`id` )
		            ON DELETE CASCADE
		            ON UPDATE CASCADE
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query(null, $sql);

		// Persons
		$sql = 'CREATE  TABLE IF NOT EXISTS `kwalbum_persons`(
		          `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
		          `name` TINYTEXT NOT NULL ,
		          `count` SMALLINT UNSIGNED NOT NULL ,
		          PRIMARY KEY (`id`) ,
		          INDEX `person` (`name`(10) ASC)
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query(null, $sql);

		// Items_Persons relationship
		$sql = 'CREATE  TABLE IF NOT EXISTS `kwalbum_items_persons`(
		          `item_id` MEDIUMINT UNSIGNED NOT NULL ,
		          `person_id` SMALLINT UNSIGNED NOT NULL ,
		          INDEX `item_id` (`item_id` ASC) ,
		          INDEX `person_id` (`person_id` ASC) ,
		          PRIMARY KEY (`item_id`, `person_id`) ,
		          CONSTRAINT `item_id_p`
		            FOREIGN KEY (`item_id` )
		            REFERENCES `kwalbum_items` (`id` )
		            ON DELETE CASCADE
		            ON UPDATE CASCADE,
		          CONSTRAINT `person_id`
		            FOREIGN KEY (`person_id` )
		            REFERENCES `kwalbum_persons` (`id` )
		            ON DELETE CASCADE
		            ON UPDATE CASCADE
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query(null, $sql);

		// Favorites, relationship between Items and Users
		$sql = 'CREATE  TABLE IF NOT EXISTS `kwalbum_favorites`(
		          `user_id` SMALLINT UNSIGNED NOT NULL ,
		          `item_id` MEDIUMINT UNSIGNED NOT NULL ,
		          `add_ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
		          INDEX `add_ts` (`add_ts` ASC) ,
		          PRIMARY KEY (`user_id`, `item_id`) ,
		          CONSTRAINT `user_id_f`
		            FOREIGN KEY (`user_id`)
		            REFERENCES `kwalbum_users` (`id`)
		            ON DELETE CASCADE
		            ON UPDATE CASCADE,
		          CONSTRAINT `item_id_f`
		            FOREIGN KEY (`item_id`)
		            REFERENCES `kwalbum_items` (`id`)
		            ON DELETE CASCADE
		            ON UPDATE CASCADE
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query(null, $sql);

		// Sites, external sites to import items from
		$sql = 'CREATE  TABLE IF NOT EXISTS `kwalbum_sites`(
		          `id` TINYINT UNSIGNED NOT NULL AUTO_INCREMENT ,
		          `url` VARCHAR(100) NOT NULL ,
		          `key` VARCHAR(45) NOT NULL ,
		          `import_dt` DATETIME NOT NULL ,
		          PRIMARY KEY (`id`)
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query(null, $sql);

		// Items_Sites relationship for imported items
		$sql = 'CREATE  TABLE IF NOT EXISTS `kwalbum_items_sites`(
		          `item_id` MEDIUMINT UNSIGNED NOT NULL ,
		          `site_id` TINYINT UNSIGNED NOT NULL ,
		          `external_item_id` MEDIUMINT UNSIGNED NOT NULL ,
		          INDEX `item_id` (`item_id` ASC) ,
		          PRIMARY KEY (`item_id`) ,
		          INDEX `site_id` (`site_id` ASC) ,
		          INDEX `external_item_id` (`external_item_id` ASC) ,
		          CONSTRAINT `item_id_s`
		            FOREIGN KEY (`item_id` )
		            REFERENCES `kwalbum_items` (`id` )
		            ON DELETE CASCADE
		            ON UPDATE CASCADE,
		          CONSTRAINT `site_id`
		            FOREIGN KEY (`site_id` )
		            REFERENCES `kwalbum_sites` (`id` )
		            ON DELETE RESTRICT
		            ON UPDATE CASCADE
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8;';
		$db->query(null, $sql);
	}
}