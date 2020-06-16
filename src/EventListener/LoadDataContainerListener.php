<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\VideoBundle\EventListener;


use Contao\CoreBundle\DataContainer\PaletteManipulator;
use HeimrichHannot\VideoBundle\EventListener\Dca\PageContainer;
use HeimrichHannot\VideoBundle\Generator\DcaFieldGenerator;

class LoadDataContainerListener
{
    const PALETTE_VIDEO = 'videoProvider';
    const PALETTE_PLAYER = 'videoFullsize,videoAutoplay';

    /**
     * @var array
     */
    private $bundleConfig;

    /**
     * LoadDataContainerListener constructor.
     */
    public function __construct(array $bundleConfig)
    {
        $this->bundleConfig = $bundleConfig;
    }


    /**
     * Hook loadDataContainer
     *
     * @param string $table
     */
    public function onLoadDataContainer(string $table): void
    {
        switch ($table)
        {
            case 'tl_news':
                $this->prepareNewsTable();
                break;
            case 'tl_page':
                $this->preparePageTable();
                break;

        }
    }

    protected function prepareNewsTable()
    {
        if (!isset($this->bundleConfig['enableNewsSupport']) || true !== $this->bundleConfig['enableNewsSupport']) {
            return;
        }
        if (!class_exists('Contao\NewsBundle\ContaoNewsBundle')) {
            return;
        }

        $dca = &$GLOBALS['TL_DCA']['tl_news'];
        $videoPalette = DcaFieldGenerator::addSingleLegendPalette('tl_news');

        foreach ($dca['palettes'] as $paletteName => $palette) {
            if (!is_string($palette)) {
                continue;
            }
            $paletteManipulator = PaletteManipulator::create()
                ->addLegend("video_legend", "image_legend", PaletteManipulator::POSITION_BEFORE)
                ->addField("addVideo", "video_legend", PaletteManipulator::POSITION_APPEND)
                ->applyToPalette($paletteName, 'tl_news');

//            $dca['palettes'][$paletteName] = str_replace('{image_legend}', $videoPalette.'{image_legend}', $dca['palettes'][$paletteName]);
        }
    }

    protected function preparePageTable()
    {
        $this->enablePrivacyCenterSupport();
    }

    protected function enablePrivacyCenterSupport()
    {
        if (!class_exists('HeimrichHannot\PrivacyCenterBundle\HeimrichHannotPrivacyCenterBundle')) {
            return;
        }
        if (!class_exists('HeimrichHannot\MultiColumnEditorBundle\HeimrichHannotContaoMultiColumnEditorBundle')) {
            trigger_error(
                "HeimrichHannotContaoMultiColumnEditorBundle not found. Multi Column Editor bundle is needed for privacy center integration.",
                E_USER_WARNING);
            return;
        }

        $dca = &$GLOBALS['TL_DCA']['tl_page'];
        $dca['palettes']['__selector__'][] = 'usePrivacyCenter';

        foreach (['root','rootfallback'] as $paletteName) {
            if (!isset($dca['palettes'][$paletteName])) {
                continue;
            }
            $dca['palettes'][$paletteName] = str_replace(';{sitemap_legend', ',usePrivacyCenter;{sitemap_legend', $dca['palettes'][$paletteName]);
        }

        $dca['fields']['usePrivacyCenter'] = [
            'label'                   => &$GLOBALS['TL_LANG']['tl_page']['usePrivacyCenter'],
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'eval'                    => ['tl_class' => 'w50', 'submitOnChange' => true],
            'sql'                     => "char(1) NOT NULL default ''"
        ];
        $dca['fields']['privacyCenterLocalStorageAttribute'] = [
            'label'     => &$GLOBALS['TL_LANG']['tl_page']['privacyCenterLocalStorageAttribute'],
            'inputType' => 'multiColumnEditor',
            'eval'      => [
                'tl_class'          => 'long clr',
                'multiColumnEditor' => [
                    'fields' => [
                        'videoProvider'         => [
                            'label'            => &$GLOBALS['TL_LANG']['tl_page']['videoProvider'],
                            'exclude'          => true,
                            'filter'           => true,
                            'inputType'        => 'select',
                            'options_callback' => [PageContainer::class, 'onMceVideoProviderOptionsCallback'],
                            'eval'             => ['groupStyle' => 'width: 49%', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
                        ],
                        'localStorageAttribute' => [
                            'label'            => &$GLOBALS['TL_LANG']['tl_page']['localStorageAttribute'],
                            'exclude'          => true,
                            'filter'           => true,
                            'inputType'        => 'select',
                            'options_callback' => [PageContainer::class, 'onMceLocalStorageAttribute'],
                            'eval'             => ['groupStyle' => 'width: 49%', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
                        ],
                    ],
                ],
            ],
            'sql'       => "blob NULL",
        ];
    }
}