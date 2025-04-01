<?
global $APPLICATION;
?><div class="zver-filter">
	<div class="flex-it"><?
//	 mp($arResult["ITEMS"]);
		foreach($arResult["ITEMS"] as $key=>$arItem)//prices
		{
			$key = $arItem["ENCODED_ID"];
			if(isset($arItem["PRICE"])):
				if ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0)
					continue;

				if(!empty($seofilterSetting['PARAMS'][$arItem["VALUES"]["MIN"]["CONTROL_NAME"]]))
					$arItem["VALUES"]["MIN"]["HTML_VALUE"] = $seofilterSetting['PARAMS'][$arItem["VALUES"]["MIN"]["CONTROL_NAME"]];
				if(!empty($seofilterSetting['PARAMS'][$arItem["VALUES"]["MAX"]["CONTROL_NAME"]]))
					$arItem["VALUES"]["MAX"]["HTML_VALUE"] = $seofilterSetting['PARAMS'][$arItem["VALUES"]["MAX"]["CONTROL_NAME"]];
				?>
				<div class="bx-filter-parameters-box">
					<div class="bx-filter-parameters-box-title"><?=$arItem["NAME"]?></div>
					<div class="bx-filter-block">
						<div class="bx-filter-parameters-box-container">
							<div class="bx-filter-parameters-box-container-block flex-it">
								<i class="bx-ft-sub"><?=GetMessage("SEOFILTER_CT_BCSF_FILTER_FROM")?></i>
								<div class="bx-filter-input-container">
									<input
										class="min-price"
										type="text"
										name="PARAMS[<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>]"
										id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
										value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
										placeholder="<?echo $arItem["VALUES"]["MIN"]["VALUE"]?>"
										size="10"
									/>
								</div>
							</div>
							<div class="bx-filter-parameters-box-container-block flex-it">
								<i class="bx-ft-sub"><?=GetMessage("SEOFILTER_CT_BCSF_FILTER_TO")?></i>
								<div class="bx-filter-input-container">
									<input
										class="max-price"
										type="text"
										name="PARAMS[<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>]"
										id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
										value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
										placeholder="<?echo $arItem["VALUES"]["MAX"]["VALUE"]?>"
										size="10"
									/>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?endif;
		}

		//not prices
		foreach($arResult["ITEMS"] as $key=>$arItem)
		{
			if(
				empty($arItem["VALUES"])
				|| isset($arItem["PRICE"])
			)
				continue;

			if (
				$arItem["DISPLAY_TYPE"] == "A"
				&& (
					$arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0
				)
			)
				continue;
			?>
			<div class="bx-filter-parameters-box">
				<div class="bx-filter-parameters-box-title"><?if($isCheckAll == 'Y' && in_array($arItem["DISPLAY_TYPE"], ['P', 'F', 'G'])){?> <input type="checkbox" title="<?=GetMessage("SEOFILTER_CT_SELECT_ALL")?>" checked="checked" onclick="setAllCheck(this);return false;"> <?}?><?=$arItem["NAME"]?> <?$APPLICATION->ShowViewContent("field_".$arItem["CODE"])?></div>
				<div class="bx-filter-block">
					<div class="bx-filter-parameters-box-container dtype-<?=$arItem["DISPLAY_TYPE"]?>">
					<?
					$arCur = current($arItem["VALUES"]);
					switch ($arItem["DISPLAY_TYPE"])
					{
						case "A"://NUMBERS_WITH_SLIDER
							if(!empty($seofilterSetting['PARAMS'][$arItem["VALUES"]["MIN"]["CONTROL_NAME"]]))
								$arItem["VALUES"]["MIN"]["HTML_VALUE"] = $seofilterSetting['PARAMS'][$arItem["VALUES"]["MIN"]["CONTROL_NAME"]];
							if(!empty($seofilterSetting['PARAMS'][$arItem["VALUES"]["MAX"]["CONTROL_NAME"]]))
								$arItem["VALUES"]["MAX"]["HTML_VALUE"] = $seofilterSetting['PARAMS'][$arItem["VALUES"]["MAX"]["CONTROL_NAME"]];

							?><div class="bx-filter-parameters-box-container-block flex-it">
								<i class="bx-ft-sub"><?=GetMessage("SEOFILTER_CT_BCSF_FILTER_FROM")?></i>
								<div class="bx-filter-input-container">
									<input
										class="min-price"
										type="text"
										name="PARAMS[<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>]"
										id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
										value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
										placeholder="<?echo $arItem["VALUES"]["MIN"]["VALUE"]?>"
										size="10"
									/>
								</div>
							</div>
							<div class="bx-filter-parameters-box-container-block flex-it">
								<i class="bx-ft-sub"><?=GetMessage("SEOFILTER_CT_BCSF_FILTER_TO")?></i>
								<div class="bx-filter-input-container">
									<input
										class="max-price"
										type="text"
										name="PARAMS[<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>]"
										id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
										value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
										placeholder="<?echo $arItem["VALUES"]["MAX"]["VALUE"]?>"
										size="10"
									/>
								</div>
							</div><?
							break;
						case "B"://NUMBERS
							if(!empty($seofilterSetting['PARAMS'][$arItem["VALUES"]["MIN"]["CONTROL_NAME"]]))
								$arItem["VALUES"]["MIN"]["HTML_VALUE"] = $seofilterSetting['PARAMS'][$arItem["VALUES"]["MIN"]["CONTROL_NAME"]];
							if(!empty($seofilterSetting['PARAMS'][$arItem["VALUES"]["MAX"]["CONTROL_NAME"]]))
								$arItem["VALUES"]["MAX"]["HTML_VALUE"] = $seofilterSetting['PARAMS'][$arItem["VALUES"]["MAX"]["CONTROL_NAME"]];
							?>
							<div class="bx-filter-parameters-box-container-block flex-it">
								<i class="bx-ft-sub"><?=GetMessage("SEOFILTER_CT_BCSF_FILTER_FROM")?></i>
								<div class="bx-filter-input-container">
									<input
										class="min-price"
										type="text"
										name="PARAMS[<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>]"
										id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
										value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
										placeholder="<?echo $arItem["VALUES"]["MIN"]["VALUE"]?>"
										size="10"
										/>
								</div>
							</div>
							<div class="bx-filter-parameters-box-container-block flex-it">
								<i class="bx-ft-sub"><?=GetMessage("SEOFILTER_CT_BCSF_FILTER_TO")?></i>
								<div class="bx-filter-input-container">
									<input
										class="max-price"
										type="text"
										name="PARAMS[<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>]"
										id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
										value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
										placeholder="<?echo $arItem["VALUES"]["MAX"]["VALUE"]?>"
										size="10"
										/>
								</div>
							</div>
							<?
							break;
						case "G"://CHECKBOXES_WITH_PICTURES
							?><div class="bx-filter-param-btn-inline checkbox-with-picture"><?
								$cnt = 0;
								foreach ($arItem["VALUES"] as $val => $ar):
								if(!empty($seofilterSetting['PARAMS'][$ar["CONTROL_NAME"]])){
									$ar["CHECKED"] = "Y";
									$cnt++;
								}

								?>
									<label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label"<?
										if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?> style="background-image:url(<?=$ar["FILE"]["SRC"]?>);"<?
									endif?> title='<?=$ar['VALUE']?>'>
										<input
											style="display: none"
											type="checkbox"
											name="PARAMS[<?=$ar["CONTROL_NAME"]?>]"
											id="<?=$ar["CONTROL_ID"]?>"
											value="<?=$ar["HTML_VALUE"]?>"
											<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
										/>
									</label>
								<?endforeach?>
								</div><?
								if($cnt > 0){
									$APPLICATION->AddViewContent("field_".$arItem["CODE"], '<div class="count-title">'.$cnt.'</div>');
								}
							break;
						case "H"://CHECKBOXES_WITH_PICTURES_AND_LABELS
							?><div class="bx-filter-param-btn-block"><?
								$cnt = 0;
								foreach ($arItem["VALUES"] as $val => $ar):
								if(!empty($seofilterSetting['PARAMS'][$ar["CONTROL_NAME"]])){
									$ar["CHECKED"] = "Y";
									$cnt++;
								}
								?><div class="bx-filter-checkbox-with-label"><input
										style="display: none"
										type="checkbox"
										name="PARAMS[<?=$ar["CONTROL_NAME"]?>]"
										id="<?=$ar["CONTROL_ID"]?>"
										value="<?=$ar["HTML_VALUE"]?>"
										<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
									/>
									<label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label">
										<span class="bx-filter-param-text" title="<?=$ar["VALUE"];?>"><?=$ar["VALUE"];?><?
										if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
											?> (<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)<?
										endif;?></span>
										<?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
											<span class="bx-filter-btn-color-icon" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
										<?endif?>
									</label></div>
								<?endforeach?>
							</div><?
							if($cnt > 0){
								$APPLICATION->AddViewContent("field_".$arItem["CODE"], '<div class="count-title">'.$cnt.'</div>');
							}
							break;
						/*case "P"://DROPDOWN
							$checkedItemExist = false;
							?><div class="bx-filter-select-container">
									<div class="bx-filter-select-block">
										<div class="bx-filter-select-text" data-role="currentOption"><?
											foreach ($arItem["VALUES"] as $val => $ar)
											{
												if ($ar["CHECKED"])
												{
													echo $ar["VALUE"];
													$checkedItemExist = true;
												}
											}
											if (!$checkedItemExist)
											{
												echo GetMessage("CT_BCSF_FILTER_ALL");
											}
											?>
										</div>
										<div class="bx-filter-select-arrow"></div>
										<input
											style="display: none"
											type="radio"
											name="'PARAMS[<?=$arCur["CONTROL_NAME_ALT"]?>]"
											id="<? echo "all_".$arCur["CONTROL_ID"] ?>"
											value=""
										/>
										<?foreach ($arItem["VALUES"] as $val => $ar):?>
											<input
												style="display: none"
												type="radio"
												name="'PARAMS[<?=$ar["CONTROL_NAME_ALT"]?>]"
												id="<?=$ar["CONTROL_ID"]?>"
												value="<? echo $ar["HTML_VALUE_ALT"] ?>"
												<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
											/>
										<?endforeach?>
										<div class="bx-filter-select-popup" data-role="dropdownContent" style="display: none;">
											<ul>
												<li>
													<label for="<?="all_".$arCur["CONTROL_ID"]?>" class="bx-filter-param-label" data-role="label_<?="all_".$arCur["CONTROL_ID"]?>" >
														<? echo GetMessage("CT_BCSF_FILTER_ALL"); ?>
													</label>
												</li>
											<?
											foreach ($arItem["VALUES"] as $val => $ar):
											?><li>
												<label for="<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label<?=$class?>" data-role="label_<?=$ar["CONTROL_ID"]?>"><?=$ar["VALUE"]?></label>
												</li>
											<?endforeach?>
											</ul>
										</div>
									</div>
							</div><?
							break;*/
						case "R"://DROPDOWN_WITH_PICTURES_AND_LABELS
							?><div class="bx-filter-select-container">
								<div class="bx-filter-select-block">
									<div class="bx-filter-select-text fix" data-role="currentOption">
										<?
										$cnt = 0;
										$checkedItemExist = false;
										foreach ($arItem["VALUES"] as $val => $ar):
											if ($ar["CHECKED"])
											{
													$cnt++;
											?>
												<?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
													<span class="bx-filter-btn-color-icon" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
												<?endif?>
												<span class="bx-filter-param-text">
													<?=$ar["VALUE"]?>
												</span>
											<?
												$checkedItemExist = true;
											}
										endforeach;
										if (!$checkedItemExist)
										{
											?><span class="bx-filter-btn-color-icon all"></span> <?
											echo GetMessage("CT_BCSF_FILTER_ALL");
										}
										?>
									</div>
									<div class="bx-filter-select-arrow"></div>
									<input
										style="display: none"
										type="radio"
										name="PARAMS[<?=$arCur["CONTROL_NAME_ALT"]?>]"
										id="<? echo "all_".$arCur["CONTROL_ID"] ?>"
										value=""
									/>
									<?foreach ($arItem["VALUES"] as $val => $ar):?>
										<input
											style="display: none"
											type="radio"
											name="PARAMS[<?=$ar["CONTROL_NAME_ALT"]?>]"
											id="<?=$ar["CONTROL_ID"]?>"
											value="<?=$ar["HTML_VALUE_ALT"]?>"
											<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
										/>
									<?endforeach?>
									<div class="bx-filter-select-popup" data-role="dropdownContent" style="display: none">
										<ul>
											<li style="border-bottom: 1px solid #e5e5e5;padding-bottom: 5px;margin-bottom: 5px;">
												<label for="<?="all_".$arCur["CONTROL_ID"]?>" class="bx-filter-param-label" data-role="label_<?="all_".$arCur["CONTROL_ID"]?>">
													<span class="bx-filter-btn-color-icon all"></span>
													<? echo GetMessage("CT_BCSF_FILTER_ALL"); ?>
												</label>
											</li>
										<?
										foreach ($arItem["VALUES"] as $val => $ar):
										?><li>
												<label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label">
													<?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
														<span class="bx-filter-btn-color-icon" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
													<?endif?>
													<span class="bx-filter-param-text">
														<?=$ar["VALUE"]?>
													</span>
												</label>
											</li>
										<?endforeach?>
										</ul>
									</div>
								</div>
							</div><?
							if($cnt > 0){
								$APPLICATION->AddViewContent("field_".$arItem["CODE"], '<div class="count-title">'.$cnt.'</div>');
							}
							break;
						case "K"://RADIO_BUTTONS
							?><div class="radio">
									<label class="bx-filter-param-label" for="<? echo "all_".$arCur["CONTROL_ID"] ?>">
										<span class="bx-filter-input-checkbox">
											<input
												type="radio"
												value=""
												name="PARAMS[<? echo $arCur["CONTROL_NAME_ALT"] ?>]"
												id="<? echo "all_".$arCur["CONTROL_ID"] ?>"
											/>
											<span class="bx-filter-param-text"><? echo GetMessage("CT_BCSF_FILTER_ALL"); ?></span>
										</span>
									</label>
								</div>
								<?foreach($arItem["VALUES"] as $val => $ar):?>
									<div class="radio">
										<label data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label" for="<? echo $ar["CONTROL_ID"] ?>">
											<span class="bx-filter-input-checkbox <? echo $ar["DISABLED"] ? 'disabled': '' ?>">
												<input
													type="radio"
													value="<? echo $ar["HTML_VALUE_ALT"] ?>"
													name="PARAMS[<? echo $ar["CONTROL_NAME_ALT"] ?>]"
													id="<? echo $ar["CONTROL_ID"] ?>"
													<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
												/>
												<span class="bx-filter-param-text" title="<?=$ar["VALUE"];?>"><?=$ar["VALUE"];?><?
												if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
													?>&nbsp;(<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)<?
												endif;?></span>
											</span>
										</label>
									</div>
								<?endforeach;?><?
							break;
						case "U"://CALENDAR
							?><div class="bx-filter-parameters-box-container-block"><div class="bx-filter-input-container bx-filter-calendar-container">
									<?$APPLICATION->IncludeComponent(
										'bitrix:main.calendar',
										'',
										array(
											'FORM_NAME' => $arResult["FILTER_NAME"]."_form",
											'SHOW_INPUT' => 'Y',
											'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="'.FormatDate("SHORT", $arItem["VALUES"]["MIN"]["VALUE"]).'" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
											'INPUT_NAME' => 'PARAMS['.$arItem["VALUES"]["MIN"]["CONTROL_NAME"].']',
											'INPUT_VALUE' => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
											'SHOW_TIME' => 'N',
											'HIDE_TIMEBAR' => 'Y',
										),
										null,
										array('HIDE_ICONS' => 'Y')
									);?>
								</div></div>
								<div class="bx-filter-parameters-box-container-block"><div class="bx-filter-input-container bx-filter-calendar-container">
									<?$APPLICATION->IncludeComponent(
										'bitrix:main.calendar',
										'',
										array(
											'FORM_NAME' => $arResult["FILTER_NAME"]."_form",
											'SHOW_INPUT' => 'Y',
											'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="'.FormatDate("SHORT", $arItem["VALUES"]["MAX"]["VALUE"]).'" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
											'INPUT_NAME' => 'PARAMS['.$arItem["VALUES"]["MAX"]["CONTROL_NAME"].']',
											'INPUT_VALUE' => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
											'SHOW_TIME' => 'N',
											'HIDE_TIMEBAR' => 'Y',
										),
										null,
										array('HIDE_ICONS' => 'Y')
									);?>
								</div></div><?
							break;
						default://CHECKBOXES
							$cnt = 0;
							foreach($arItem["VALUES"] as $val => $ar):
								if(!empty($seofilterSetting['PARAMS'][$ar["CONTROL_NAME"]])){
									$ar["CHECKED"] = "Y";
									$cnt++;
								}
								?><div class="checkbox">
									<label data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx-filter-param-label <? echo $ar["DISABLED"] ? 'disabled': '' ?>" for="<? echo $ar["CONTROL_ID"] ?>">
										<span class="bx-filter-input-checkbox">
											<input
												type="checkbox"
												value="<? echo $ar["HTML_VALUE"] ?>"
												name="PARAMS[<? echo $ar["CONTROL_NAME"] ?>]"
												id="<? echo $ar["CONTROL_ID"] ?>"
												<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
											/>
											<span class="bx-filter-param-text" title="<?=$ar["VALUE"];?>"><?=htmlspecialcharsback($ar["VALUE"]);?><?
											if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])):
												?>&nbsp;(<span data-role="count_<?=$ar["CONTROL_ID"]?>"><? echo $ar["ELEMENT_COUNT"]; ?></span>)<?
											endif;?></span>
										</span>
									</label>
								</div>
							<?endforeach;
							if($cnt > 0){
								$APPLICATION->AddViewContent("field_".$arItem["CODE"], '<div class="count-title">'.$cnt.'</div>');
							}
						break;
					}
					?>
					</div>
				</div>
			</div><?
		}
	?></div><!--//row-->
</div>