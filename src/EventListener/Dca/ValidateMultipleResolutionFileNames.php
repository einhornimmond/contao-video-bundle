<?php

namespace HeimrichHannot\VideoBundle\EventListener\Dca;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\StringUtil;
use Contao\FilesModel;
use HeimrichHannot\VideoBundle\Video\FileVideoMultipleResolution;

#[AsCallback(table: 'tl_content', target: 'fields.multiResolutionVideoSRC.save')]
class ValidateMultipleResolutionFileNames
{
    public function __invoke($value, DataContainer $dc)
    {
        $files = StringUtil::deserialize($value, true);
        foreach($files as $file) {
            $fileObject = FilesModel::findByUuid($file);
            if (!$fileObject || !preg_match(FileVideoMultipleResolution::getFilePathRegExp(), $fileObject->path)) {
                throw new \Exception("File name didn't match expectations, expected for example: file_name_720.mp4");
            }
        }
        
        return $value;
    }
}