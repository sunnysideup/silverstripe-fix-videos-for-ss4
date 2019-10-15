# silverstripe-fix-videos-for-ss4
Fixes embedded videos in SS3 to the new format in SS4


I have a bunch of HTML snippets with various youtube links in it. I want to replace these as follows:

FROM:

```php
<p>
    <iframe foor="bar" src="https://www.youtube.com/embed/ANYCODEHERE" frameborder="0" foo2="bar2"></iframe>
</p>
```

TO:

```php
<div thumbnail="https://i.ytimg.com/vi/ANYCODEHERE/hqdefault.jpg" class="ss-htmleditorfield-file embed">
    <iframe src="https://www.youtube.com/embed/ANYCODEHERE?feature=oembed" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
</div>
```

There will be 100s of videos here so we need to extract the video code.

The class in the module is how I did it... 

To test it, I run: 


```php

$obj = new FixVideos();
$newHTML = $obj->run(
'
            <h1>test goes here...</h1>
            <p>test goes here...</p>
            <br />
            <p>
                <iframe foo="bar" src="https://www.youtube.com/embed/xoBk_ccx2L8" frameborder="0" foo2="bar2"></iframe>
            </p>
        ';
);
echo $newHTML;
```
