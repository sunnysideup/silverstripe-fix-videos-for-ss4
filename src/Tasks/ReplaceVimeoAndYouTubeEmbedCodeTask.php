<?php

namespace Sunnysideup\FixVideosForSS4\Tasks;

use DOMDocument;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\DataObject;

use Sunnysideup\FixVideosForSS4\Api\ReplaceVimeoAndYouTubeEmbedCode;



class ReplaceVimeoAndYouTubeEmbedCodeTask extends BuildTask
{

    protected $forReal = false;

    protected $title = 'Fix legacy YouTube and Vimeo';

    protected $description = 'Runs through all the HTMLText fields.';

    public function run($request)
    {
        $api = new ReplaceVimeoAndYouTubeEmbedCode();
        $objectsChanged = [];
        $objectsFieldsChanged = [];
        $completed = [];
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
                            $objectKey = $object->ClassName.'-'.$object->ID;
                            $objectFieldKey = $objectKey.'-'.$fieldName;
                            if(isset($completed[$objectFieldKey])) {
                                continue;
                            }
                            DB::alteration_message('-----------------------------');
                            DB::alteration_message('Checking: '.$object->getTitle().' ('.$objectKey.')');
                            DB::alteration_message('-----------------------------');
                            $isPublished = $object->isPublished();
                            $htmlOld = $object->$fieldName;
                            if($htmlOld) {
                                $htmlNew = $api->oldToNewHTML($htmlOld);
                                if($htmlNew) {
                                    echo 'FROM:
                                    ' . $htmlOld;
                                    DB::alteration_message('-----------------------------');
                                    echo 'TO:
                                    ' . $htmlNew;
                                    DB::alteration_message('-----------------------------');
                                    $object->$fieldName = $htmlNew;
                                    if($this->forReal) {
                                        $object->write();
                                    }
                                    if(!isset($classesChanged[$className])) {
                                        $classesChanged[$className] = 0;
                                    }
                                    $classesChanged[$className]++;
                                    $objectsChanged[$objectKey] = $objectKey;
                                    $objectsFieldsChanged[$objectFieldKey] = $objectFieldKey;
                                    if($isPublished) {
                                        if($this->forReal) {
                                            $object->publishRecursive();
                                        }
                                    }
                                }
                            }
                            $completed[$objectFieldKey] = true;
                        }
                    }
                }
            }
        }
        DB::alteration_message('-----------------------------');
        DB::alteration_message('-----------------------------');
        DB::alteration_message('-----------------------------');
        DB::alteration_message('Classes changed '.print_r($classesChanged, 1));
        DB::alteration_message('Number of objects changed '.count($objectsChanged));
        DB::alteration_message('Number of objects fields changed '.count($objectsFieldsChanged));
        DB::alteration_message('-----------------------------');
    }

}
