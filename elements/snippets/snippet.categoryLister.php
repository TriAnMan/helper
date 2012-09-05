<?php
/**
 * Сниппет обеспечивает вывод стрелочек вперед, назад по категориям
 */
if(!class_exists('Helper')) {
	$sModelPath = $modx->getOption('core_path').'components/helper/model/';
	include_once $sModelPath.'helper.class.php';
}

$id = $modx->resource->get('id');

//органичиваем вывод для определенных айдишников
if( isset($scriptProperties['skipOnIds'])){
  $arSkipOnIds = explode(',',$scriptProperties['skipOnIds']);
  if( in_array($id, $arSkipOnIds)){
	  return '';
  }
}

// Обрабатываем входные параметры
$bNext = isset($scriptProperties['next']) && $scriptProperties['next'] == true;
$bFolder = isset($scriptProperties['folder']) && $scriptProperties['folder'] == true;
$sText = isset($scriptProperties['text']) ? $scriptProperties['text'] : '';
$sHtmlOptions = isset($scriptProperties['htmlOptions']) ? $scriptProperties['htmlOptions'] : '';

if (!$arResource = Helper::getFirstSibling($bNext, $bFolder)){
	return '';
}

$sUrl = $modx->makeUrl($arResource['id']);
$sText = '<a href="' .$sUrl. '" ' .$sHtmlOptions. '>' . ($sText != '' ? $sText : $arResource['pagetitle']). '</a>';
$sText = $bNext ? " &raquo; " .$sText : $sText. " &laquo; ";

return $sText;