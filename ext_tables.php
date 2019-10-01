<?php
defined('TYPO3_MODE') || die('Access denied.');

if (
    TYPO3_MODE == 'BE'
) {
    $GLOBALS['TBE_MODULES_EXT']['xMOD_db_new_content_el']['addElClasses']['JambageCom\\Jfmulticontent\\Controller\\Plugin\\WizardIcon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath(JFMULTICONTENT_EXT) . 'Classes/Controller/Plugin/WizardIcon.php';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        JFMULTICONTENT_EXT,
        'Configuration/TypoScript/PluginSetup/',
        'Multi content'
    );
}

