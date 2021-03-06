<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2010 Matteo Lucarelli
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

include("../inc/inc.Settings.php");
include("../inc/inc.Language.php");
include("../inc/inc.Init.php");
include("../inc/inc.Extension.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

/**
 * Include class to preview documents
 */
require_once("SeedDMS/Preview.php");

$isajax = isset($_GET['action']) && ($_GET['action'] == 'items' || $_GET['action'] == 'form');
$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
$view = UI::factory($theme, $tmp[1], array('dms'=>$dms, 'user'=>$user));
$accessop = new SeedDMS_AccessOperation($dms, $user, $settings);
if (!$accessop->check_view_access($view, $_GET)) {
	UI::exitError(getMLText("my_transmittals"),getMLText("access_denied"), false, $isajax);
}

if(isset($_GET['transmittalid']) && $_GET['transmittalid']) {
	$seltransmittal = $dms->getTransmittal($_GET['transmittalid']);
} else {
	$seltransmittal = null;
}

if($view) {
	$view->setParam('seltransmittal', $seltransmittal);
	$view->setParam('showtree', showtree());
	$view->setParam('cachedir', $settings->_cacheDir);
	$view->setParam('timeout', $settings->_cmdTimeout);
	$view->setParam('previewWidthList', $settings->_previewWidthList);
	$view->setParam('previewConverters', isset($settings->_converters['preview']) ? $settings->_converters['preview'] : array());
	$view->setParam('accessobject', $accessop);
	$view->setParam('onepage', $settings->_onePageMode); // do most navigation by reloading areas of pages with ajax
	$view($_GET);
	exit;
}

?>
