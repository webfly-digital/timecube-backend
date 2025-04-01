<?

namespace Ammina\Optimizer;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\EventResult;
use Bitrix\Main\Type\DateTime;

class StatTypesTable extends DataManager
{
	public static function getTableName()
	{
		return 'am_optimizer_stat_types';
	}

	public static function getMap()
	{
		$fieldsMap = array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'TYPE' => array(
				'data_type' => 'string',
			),
			'TOTAL_COUNT' => array(
				'data_type' => 'integer',
			),
			'TOTAL_SIZE' => array(
				'data_type' => 'integer',
			),
		);

		return $fieldsMap;
	}

}
