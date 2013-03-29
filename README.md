JsTrans
=============

JsTrans is a Yii extension aiming to port the native Yii translations module to javascript. Language files can be published
and used within javascript in almost the same way as Yii does server side.
Currently supported are placeholders and plural forms (including expressions).


Installation
-----------
* Download/clone JsTrans in extension folder (folder should be 'JsTrans') 
* Import the extension in main config: ('ext.JsTrans.*')


Usage
-----------
We need to publish the translations to javascript for them to work. You can publish all your translations in a single
location, or publish only parts in specific places. (e.g. controller or view)

     new JsTrans('app','nl');

The first parameter is the category, the second the language. Both accept single items as string or multiple as array.
There is an optional third parameter to specify the default language. If nothing is passed, it will use the App default language.

If everything went well, the translations are published as a javascript file containing all the translations in JSON format.
A hash is included in the filename, to distinguish between different languages/categories. By default the file is cached, you
may need to empty the assets folder in order to force a refresh.

At this point, translations can be used in javascript:

    Yii.t('app','My non-translated text');

Make sure there is a translation available to see the results.


Examples
-----------
(for demo purposes the default language is English, it could be any language)

Simple translation:

    Yii.t('app','Hello world');  // Hello World

Simple translation with custom language:

    Yii.t('app','Hello world','','fr');  // Bonjour tout le monde
    Yii.t('app','Hello world','','de');  // Hallo Welt

Placeholder:

    Yii.t('app','Hello {name}',{name:'Michael'}); // Hello Michael

Multiple placeholders:

    Yii.t('app','Hello {firstname} {lastname}', {firstname:'Michael', lastname:'Jackson'}); // Hello Michael Jackson

Plural forms:

    Yii.t('app','Apple|Apples',0); // Apples
    Yii.t('app','Apple|Apples',1); // Apple
    Yii.t('app','Apple|Apples',2); // Apples

Plural forms with placeholders:

    Yii.t('app','{n} Apple|{n} Apples',0); // 0 Apples
    Yii.t('app','{n} Apple|{n} Apples',1); // 1 Apple
    Yii.t('app','{n} Apple|{n} Apples',2); // 2 Apples

Plural forms with expressions:

    Yii.t('app','0#No comments, be the first!|1#One comment|n>1#{n} comments',0); // No comments, be the first!
    Yii.t('app','0#No comments, be the first!|1#One comment|n>1#{n} comments',1); // One comment
    Yii.t('app','0#No comments, be the first!|1#One comment|n>1#{n} comments',2); // 2 comments


Plural forms with expressions and placeholders:

    Yii.t('app','0#{name} has no mail|1#{name} has one mail|n>1#{name} has {n} mails',{n:0, name:'Pete'}); // Pete has no mail
    Yii.t('app','0#{name} has no mail|1#{name} has one mail|n>1#{name} has {n} mails',{n:1, name:'Pete'}); // Pete has one mail
    Yii.t('app','0#{name} has no mail|1#{name} has one mail|n>1#{name} has {n} mails',{n:2, name:'Pete'}); // Pete has 2 mails


(optional) Generate translations using Yiic
-----------

Yii can be configured to automatically scan javascript files for translations, for more info see
http://www.yiiframework.com/doc/api/1.1/CPhpMessageSource 

An example configuration could look like this:

    return array(
        'sourcePath' => dirname(__FILE__) . '/../..',
        'messagePath' => dirname(__FILE__) . '/../messages',
        'translator' => 'Yii.t',
        'languages' => array('nl','de'),
        'fileTypes' => array('js'),
        'overwrite' => true,
        'exclude' => array(
            '.git',
            '.svn',
            '/framework',
            '/protected',
        ),
    );

Best practise is to keep your javascript translations in separate categories, otherwise they might override each other.
This is a known limitation of the framework.

Resources
-----------
* extension page: http://www.yiiframework.com/extension/jstrans/
* github project: https://github.com/Arne-S/JsTrans

