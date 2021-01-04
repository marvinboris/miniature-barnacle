<?php
/**
 * Implementation of ExpiredDocuments view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");

/**
 * Include class to preview documents
 */
require_once("SeedDMS/Preview.php");

/**
 * Class which outputs the html page for ExpiredDocuments view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ExpiredDocuments extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];

		header('Content-Type: application/javascript');
		parent::jsTranslations(array('cancel', 'splash_move_document', 'confirm_move_document', 'move_document', 'confirm_transfer_link_document', 'transfer_content', 'link_document', 'splash_move_folder', 'confirm_move_folder', 'move_folder'));
		$this->printDeleteDocumentButtonJs();
		/* Add js for catching click on document in one page mode */
		$this->printClickDocumentJs();
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$orderby = $this->params['orderby'];
		$orderdir = $this->params['orderdir'];
		$cachedir = $this->params['cachedir'];
		$previewwidth = $this->params['previewWidthList'];
		$timeout = $this->params['timeout'];
		$xsendfile = $this->params['xsendfile'];
		$order = $orderby.$orderdir;

		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/bootbox/bootbox.min.js"></script>'."\n", 'js');

		$db = $dms->getDB();
		$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidth, $timeout, $xsendfile);

		$this->htmlStartPage(getMLText("expired_documents"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("expired_documents"), "admin_tools");

		$this->contentHeading(getMLText("expired_documents"));
//		$this->contentContainerStart();

		if($docs = $dms->getDocumentsExpired(-1400, null, $orderby, $orderdir, true)) {

			print "<table class=\"table table-condensed\">";
			print "<thead>\n<tr>\n";
			print "<th></th>";
			print "<th>".getMLText("name");
			print " <a href=\"../out/out.ExpiredDocuments.php?".($order=="na"?"&orderby=n&orderdir=d":"&orderby=n&orderdir=a")."\" \"title=\"".getMLText("sort_by_name")."\">".($order=="na"?' <i class="icon-sort-by-alphabet selected"></i>':($order=="nd"?' <i class="icon-sort-by-alphabet-alt selected"></i>':' <i class="icon-sort-by-alphabet"></i>'))."</a>";
			print " <a href=\"../out/out.ExpiredDocuments.php?".($order=="ea"?"&orderby=e&orderdir=d":"&orderby=e&orderdir=a")."\" \"title=\"".getMLText("sort_by_expiration_date")."\">".($order=="ea"?' <i class="icon-sort-by-order selected"></i>':($order=="ed"?' <i class="icon-sort-by-order-alt selected"></i>':' <i class="icon-sort-by-order"></i>'))."</a>";
			print "</th>\n";
			print "<th>".getMLText("status")."</th>\n";
			print "<th>".getMLText("action")."</th>\n";
			print "</tr>\n</thead>\n<tbody>\n";

			$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidth, $timeout, $xsendfile);
			foreach ($docs as $document) {
				echo $this->documentListRow($document, $previewer);
			}
			print "</tbody></table>";
		}
		else $this->infoMsg("no_docs_expired");
		
//		$this->contentContainerEnd();

		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
