<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\VideoBundle\EventListener\Dca;

use Contao\DataContainer;
use HeimrichHannot\UtilsBundle\Util\Utils;
use HeimrichHannot\VideoBundle\Collection\VideoProviderCollection;

class PageContainer
{
    /**
     * @var VideoProviderCollection
     */
    private $videoProviderCollection;
    /**
     * @var ModelInstanceChoice
     */
    private $utils;

    /**
     * PageContainer constructor.
     */
    public function __construct(VideoProviderCollection $videoProviderCollection, Utils $utils)
    {
        $this->videoProviderCollection = $videoProviderCollection;
        $this->utils = $utils;
    }

    /**
     * @param DataContainer $dc
     *
     * @return array
     */
    public function onMceVideoProviderOptionsCallback($dc)
    {
        return $this->videoProviderCollection->getVideoProvider();
    }

    /**
     * @param DataContainer $dc
     *
     * @return mixed
     */
    public function onMceLocalStorageAttribute($dc)
    {
        $options = [];

        foreach ($this->utils->model()->findModelInstancesBy('tl_tracking_object', null, null) ?? [] as $model) {
            $options[$model->id] = $model->title ?: $model->id;
        }

        return $options;
    }
}
