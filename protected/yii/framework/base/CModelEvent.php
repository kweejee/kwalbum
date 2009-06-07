<?php
/**
 * CModelEvent class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2009 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */


/**
 * CModelEvent class.
 *
 * CModelEvent represents the event parameters needed by events raised by a model.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CModelEvent.php 1080 2009-05-31 20:47:16Z qiang.xue $
 * @package system.base
 * @since 1.0
 */
class CModelEvent extends CEvent
{
	/**
	 * @var boolean whether the model is valid. Defaults to true.
	 * If this is set false, {@link CModel::validate()} will return false and quit the current validation process.
	 */
	public $isValid=true;
}
