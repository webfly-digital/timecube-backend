<?

namespace Ammina\Optimizer;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\EventResult;

class HistoryTable extends DataManager
{
	public static function getTableName()
	{
		return 'am_optimizer_history';
	}

	public static function getMap()
	{
		$fieldsMap = array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'PAGE_ID' => array(
				'data_type' => 'integer',
			),
			'PAGE' => array(
				'data_type' => '\Ammina\Optimizer\Page',
				'reference' => array('=this.PAGE_ID' => 'ref.ID'),
			),
			'DATE_CHECK' => array(
				'data_type' => 'datetime',
			),
			'DESKTOP_DATA' => array(
				'data_type' => 'string',
			),
			'MOBILE_DATA' => array(
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
		if (isset($data['DESKTOP_DATA'])) {
			$arUpdateFields['DESKTOP_DATA'] = serialize($data['DESKTOP_DATA']);
		}
		if (isset($data['MOBILE_DATA'])) {
			$arUpdateFields['MOBILE_DATA'] = serialize($data['MOBILE_DATA']);
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
		if (isset($data['DESKTOP_DATA'])) {
			$arUpdateFields['DESKTOP_DATA'] = serialize($data['DESKTOP_DATA']);
		}
		if (isset($data['MOBILE_DATA'])) {
			$arUpdateFields['MOBILE_DATA'] = serialize($data['MOBILE_DATA']);
		}

		if (is_array($arUpdateFields)) {
			$result->modifyFields($arUpdateFields);
		}
		return $result;
	}

	public static function getList(array $parameters = array())
	{
		$result = parent::getList($parameters);
		$result->setSerializedFields(array("DESKTOP_DATA", "MOBILE_DATA"));
		return $result;
	}
}
