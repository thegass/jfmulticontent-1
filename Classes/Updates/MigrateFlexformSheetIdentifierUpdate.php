<?php
namespace JambageCom\Jfmulticontent\Updates;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with TYPO3 source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use JambageCom\Jfmulticontent\Utility\DatabaseUtility;
use TYPO3\CMS\Core\Charset\CharsetConverter;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\AbstractUpdate;

/**
 * Migrate Flexform sheet identifier
 *
 * Before TYPO3 9 jfmulticontent named tabs in flexform configuration have been stored in the tt_content records like this:
 * <general></general>
 *
 * Starting with TYPO3 9 the tabs are stored as in the flexform file
 * <s_general></s_general>
 *
 * If you do not execute this update script, this has the effect that all your flexform data is broken. Now the sheets have a named identifier with the leading 's_'.
 *
 * The flexform configuration looks like this now:
 * <s_general></s_general>
 *
 */
class MigrateFlexformSheetIdentifierUpdate extends AbstractUpdate
{
    /**
     * @var string
     */
    protected $title = 'EXT:' . JFMULTICONTENT_EXT . ' - Migrate flexform sheet identifiers';

    /**
     * @var string
     */
    protected $identifier = JFMULTICONTENT_EXT . 'MigrateFlexformSheetIdentifierUpdate';

    /**
     * Checks whether updates are required.
     *
     * @param string &$description The description for the update
     * @return bool Whether an update is required (TRUE) or not (FALSE)
     */
    public function checkForUpdate (&$description)
    {
        $contentElementsWithWrongSheetTitle = $this->getUpdatableContentElements(1);

        $description .= 'There are <b>' . \count($contentElementsWithWrongSheetTitle) . ' content elements</b> with sheet titles which are missing the leading \'s_\' ' .
            '.<br>' .
            'Caution! Please make sure that you\'ve made a backup copy of tt_content ' .
            'before executing this update wizard.<br><br>';

        return \count($contentElementsWithWrongSheetTitle) > 0;
    }

    /**
     * Performs the accordant updates.
     *
     * @param array &$dbQueries Queries done in this update
     * @param string|array &$customMessages Custom messages
     * @return bool Whether everything went smoothly or not
     */
    public function performUpdate (array &$dbQueries, &$customMessages)
    {
        /** @var FlexFormTools $flexFormTools */
        $flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);
        $updatableContentElements = $this->getUpdatableContentElements();

        foreach ($updatableContentElements as $contentElement) {
            $flexformData = GeneralUtility::xml2array($contentElement['pi_flexform']);
            $newFlexformData = ['data' => []];
            if (!empty($flexformData['data'])) {
                foreach ($flexformData['data'] as $sheetIdentifier => $sheetData) {
                    if (strpos($sheetIdentifier, 's_') === false) {
                        $newFlexformData['data']['s_' . $sheetIdentifier] = $sheetData;
                    } else {
                        $newFlexformData['data'][$sheetIdentifier] = $sheetData;
                    }
                }
            }
            $connection = DatabaseUtility::getConnectionPool()->getConnectionForTable('tt_content');
            $connection->update(
                'tt_content',
                [
                    'pi_flexform' => $flexFormTools->flexArray2Xml($newFlexformData, true)
                ],
                [
                    'uid' => (int) $contentElement['uid']
                ]
            );
        }
        return true;
    }

    /**
     * Returns content elements, based on DCE, with old sheet identifier
     *
     * @return array tt_content rows
     */
    protected function getUpdatableContentElements ($limit = 0) : array
    {
        $queryBuilder = DatabaseUtility::getConnectionPool()->getQueryBuilderForTable('tt_content');
        $query = $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->like(
                    'list_type',
                    $queryBuilder->createNamedParameter(JFMULTICONTENT_EXT . '_pi1')
                ),
                $queryBuilder->expr()->notLike(
                    'pi_flexform',
                    $queryBuilder->createNamedParameter('%<sheet index=\"s\\_%')
                )
            );
            
        if ($limit) {
            $query = $query->setMaxResults($limit);
        }
        $sql = $query->getSQL();
        $parameters = $query->getParameters();
        $search = array();
        $replace = array();
        foreach ($parameters as $key => $value) {
            $search[] = ':' . $key;
            $replace[] = '\'' . $value . '\'';
        }

        $sql = str_replace($search, $replace, $sql);    

        $result = $query->execute()
            ->fetchAll();

        return $result;
    }
}
