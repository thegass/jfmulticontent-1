<?php
defined('TYPO3_MODE') || die('Access denied.');

if (!defined ('JFMULTICONTENT_EXT')) {
    define('JFMULTICONTENT_EXT', 'jfmulticontent');
}

if (!defined ('T3JQUERY')) {
    define('T3JQUERY', false);
}

call_user_func(function () {
    $extensionConfiguration = array();

    if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][JFMULTICONTENT_EXT])) {
        $extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][JFMULTICONTENT_EXT]);
    } else if (
        defined('TYPO3_version') &&
        version_compare(TYPO3_version, '9.0.0', '>=')
    ) {
        $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
        )->get(JFMULTICONTENT_EXT);
    }
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][JFMULTICONTENT_EXT] = $extensionConfiguration;

    if ($extensionConfiguration['ttNewsCodes']) {
        // Add the additional CODES to tt_news
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['what_to_display'][] = array(
            0 => 'LIST_ACCORDION',
            1 => 'LIST_ACCORDION'
        );
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['what_to_display'][] = array(
            0 => 'LIST_SLIDER',
            1 => 'LIST_SLIDER'
        );
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['what_to_display'][] = array(
            0 => 'LIST_SLIDEDECK',
            1 => 'LIST_SLIDEDECK'
        );
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['what_to_display'][] = array(
            0 => 'LIST_EASYACCORDION',
            1 => 'LIST_EASYACCORDION'
        );
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['extraCodesHook'][]        = 'EXT:' . JFMULTICONTENT_EXT . '/lib/class.tx_jfmulticontent_ttnews_extend.php:tx_jfmulticontent_ttnews_extend';
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['extraGlobalMarkerHook'][] = 'EXT:' . JFMULTICONTENT_EXT . '/lib/class.tx_jfmulticontent_ttnews_extend.php:tx_jfmulticontent_ttnews_extend';
    }


    $listType = 'jfmulticontent_pi1';

    // Page module hook
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$listType][JFMULTICONTENT_EXT] = 'JambageCom\\Jfmulticontent\\Hooks\\CmsBackend->getExtensionSummary';

    // Save the content
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][JFMULTICONTENT_EXT] = \JambageCom\Jfmulticontent\Hooks\CmsBackend::class;

    if ($extensionConfiguration['addBrowseLinks']) {
        // Add browseLinksHook
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.browse_links.php']['browseLinksHook'][] = \JambageCom\Jfmulticontent\Hooks\ElementBrowser::class;
        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('rtehtmlarea')) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/rtehtmlarea/mod3/class.tx_rtehtmlarea_browse_links.php']['browseLinksHook'][] = \JambageCom\Jfmulticontent\Hooks\ElementBrowser::class;
        }
    }

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43(JFMULTICONTENT_EXT, 'pi1/class.tx_jfmulticontent_pi1.php', '_pi1', 'list_type', 1);
});

