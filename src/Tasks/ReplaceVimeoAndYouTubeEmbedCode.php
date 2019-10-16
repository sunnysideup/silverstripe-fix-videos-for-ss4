<?php

namespace Sunnysideup\FixVideosForSS4\Tasks;

use DOMDocument;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Core\ClassInfo;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\DataObject;




class ReplaceVimeoAndYouTubeEmbedCodeTask extends BuildTask
{

    protected $title = 'Fix legacy YouTube and Vimeo';

    protected $description = 'Runs through all the HTMLText fields.';

    public function run($request)
    {
        $api = new ReplaceVimeoAndYouTubeEmbedCode();

        // Get class names for page types that are not virtual pages or redirector pages
        $classes = ClassInfo::subclassesFor(DataObject::class);
        foreach($classes as $className) {
            $fields = Config::inst()->get($className, 'db');
            foreach($fields as $fieldName => $fieldType) {
                if($fieldType === 'HTMLText') {
                    $filter = [$fieldName.':PartialMatch' => 'iframe'];
                    $objects = $className::get()->filter($filter);
                    if($objects->count()) {
                        foreach($objects as $object) {
                            DB::alteration_message('-----------------------------');
                            DB::alteration_message('Checking: '.$object->getTitle());
                            DB::alteration_message('-----------------------------');
                            $isPublished = $object->isPublished();
                            $htmlOld = $object->$fieldName;
                            if($htmlOld) {
                                $htmlNew = $api->oldToNewHTML($htmlOld);
                                if($htmlNew) {
                                    echo 'FROM: ' . $htmlOld;
                                    DB::alteration_message('-----------------------------');
                                    echo 'FROM: ' . $htmlNew;
                                    DB::alteration_message('-----------------------------');
                                    $object->$fieldName = $htmlNew;
                                    $object->write();
                                    if($isPublished) {
                                        $object->publishRecursive();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}
