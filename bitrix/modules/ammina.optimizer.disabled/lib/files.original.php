<?

namespace Ammina\Optimizer;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\Validator\Length;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\EventResult;
use Bitrix\Main\Type\DateTime;

class FilesOriginalsTable extends DataManager
{
	public static function getTableName()
	{
		return 'am_optimizer_files_orig';
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
				'validation' => array(__CLASS__, 'validateType'),
			),
			'FILE_NAME_HASH' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateFileNameHash'),
			),
			'FILE_NAME' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateFileName'),
			),
			'FILE_EXTENSION' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFileExtension'),
			),
			'FILE_DATE' => array(
				'data_type' => 'datetime',
				'required' => true,
			),
			'FILE_SIZE' => array(
				'data_type' => 'integer',
			),
			'CNT_OPTIMIZED' => array(
				'data_type' => 'integer',
			),
			'OPTIMIZED' => array(
				'data_type' => '\Ammina\Optimizer\FilesOptimized',
				'reference' => array(
					'=this.ID' => 'ref.ORIGINAL_ID',
				),
			),
		);

		return $fieldsMap;
	}

	public static function validateType()
	{
		return array(
			new Length(NULL, 32),
		);
	}

	public static function validateFileName()
	{
		return array(
			new  Length(NULL, 511),
		);
	}

	public static function validateFileNameHash()
	{
		return array(
			new  Length(NULL, 16),
		);
	}

	public static function validateFileExtension()
	{
		return array(
			new  Length(NULL, 32),
		);
	}

	public static function onBeforeUpdate(Event $event)
	{
		$result = new EventResult();
		$data = $event->getParameter("fields");
		$arUpdateFields = false;
		if (isset($data['FILE_NAME'])) {
			$arUpdateFields['FILE_NAME_HASH'] = amopt_substr(md5(trim(amopt_strtolower($data['FILE_NAME']))), 0, 4);
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
		if (isset($data['FILE_NAME'])) {
			$arUpdateFields['FILE_NAME_HASH'] = amopt_substr(md5(trim(amopt_strtolower($data['FILE_NAME']))), 0, 4);
		}

		if (is_array($arUpdateFields)) {
			$result->modifyFields($arUpdateFields);
		}
		return $result;
	}

	public static function getList(array $parameters = array())
	{
		if (isset($parameters['filter']['CHECK_HASH']) && $parameters['filter']['CHECK_HASH'] == "Y" && isset($parameters['filter']['FILE_NAME'])) {
			unset($parameters['filter']['CHECK_HASH']);
			$parameters['filter']['FILE_NAME_HASH'] = amopt_substr(md5(trim(amopt_strtolower($parameters['filter']['FILE_NAME']))), 0, 4);
			$parameters['filter']['=FILE_NAME']=$parameters['filter']['FILE_NAME'];
			unset($parameters['filter']['FILE_NAME']);
		}
		$result = parent::getList($parameters);
		return $result;
	}
}
