<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Ingo Renner <ingo@typo3.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * Index Queue indexing configuration selector form field.
 *
 * @author Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage solr
 */
class tx_solr_backend_IndexingConfigurationSelectorField {

	/**
	 * Site used to determine indexing configurations
	 *
	 * @var tx_solr_Site
	 */
	protected $site;

	/**
	 * Form element name
	 *
	 * @var string
	 */
	protected $formElementName = 'tx_solr-index-queue-indexing-configuration-selector';


	/**
	 * Constructor
	 *
	 * @param tx_solr_Site $site The site to use to determine indexing configurations
	 */
	public function __construct(tx_solr_Site $site) {
		$this->site = $site;
	}

	/**
	 * Sets the form element name.
	 *
	 * @param string $formElementName Form element name
	 */
	public function setFormElementName($formElementName) {
		$this->formElementName = $formElementName;
	}

	/**
	 * Gets the form element name.
	 *
	 * @return string form element name
	 */
	public function getFormElementName() {
		return $this->formElementName;
	}

	/**
	 * Renders a field to select which indexing configurations to initialize.
	 *
	 * Uses TCEforms.
	 *
	 *  @return string Markup for the select field
	 */
	public function render() {
		$tceForm = t3lib_div::makeInstance('t3lib_TCEforms');

		$tablesToIndex = $this->getIndexQueueConfigurationTableMap();

		$PA = array(
			'fieldChangeFunc' => array(),
			'itemFormElName' => $this->formElementName
		);

		$formField = $tceForm->getSingleField_typeSelect_checkbox(
			'', // table
			'', // field
			'', // row
			$PA, // array with additional configuration options
			array(), // config,
			$this->buildSelectorItems($tablesToIndex), // items
			'' // Label for no-matching-value
		);

			// need to wrap the field in a TCEforms table to make the CSS apply
		$form = '
		<table class="typo3-TCEforms tx_solr-TCEforms">
			<tr>
				<td>' . "\n" . $formField . "\n" . '</td>
			</tr>
		</table>
		';

		return $form;
	}

	/**
	 * Builds a map of indexing configuration names to tables to to index.
	 *
	 * @return array Indexing configuration to database table map
	 */
	protected function getIndexQueueConfigurationTableMap() {
		$indexingTableMap = array();

		$solrConfiguration = tx_solr_Util::getSolrConfigurationFromPageId($this->site->getRootPageId());

		foreach ($solrConfiguration['index.']['queue.'] as $name => $configuration) {
			if (is_array($configuration)) {
				$name = substr($name, 0, -1);

				if ($solrConfiguration['index.']['queue.'][$name]) {
					$table = $name;
					if ($solrConfiguration['index.']['queue.'][$name . '.']['table']) {
						$table = $solrConfiguration['index.']['queue.'][$name . '.']['table'];
					}

					$indexingTableMap[$name] = $table;
				}
			}
		}

		return $indexingTableMap;
	}

	/**
	 * Builds the items to render in the TCEforms select field.
	 *
	 * @param array $tablesToIndex A map of indexing configuration to database tables
	 * @return array Selectable items for the TCEforms select field
	 */
	protected function buildSelectorItems(array $tablesToIndex) {
		$selectorItems = array();

		foreach ($tablesToIndex as $configurationName => $tableName) {
			$icon = 'tcarecords-' . $tableName . '-default';
			if ($tableName == 'pages') {
				$icon = 'apps-pagetree-page-default';
			}

			$labelTableName = '';
			if ($configurationName != $tableName) {
				$labelTableName = ' (' . $tableName . ')';
			}

			$selectorItems[] = array(
				$configurationName . $labelTableName,
				$configurationName,
				$icon
			);
		}

		return $selectorItems;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/backend/class.tx_solr_backend_indexingconfigurationselectorfield.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/backend/class.tx_solr_backend_indexingconfigurationselectorfield.php']);
}

?>