<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '';

$strReturn .= '<ul class="breadcrumbs-menu" itemscope itemtype="https://schema.org/BreadcrumbList">';

$itemSize = count($arResult);
for($index = 0; $index < $itemSize; $index++)
{
    if ($arResult[$index]["LINK"] == '/'.WF_CATALOG_ROOT.'/')
        continue;

	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	$arrow = '';//($index > 0? '<i class="bx-breadcrumb-item-angle fa fa-angle-right"></i>' : '');

	if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
	{
		$strReturn .=  $arrow.
			'<li id="bx_breadcrumb_'.$index.'" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
				<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'" itemprop="item"><span itemprop="name">'.$title.'</span></a>
				<meta itemprop="position" content="'.($index + 1).'" />
			</li>';
	}
	else
	{
		$strReturn .= $arrow. '<li><span>'.$title.'</span></li>';
	}
}

$strReturn .= '</ul>';

return $strReturn;

