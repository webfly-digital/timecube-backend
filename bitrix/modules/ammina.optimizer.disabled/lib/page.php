<?

namespace Ammina\Optimizer;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\EventResult;
use Bitrix\Main\Type\DateTime;

class PageTable extends DataManager
{
	public static function getTableName()
	{
		return 'am_optimizer_page';
	}

	public static function getMap()
	{
		$fieldsMap = array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'PAGE_URL' => array(
				'data_type' => 'string',
			),
			'ACTIVE' => array(
				'data_type' => 'enum',
				'values' => array('N', 'Y'),
			),
			'STATUS' => array(
				'data_type' => 'enum',
				'values' => array('N', 'P', 'E', 'C'),
			),
			'DATE_CREATE' => array(
				'data_type' => 'datetime',
			),
			'DATE_CHECK' => array(
				'data_type' => 'datetime',
			),
			'DESKTOP_PERFORMANCE' => array(
				'data_type' => 'integer',
			),
			'DESKTOP_ACCESSIBILITY' => array(
				'data_type' => 'integer',
			),
			'DESKTOP_BESTPRACTICES' => array(
				'data_type' => 'integer',
			),
			'DESKTOP_SEO' => array(
				'data_type' => 'integer',
			),
			'DESKTOP_PWA' => array(
				'data_type' => 'integer',
			),
			'MOBILE_PERFORMANCE' => array(
				'data_type' => 'integer',
			),
			'MOBILE_ACCESSIBILITY' => array(
				'data_type' => 'integer',
			),
			'MOBILE_BESTPRACTICES' => array(
				'data_type' => 'integer',
			),
			'MOBILE_SEO' => array(
				'data_type' => 'integer',
			),
			'MOBILE_PWA' => array(
				'data_type' => 'integer',
			),
			'OLD_DATA' => array(
				'data_type' => 'string',
			),
		);

		return $fieldsMap;
	}

	public static function onBeforeUpdate(Event $event)
	{
		$result = new EventResult();
		$data = $event->getParameter("fields");
		$arUpdateFields = false;
		if (isset($data['OLD_DATA'])) {
			$arUpdateFields['OLD_DATA'] = serialize($data['OLD_DATA']);
		}

		if (is_array($arUpdateFields)) {
			$result->modifyFields($arUpdateFields);
		}
		return $result;
	}

	public static function onBeforeAdd(Event $event)
	{
		$result = new EventResult();
		$data = $event->getParameter("fields");
		$arUpdateFields = false;
		if (isset($data['OLD_DATA'])) {
			$arUpdateFields['OLD_DATA'] = serialize($data['OLD_DATA']);
		}
		$arUpdateFields['DATE_CREATE'] = new DateTime();
		if (is_array($arUpdateFields)) {
			$result->modifyFields($arUpdateFields);
		}
		return $result;
	}

	public static function getList(array $parameters = array())
	{
		$result = parent::getList($parameters);
		$result->setSerializedFields(array("OLD_DATA"));
		return $result;
	}
}
