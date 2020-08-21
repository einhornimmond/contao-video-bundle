<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$GLOBALS['TL_CTE']['media'][\HeimrichHannot\VideoBundle\ContentElement\VideoElement::TYPE] = \HeimrichHannot\VideoBundle\ContentElement\VideoElement::class;

$GLOBALS['TL_HOOKS']['loadDataContainer'][] = [\HeimrichHannot\VideoBundle\EventListener\LoadDataContainerListener::class, 'onLoadDataContainer'];
$GLOBALS['TL_HOOKS']['parseArticles'][] = [\HeimrichHannot\VideoBundle\EventListener\ParseArticlesListener::class, 'onParseArticles'];
$GLOBALS['TL_HOOKS']['sqlGetFromDca']['videoBundle'] = [\HeimrichHannot\VideoBundle\EventListener\SqlGetDataListener::class, 'onSqlGetFromDca'];
