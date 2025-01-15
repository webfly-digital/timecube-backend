<?
namespace Zverushki\Seofilter\Configure;
use Bitrix\Main\Loader,
	Zverushki\Seofilter\configuration;
class Version
{
	static function get(){
		if(!$_COOKIE['DMODE'] == 'MODULE')
			return;

		_jp([
			'version' => static::getVersion(),
			'bitrix_version' => SM_VERSION,
			'check' => Loader::includeSharewareModule('zverushki.seofilter'),
			'argument' => static::getArguments(),
			'options' => configuration::getOptions(SITE_ID),
			'MemTotal' => static::getMemory()['MemTotal'],
			'MemLimit' => ini_get('memory_limit')
		]);
	}
	static function getMemory(){
		if(file_exists("/proc/meminfo")) {
			$data = explode( "\n", file_get_contents( "/proc/meminfo" ) );
			$meminfo = [];
			if($data)
				foreach ( $data as $line ) {
					list( $key, $val ) = explode( ":", $line );
					if(preg_match('/kB/', $val))
						$val = round((intval($val)/1024), 1).' Mb';
					$meminfo[ $key ] = trim( $val );
				}

			return $meminfo;
		}
	}

	static function getVersion(){
		include __DIR__.'/../../install/version.php';
		return $arModuleVersion['VERSION'];
	}

	static function getArguments(){
		include_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/zverushki.seofilter/argument.php';
		$argument['date'] = date('d.m.Y H:i:s', $argument['time']);
		$argument['time_default_option'] = filectime(__DIR__.'/../../default_option.php');
		$argument['date_default_option'] = date('d.m.Y H:i:s', $argument['time_default_option']	);

		return $argument;
	}
}
?>