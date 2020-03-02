<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\VideoBundle\DependencyInjection;


use HeimrichHannot\VideoBundle\Video\YouTubeVideo;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class HeimrichHannotVideoExtension extends Extension
{

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        if (!isset($config['videoTypes']['youtube'])) {
            $config['videoTypes']['youtube']['class'] = YouTubeVideo::class;
        }
        $container->setParameter('huh_video', $config);
    }

    public function getAlias()
    {
        return 'huh_video';
    }


}