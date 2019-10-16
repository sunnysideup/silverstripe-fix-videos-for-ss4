# silverstripe Fix Videos for SS4
Fixes embedded videos in SS3 to the new format in SS4

Replaces: 


```php
<p>
    <iframe foor="bar" src="https://www.youtube.com/embed/ANYCODEHERE" frameborder="0" foo2="bar2"></iframe>
</p>
```

With:

```php
<div thumbnail="https://i.ytimg.com/vi/ANYCODEHERE/hqdefault.jpg" class="ss-htmleditorfield-file embed">
    <iframe src="https://www.youtube.com/embed/ANYCODEHERE?feature=oembed" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
</div>
```

And similar for Vimeo
