<?php

namespace Sunnysideup\FixVideosForSS4;

use DOMDocument;




class ReplaceVimeoAndYouTubeEmbedCode
{

    protected $prefixHTML = '<html><body>';

    protected $postfixHTML = '</body></html>';

    /**
     * returns null if there is no change!
     * @param  string       $oldHTML
     * @return string|null  returns null if there is no change!
     */
    public function oldToNewHTML(string $oldHTML) : ?string
    {
        $change = false;
        $dom = new DOMDocument();
        $dom->loadHTML(
            $this->prefixHTML.$oldHTML.$this->postfixHTML,
            LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD
        );
        foreach ($dom->getElementsByTagName('p') as $pTag) {
            foreach($pTag->getElementsByTagName('iframe') as $iframe ) {
                $url = $iframe->getAttribute('src');
                $youTubeVideoID =  $this->getYouTubeCodeFromURL($url);
                if($youTubeVideoID) {
                    $change = true;
                    $fragment = $dom->createDocumentFragment();
                    $newHTML = $this->newYouTubeCode($youTubeVideoID);
                    $fragment->appendXML($newHTML);
                    $pTag->parentNode->replaceChild($fragment, $pTag);
                } else {
                    $vimeoID =  $this->getVimeoCodeFromURL($url);
                    if($vimeoID) {
                        $change = true;
                        $fragment = $dom->createDocumentFragment();
                        $newHTML = $this->newVimeoCode($vimeoID);
                        $fragment->appendXML($newHTML);
                        $pTag->parentNode->replaceChild($fragment, $pTag);
                    }
                }
            }
        }
        if($change) {
            $html = $dom->saveXML();
            $html = $this->cleanHTML($html);

            return $html;
        } else {
            null;
        }
    }


    protected function newYouTubeCode($videoID)
    {
        return '<div thumbnail="https://i.ytimg.com/vi/'.$videoID.'/hqdefault.jpg" class="ss-htmleditorfield-file embed"><iframe src="https://www.youtube.com/embed/'.$videoID.'?feature=oembed" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe></div>';
    }


    protected function cleanHTML(string $html) : string
    {
        // $html = str_replace("\r", '', $html);
        // $html = str_replace("\n", '', $html);
        for($i = 0; $i < 3; $i++) {
            $html = str_replace($this->prefixHTML, '', $html);
            $html = str_replace($this->postfixHTML, '', $html);
            $html = str_replace('<?xml version="1.0" standalone="yes"?>', '', $html);
            //remove white space
            $html = preg_replace('/\s+/', ' ', $html);
            $html = str_replace('> <', '><', $html);
        }

        return $html;
    }

    protected function getYouTubeCodeFromURL($url) : string
    {
        $id = '';
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            $id = $match[1];
        }
        return $id;
    }


    /**
     * source: https://gist.github.com/anjan011/1fcecdc236594e6d700f
     * Get Vimeo video id from url
     *
     * Supported url formats -
     *
     * https://vimeo.com/11111111
     * http://vimeo.com/11111111
     * https://www.vimeo.com/11111111
     * http://www.vimeo.com/11111111
     * https://vimeo.com/channels/11111111
     * http://vimeo.com/channels/11111111
     * https://vimeo.com/groups/name/videos/11111111
     * http://vimeo.com/groups/name/videos/11111111
     * https://vimeo.com/album/2222222/video/11111111
     * http://vimeo.com/album/2222222/video/11111111
     * https://vimeo.com/11111111?param=test
     * http://vimeo.com/11111111?param=test
     *
     * @param string $url The URL
     *
     * @return string the video id extracted from url
     */
    protected function getVimeoCodeFromURL($url) : string
    {
        $regs = [];

        $id = '';

        if (preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs)) {
            $id = $regs[3];
        }

        return $id;

    }

}
