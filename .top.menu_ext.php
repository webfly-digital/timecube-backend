<?
$aMenuLinksMain = $aMenuLinks;
$aMenuLinks = [];
include_once '.footer.menu.php';
$aMenuLinksExt = $aMenuLinks;
$aMenuLinks = array_merge($aMenuLinksMain, $aMenuLinksExt);