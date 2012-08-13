<?php
/**
 * JsTrans
 *
 * Use Yii translations in Javascript
 *
 */


/**
 * Publish translations in JSON and append to the page
 *
 * @param message the message to be translated
 * @param params array of parameters (number, placeholders)
 * @param dictionary instance of dictionary
 * @return translated string
 */
class JsTrans
{
    public function __construct($categories, $languages, $defaultLanguage = null)
    {
        // set default language
        if (!$defaultLanguage) $defaultLanguage = Yii::app()->language;

        // create arrays from params
        if (!is_array($categories)) $categories = array($categories);
        if (!is_array($languages)) $languages = array($languages);

        // publish assets folder
        $assetUrl = Yii::app()->assetManager->publish(dirname(__FILE__) . '/assets', false, -1, true);

        // create hash
        $hash = substr(md5(implode($categories) . ':' . implode($languages)), 0, 10);

        $dictionaryFile = "$assetUrl/dictionary-$hash.js";

        // generate dictionary file if not exists
        if (!file_exists($dictionaryFile)) {

            // declare config (passed to JS)
            $config = array('language' => $defaultLanguage);

            // base folder for message translations
            $messagesFolder = rtrim(Yii::app()->coreMessages->basePath, '\/');

            // loop message files and store translations in array
            $dictionary = array();
            foreach ($languages as $lang) {
                if (!isset($dictionary[$lang])) $dictionary[$lang] = array();

                foreach ($categories as $cat) {
                    if (!isset($dictionary[$lang][$cat])) $dictionary[$lang][$cat] = array();

                    $messagefile = $messagesFolder . '/' . $lang . '/' . $cat . '.php';
                    if (file_exists($messagefile)) $dictionary[$lang][$cat] = require_once($messagefile);
                }
            }

            // save config/dictionary
            $data = "Yii.translate.config=" . CJSON::encode($config) . ";Yii.translate.dictionary=" . CJSON::encode($dictionary);
            file_put_contents(Yii::getPathOfAlias('webroot') . $dictionaryFile, $data);
        }

        // publish library and dictionary
        if (file_exists(Yii::getPathOfAlias('webroot') . $dictionaryFile)) {
            Yii::app()->getClientScript()
                    ->registerScriptFile($assetUrl . '/JsTrans.min.js', CClientScript::POS_HEAD)
                    ->registerScriptFile($dictionaryFile, CClientScript::POS_HEAD);
        } else {
            Yii::log('Error: Could not publish dictionary file, check file permissions', 'trace', 'jstrans');
        }
    }
}
