<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

function doAMOPTShowFormPage($arCurrentValues, $strBaseName)
{
	?>
	<div class="amopts-category-page">
		<div class="amopts-category-page__title">
			<div class="amopts-category-page__main">
				<h2><?= Loc::getMessage("AMOPT_GROUP_PAGES_FILTER_TITLE") ?></h2>
				<p class="amopts-category-page__description"><?= Loc::getMessage("AMOPT_GROUP_PAGES_FILTER_DESCRIPTION") ?></p>
			</div>
			<div class="amopts-category-page__options">
				<input type="hidden" name="AMOPT<?= $strBaseName ?>[page][DELETE]" value="N"/>
				<label><input type="checkbox" name="AMOPT<?= $strBaseName ?>[page][DELETE]" value="Y"/> <?= Loc::getMessage("AMOPT_OPTION_DELETE") ?>
				</label>
			</div>
		</div>
		<div class="amopts-category-page__content">
			<div class="amopts-category-page__option">
				<div class="amopts-category-page__option-title">
					<p class="amopts-category-page__option-name"><?= Loc::getMessage("AMOPT_GROUP_PAGES_OPTION_NAME_TITLE") ?></p>
					<p class="amopts-category-page__option-description"><?= Loc::getMessage("AMOPT_GROUP_PAGES_OPTION_NAME_DESCRIPTION") ?></p>
				</div>
				<div class="amopts-category-page__option-set">
					<input type="text" size="50" name="AMOPT<?= $strBaseName ?>[page][NAME]" value="<?= htmlspecialchars($arCurrentValues['page']['NAME']) ?>"/>
				</div>
				<div class="amopts-category-page__option-help">
					&nbsp;
				</div>
			</div>
			<div class="amopts-category-page__option">
				<div class="amopts-category-page__option-title">
					<p class="amopts-category-page__option-name"><?= Loc::getMessage("AMOPT_GROUP_PAGES_OPTION_ACTIVE_TITLE") ?></p>
					<p class="amopts-category-page__option-description"><?= Loc::getMessage("AMOPT_GROUP_PAGES_OPTION_ACTIVE_DESCRIPTION") ?></p>
				</div>
				<div class="amopts-category-page__option-set">
					<input type="hidden" name="AMOPT<?= $strBaseName ?>[page][ACTIVE]" value="N"/>
					<input type="checkbox" name="AMOPT<?= $strBaseName ?>[page][ACTIVE]" title="<?= Loc::getMessage("AMOPT_GROUP_PAGES_OPTION_ACTIVE_TITLE") ?>" value="Y"<?= ($arCurrentValues['page']['ACTIVE'] == "Y" ? ' checked="checked"' : '') ?> />
				</div>
				<div class="amopts-category-page__option-help">
					&nbsp;
				</div>
			</div>
			<div class="amopts-category-page__option">
				<div class="amopts-category-page__option-title">
					<p class="amopts-category-page__option-name"><?= Loc::getMessage("AMOPT_GROUP_PAGES_OPTION_PAGES_TITLE") ?></p>
					<p class="amopts-category-page__option-description"><?= Loc::getMessage("AMOPT_GROUP_PAGES_OPTION_PAGES_DESCRIPTION") ?></p>
				</div>
				<div class="amopts-category-page__option-set">
					<textarea cols="50" rows="5" name="AMOPT<?= $strBaseName ?>[page][PAGES]"><?= htmlspecialchars($arCurrentValues['page']['PAGES']) ?></textarea>
				</div>
				<div class="amopts-category-page__option-help">
					&nbsp;
				</div>
			</div>
		</div>
	</div>
	<?
}

function doAMOPTShowFormSettings($arAllOptionsDescription, $arCurrentValues, $strBaseName, $bIsPages = false)
{
	foreach ($arAllOptionsDescription['category'] as $strCategory => &$arCategory) {
		$arShortCategory = array();
		$strBaseKey = 'AMOPT_OPTION_GROUP_' . amopt_strtoupper($strCategory) . "_";
		if (!isset($arCategory['TITLE'])) {
			$arCategory['TITLE'] = Loc::getMessage($strBaseKey . "TITLE");
		}
		if (!isset($arCategory['DESCRIPTION'])) {
			$arCategory['DESCRIPTION'] = Loc::getMessage($strBaseKey . "DESCRIPTION");
		}
		if (!isset($arCategory['HELP'])) {
			$arCategory['HELP'] = Loc::getMessage($strBaseKey . "HELP");
		}
		?>
		<div class="amopts-category">
			<div class="amopts-category__title">
				<div class="amopts-category__main">
					<h2><?= $arCategory['TITLE'] ?></h2>
					<p class="amopts-category__description"><?= $arCategory['DESCRIPTION'] ?></p>
					<?
					//Короткое описание свойств
					?>
				</div>
				<div class="amopts-category__options">
					<input type="hidden" name="AMOPT<?= $strBaseName ?>[category][<?= $strCategory ?>][options][ACTIVE]" value="N"/>
					<label><input type="checkbox" name="AMOPT<?= $strBaseName ?>[category][<?= $strCategory ?>][options][ACTIVE]" value="Y"<?= ($arCurrentValues['category'][$strCategory]['options']['ACTIVE'] == "Y" ? ' checked="checked"' : '') ?> /> <?= Loc::getMessage("AMOPT_OPTION_ACTIVE") ?>
					</label>
				</div>
			</div>
			<div class="amopts-category__content">
				<?
				foreach ($arCategory['groups'] as $strGroup => &$arGroup) {
					$arShortGroup = array();
					$strBaseKey = 'AMOPT_OPTION_GROUP_' . amopt_strtoupper($strCategory) . "_GROUP_" . amopt_strtoupper($strGroup) . "_";
					if (!isset($arGroup['TITLE'])) {
						$arGroup['TITLE'] = Loc::getMessage($strBaseKey . "TITLE");
					}
					if (!isset($arGroup['DESCRIPTION'])) {
						$arGroup['DESCRIPTION'] = Loc::getMessage($strBaseKey . "DESCRIPTION");
					}
					if (!isset($arGroup['HELP'])) {
						$arGroup['HELP'] = Loc::getMessage($strBaseKey . "HELP");
					}
					if ($arCurrentValues['category'][$strCategory]['groups'][$strGroup]['DEFAULT'] != "N") {
						$arCurrentValues['category'][$strCategory]['groups'][$strGroup]['DEFAULT'] = "Y";
					}

					?>
					<div class="amopts-category__group">
						<div class="amopts-category__group-title">
							<div class="amopts-category__group-title-main">
								<h3><?= $arGroup['TITLE'] ?></h3>
								<p class="amopts-category__group-description"><?= $arGroup['DESCRIPTION'] ?></p>
							</div>
							<div class="amopts-category__group-title-def">
								<input type="hidden" name="AMOPT<?= $strBaseName ?>[category][<?= $strCategory ?>][groups][<?= $strGroup ?>][DEFAULT]" value="N"/>
								<label><input type="checkbox" class="amopts-category__group-disable" name="AMOPT<?= $strBaseName ?>[category][<?= $strCategory ?>][groups][<?= $strGroup ?>][DEFAULT]" value="Y"<?= ($arCurrentValues['category'][$strCategory]['groups'][$strGroup]['DEFAULT'] == "Y" ? ' checked="checked"' : '') ?> /> <?= Loc::getMessage("AMOPT_OPTION_DEFAULT") ?>
								</label>
							</div>
						</div>
						<div class="amopts-category__group-content">
							<?
							foreach ($arGroup['options'] as $strOption => &$arOption) {
								$strBaseKey = 'AMOPT_OPTION_GROUP_' . amopt_strtoupper($strCategory) . "_GROUP_" . amopt_strtoupper($strGroup) . "_" . amopt_strtoupper($strOption) . "_";
								if (!isset($arOption['TITLE'])) {
									$arOption['TITLE'] = Loc::getMessage($strBaseKey . "TITLE");
								}
								if (!isset($arOption['DESCRIPTION'])) {
									$arOption['DESCRIPTION'] = Loc::getMessage($strBaseKey . "DESCRIPTION");
								}
								if (!isset($arOption['HELP'])) {
									$arOption['HELP'] = Loc::getMessage($strBaseKey . "HELP");
								}

								if (isset($arOption['VARIANTS'])) {
									foreach ($arOption['VARIANTS'] as $keyVariant => &$arVariant) {
										$strBaseKeyVariant = 'AMOPT_OPTION_GROUP_' . amopt_strtoupper($strCategory) . "_GROUP_" . amopt_strtoupper($strGroup) . "_" . amopt_strtoupper($strOption) . "_VARIANT_" . amopt_strtoupper($keyVariant) . "_";
										if (!isset($arVariant['TITLE'])) {
											$arVariant['TITLE'] = Loc::getMessage($strBaseKeyVariant . "TITLE");
										}
										if (!isset($arVariant['DESCRIPTION'])) {
											$arVariant['DESCRIPTION'] = Loc::getMessage($strBaseKeyVariant . "DESCRIPTION");
										}
										if (!isset($arVariant['HELP'])) {
											$arVariant['HELP'] = Loc::getMessage($strBaseKeyVariant . "HELP");
										}
									}
								}

								if ($arOption['TYPE'] == "checkbox") {
									if (!isset($arOption['SHORT_TITLE'])) {
										$arOption['SHORT_TITLE']['Y'] = Loc::getMessage($strBaseKey . "SHORT_Y");
										$arOption['SHORT_TITLE']['N'] = Loc::getMessage($strBaseKey . "SHORT_N");
									}
								} else {
									if (!isset($arOption['SHORT_TITLE'])) {
										$arOption['SHORT_TITLE'] = Loc::getMessage($strBaseKey . "SHORT");
									}
								}

								if ($arOption['SHORT'] == "Y") {
									if (!isset($arGroup['options']['ACTIVE']) || $arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options']['ACTIVE'] == "Y" || $strOption == "ACTIVE") {
										if ($arOption['TYPE'] == "checkbox") {
											if ($arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options'][$strOption] == "Y") {
												$arShortGroup[] = $arOption['SHORT_TITLE']['Y'];
											} else {
												$arShortGroup[] = $arOption['SHORT_TITLE']['N'];
											}
										} elseif ($arOption['TYPE'] == "select") {
											$arShortGroup[] = $arOption['SHORT_TITLE'] . " " . $arOption['VARIANTS'][$arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options'][$strOption]]['TITLE'];
										} elseif ($arOption['TYPE'] == "select.options") {
											$arShortGroup[] = $arOption['SHORT_TITLE'] . " " . $arOption['VARIANTS'][$arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options'][$strOption]]['TITLE'];
										} elseif ($arOption['TYPE'] == "text.bytes") {
											$arShortGroup[] = $arOption['SHORT_TITLE'];
										} else {
											$arShortGroup[] = $arOption['SHORT_TITLE'];
										}
									}
								}
								?>
								<div class="amopts-category__option">
									<div class="amopts-category__option-title">
										<p class="amopts-category__option-name"><?= $arOption['TITLE'] ?></p>
										<p class="amopts-category__option-description"><?= $arOption['DESCRIPTION'] ?></p>
									</div>
									<div class="amopts-category__option-set">
										<?
										$bDisabled = ($arCurrentValues['category'][$strCategory]['groups'][$strGroup]['DEFAULT'] == "Y");
										if ($arOption['TYPE'] == "checkbox") {
											doAMOPTShowFormOptionCheckbox($arOption, $arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options'][$strOption], $strBaseName . '[category][' . $strCategory . '][groups][' . $strGroup . '][options][' . $strOption . ']', $bDisabled);
										} elseif ($arOption['TYPE'] == "text") {
											doAMOPTShowFormOptionText($arOption, $arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options'][$strOption], $strBaseName . '[category][' . $strCategory . '][groups][' . $strGroup . '][options][' . $strOption . ']', $bDisabled);
										} elseif ($arOption['TYPE'] == "textarea") {
											doAMOPTShowFormOptionTextArea($arOption, $arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options'][$strOption], $strBaseName . '[category][' . $strCategory . '][groups][' . $strGroup . '][options][' . $strOption . ']', $bDisabled);
										} elseif ($arOption['TYPE'] == "select") {
											doAMOPTShowFormOptionSelect($arOption, $arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options'][$strOption], $strBaseName . '[category][' . $strCategory . '][groups][' . $strGroup . '][options][' . $strOption . ']', $bDisabled);
										} elseif ($arOption['TYPE'] == "select.options") {
											doAMOPTShowFormOptionSelectOptions($arOption, $arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options'][$strOption], $strBaseName . '[category][' . $strCategory . '][groups][' . $strGroup . '][options][' . $strOption . ']', $bDisabled);
										} elseif ($arOption['TYPE'] == "text.bytes") {
											doAMOPTShowFormOptionTextBytes($arOption, $arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options'][$strOption], $strBaseName . '[category][' . $strCategory . '][groups][' . $strGroup . '][options][' . $strOption . ']', $bDisabled);
										} elseif ($arOption['TYPE'] == "files.static") {
											doAMOPTShowFormOptionFilesStatic($arOption, $arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options'][$strOption], $strBaseName . '[category][' . $strCategory . '][groups][' . $strGroup . '][options][' . $strOption . ']', $bDisabled);
										} elseif ($arOption['TYPE'] == "file") {
											doAMOPTShowFormOptionFile($arOption, $arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options'][$strOption], $strBaseName . '[category][' . $strCategory . '][groups][' . $strGroup . '][options][' . $strOption . ']', $bDisabled);
										} elseif ($arOption['TYPE'] == "headers.preload") {
											doAMOPTShowFormOptionHeadersPreload($arOption, $arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options'][$strOption], $strBaseName . '[category][' . $strCategory . '][groups][' . $strGroup . '][options][' . $strOption . ']', $bDisabled);
										} elseif ($arOption['TYPE'] == "headers.prefetch") {
											doAMOPTShowFormOptionHeadersPrefetch($arOption, $arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options'][$strOption], $strBaseName . '[category][' . $strCategory . '][groups][' . $strGroup . '][options][' . $strOption . ']', $bDisabled);
										} elseif ($arOption['TYPE'] == "headers.preconnect") {
											doAMOPTShowFormOptionHeadersPreconnect($arOption, $arCurrentValues['category'][$strCategory]['groups'][$strGroup]['options'][$strOption], $strBaseName . '[category][' . $strCategory . '][groups][' . $strGroup . '][options][' . $strOption . ']', $bDisabled);
										} else {
											echo $arOption['TYPE'];
										}
										?>

									</div>
									<div class="amopts-category__option-help">
										&nbsp;
									</div>
								</div>
								<?
							}
							foreach ($arShortGroup as $k => $v) {
								if (amopt_strlen(trim($v)) <= 0) {
									unset($arShortGroup[$k]);
								}
							}
							?>
						</div>
						<div class="amopts-category__group-content-short"><?= implode(", ", $arShortGroup) ?><?
							if (!empty($arShortGroup)) {
								?>.
								<a href="javascript:void(0)" data-group="<?= $strGroup ?>"><?= Loc::getMessage("AMMINA_OPTIMIZER_DETAIL") ?></a><?
							}
							?></div>
					</div>
					<?
					if (!empty($arShortGroup)) {
						$arShortCategory[] = '<strong>' . $arGroup['TITLE'] . '</strong>: ' . implode(", ", $arShortGroup) . '. <a href="javascript:void(0)" data-group="' . $strGroup . '">' . Loc::getMessage("AMMINA_OPTIMIZER_DETAIL") . '</a>';
					}
				}
				?>
			</div>
			<div class="amopts-category__content-short">
				<p><?= implode('</p><p>', $arShortCategory) ?></p>
			</div>
		</div>
		<?
	}
}

function doAMOPTShowFormOptionCheckbox($arOption, $arCurrentValue, $strBaseName, $bDisabled = false)
{
	?>
	<input type="hidden" name="AMOPT<?= $strBaseName ?>" title="<?= $arOption['TITLE'] ?>" value="N"/>
	<input type="checkbox" class="amopts-field-allowdisabled" name="AMOPT<?= $strBaseName ?>" title="<?= $arOption['TITLE'] ?>" value="Y"<?= ($arCurrentValue == "Y" ? ' checked="checked"' : '') ?><?= ($bDisabled ? ' disabled="disabled"' : '') ?>/>
	<?
}

function doAMOPTShowFormOptionText($arOption, $arCurrentValue, $strBaseName, $bDisabled = false)
{
	?>
	<input type="text" class="amopts-field-allowdisabled" name="AMOPT<?= $strBaseName ?>" size="50" value="<?= htmlspecialchars($arCurrentValue) ?>"<?= ($bDisabled ? ' disabled="disabled"' : '') ?>/>
	<?
}

function doAMOPTShowFormOptionTextArea($arOption, $arCurrentValue, $strBaseName, $bDisabled = false)
{
	?>
	<textarea cols="50" rows="5" class="amopts-field-allowdisabled" name="AMOPT<?= $strBaseName ?>"<?= ($bDisabled ? ' disabled="disabled"' : '') ?>><?= htmlspecialchars($arCurrentValue) ?></textarea>
	<?
}

function doAMOPTShowFormOptionSelect($arOption, $arCurrentValue, $strBaseName, $bDisabled = false)
{
	?>
	<div class="amopts-field">
		<select name="AMOPT<?= $strBaseName ?>" class="amopts-field__select amopts-field-allowdisabled"<?= ($bDisabled ? ' disabled="disabled"' : '') ?>>
			<?
			foreach ($arOption['VARIANTS'] as $k => $v) {
				?>
				<option value="<?= $k ?>"<?= ($k == $arCurrentValue ? " selected" : "") ?><?= ($v['DISABLED'] ? ' disabled="disabled"' : '') ?>><?= htmlspecialchars($v['TITLE']) ?></option>
				<?
			}
			?>
		</select>
		<div class="amopts-field__description">
			<?
			foreach ($arOption['VARIANTS'] as $k => $v) {
				?>
				<div class="amopts-field__description-text<?= ($k == $arCurrentValue ? " amopts-field__description-text_active" : "") ?>" data-value="<?= $k ?>"><?= $v['DESCRIPTION'] ?></div>
				<?
			}
			?>
		</div>
	</div>
	<?
}

function doAMOPTShowFormOptionSelectOptions($arOption, $arCurrentValue, $strBaseName, $bDisabled = false)
{
	?>
	<div class="amopts-field">
		<select name="AMOPT<?= $strBaseName ?>" class="amopts-field__select-options amopts-field-allowdisabled"<?= ($bDisabled ? ' disabled="disabled"' : '') ?>>
			<?
			foreach ($arOption['VARIANTS'] as $k => $v) {
				?>
				<option value="<?= $k ?>"<?= ($k == $arCurrentValue ? " selected" : "") ?><?= ($v['DISABLED'] ? ' disabled="disabled"' : '') ?>><?= htmlspecialchars($v['TITLE']) ?></option>
				<?
			}
			?>
		</select>
		<div class="amopts-field__description">
			<?
			foreach ($arOption['VARIANTS'] as $k => $v) {
				?>
				<div class="amopts-field__description-text<?= ($k == $arCurrentValue ? " amopts-field__description-text_active" : "") ?>" data-value="<?= $k ?>"><?= $v['DESCRIPTION'] ?></div>
				<?
			}
			?>
		</div>
	</div>
	<?
}

function doAMOPTShowFormOptionTextBytes($arOption, $arCurrentValue, $strBaseName, $bDisabled = false)
{
	$strValue = $arCurrentValue;
	$strType = 'b';
	if ($strValue > 0 && (intval((($strValue / 1024) * 10)) / 10) * 1024 == $strValue) {
		$strValue = $strValue / 1024;
		$strType = "k";
		if ((intval((($strValue / 1024) * 10)) / 10) * 1024 == $strValue) {
			$strValue = $strValue / 1024;
			$strType = "m";
			if ((intval((($strValue / 1024) * 10)) / 10) * 1024 == $strValue) {
				$strValue = $strValue / 1024;
				$strType = "g";
			}
		}
	}
	?>
	<div class="amopts-field">
		<input type="hidden" name="AMOPT<?= $strBaseName ?>" class="amopts-field__field" value="<?= $arCurrentValue ?>"/>
		<input type="text" size="10" value="<?= $strValue ?>" class="amopts-field__textbytes amopts-field-allowdisabled"<?= ($bDisabled ? ' disabled="disabled"' : '') ?>/>
		<select class="amopts-field__textbytes-type amopts-field-allowdisabled"<?= ($bDisabled ? ' disabled="disabled"' : '') ?>>
			<option value="b"<?= ($strType == "b" ? ' selected' : '') ?>><?= Loc::getMessage("AMOPT_OPTION_TYPE_TEXT_BYTES_BYTE") ?></option>
			<option value="k"<?= ($strType == "k" ? ' selected' : '') ?>><?= Loc::getMessage("AMOPT_OPTION_TYPE_TEXT_BYTES_KBYTE") ?></option>
			<option value="m"<?= ($strType == "m" ? ' selected' : '') ?>><?= Loc::getMessage("AMOPT_OPTION_TYPE_TEXT_BYTES_MBYTE") ?></option>
			<option value="g"<?= ($strType == "g" ? ' selected' : '') ?>><?= Loc::getMessage("AMOPT_OPTION_TYPE_TEXT_BYTES_GBYTE") ?></option>
		</select>
	</div>
	<?
}

function doAMOPTShowFormOptionFilesStatic($arOption, $arCurrentValue, $strBaseName, $bDisabled = false)
{
	/**
	 * @todo Добавить разделение для удобной настройки
	 */
	?>
	<textarea cols="50" rows="5" class="amopts-field-allowdisabled" name="AMOPT<?= $strBaseName ?>"<?= ($bDisabled ? ' disabled="disabled"' : '') ?>><?= htmlspecialchars($arCurrentValue) ?></textarea>
	<?
}

function doAMOPTShowFormOptionFile($arOption, $arCurrentValue, $strBaseName, $bDisabled = false)
{
	/**
	 * @todo Добавить выбор файла с сервера и диска
	 */
	?>
	<input type="text" class="amopts-field-allowdisabled" name="AMOPT<?= $strBaseName ?>" size="50" value="<?= htmlspecialchars($arCurrentValue) ?>"<?= ($bDisabled ? ' disabled="disabled"' : '') ?>/>
	<?
}

function doAMOPTShowFormOptionHeadersPreload($arOption, $arCurrentValue, $strBaseName, $bDisabled = false)
{
	/**
	 * @todo Добавить интерфейс для удобной настройки
	 */
	?>
	<textarea cols="50" rows="5" class="amopts-field-allowdisabled" name="AMOPT<?= $strBaseName ?>"<?= ($bDisabled ? ' disabled="disabled"' : '') ?>><?= htmlspecialchars($arCurrentValue) ?></textarea>
	<?
}

function doAMOPTShowFormOptionHeadersPrefetch($arOption, $arCurrentValue, $strBaseName, $bDisabled = false)
{
	/**
	 * @todo Добавить интерфейс для удобной настройки
	 */
	?>
	<textarea cols="50" rows="5" class="amopts-field-allowdisabled" name="AMOPT<?= $strBaseName ?>"<?= ($bDisabled ? ' disabled="disabled"' : '') ?>><?= htmlspecialchars($arCurrentValue) ?></textarea>
	<?
}

function doAMOPTShowFormOptionHeadersPreconnect($arOption, $arCurrentValue, $strBaseName, $bDisabled = false)
{
	/**
	 * @todo Добавить интерфейс для удобной настройки
	 */
	?>
	<textarea cols="50" rows="5" class="amopts-field-allowdisabled" name="AMOPT<?= $strBaseName ?>"<?= ($bDisabled ? ' disabled="disabled"' : '') ?>><?= htmlspecialchars($arCurrentValue) ?></textarea>
	<?
}