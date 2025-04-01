<?

namespace Ammina\Optimizer;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\Validator\Length;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\EventResult;
use Bitrix\Main\Type\DateTime;

class FilesOptimizedTable extends DataManager
{
	public static function getTableName()
	{
		return 'am_optimizer_files_opt';
	}

	public static function getMap()
	{
		$fieldsMap = array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'ORIGINAL_ID' => array(
				'data_type' => 'integer',
			),
			'FILE_NAME' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateFileName'),
			),
			'FILE_DATE' => array(
				'data_type' => 'datetime',
				'required' => true,
			),
			'FILE_SIZE' => array(
				'data_type' => 'integer',
				'required' => true,
			),
		);

		return $fieldsMap;
	}

	public static function validateFileName()
	{
		return array(
			new Length(NULL, 511),
		);
	}
}
