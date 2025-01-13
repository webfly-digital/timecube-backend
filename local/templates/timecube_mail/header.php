<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=600">
</head>

<body style="margin: 0px;">
<? if (\Bitrix\Main\Loader::includeModule('mail')) : ?>
<?=\Bitrix\Mail\Message::getQuoteStartMarker(true); ?>
<? endif; ?>
<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
    <tr>
        <td width="100%" bgcolor="#ffffff" valign="top" align="center" style="padding: 15px 0px 15px 0px;">
