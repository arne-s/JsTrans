<?php

/**
 * JsTrans
 *
 * Use Yii translations in Javascript
 */
class JsTrans
{
    public $categories;
	public $languages;
	public $defaultLanguage = null;
	
	private $_assetsPath;
    private $_publishPath;
    private $_publishUrl;

    /**
     * Publish translations in JSON and append to the page
     *
     * @param mixed $categories the categories that are exported (accepts array and string)
     * @param mixed $languages the languages that are exported (accepts array and string)
     * @param string $defaultLanguage the default language used in translations
     */
    public function __construct($categories = null, $languages = null, $defaultLanguage = null) {
    	$this->categories = $categories;
    	$this->languages = $languages;
    	$this->defaultLanguage = $defaultLanguage;
    	
    	// if the class is being used the old way (not as a component)
    	if ($categories && $languages) {
    		$this->init();
    	}
    }
    
    public function init()
    {
        $assetManager = Yii::app()->assetManager;

        // set default language
        if (!$this->defaultLanguage) $this->defaultLanguage = Yii::app()->language;

        // create arrays from params
        if (!is_array($this->categories)) $this->categories = array($this->categories);
        if (!is_array($this->languages)) $this->languages = array($this->languages);

        // set paths
        $this->_assetsPath = dirname(__FILE__) . '/assets';
        $this->_publishPath = $assetManager->getPublishedPath($this->_assetsPath);
        $this->_publishUrl = $assetManager->getPublishedUrl($this->_assetsPath);

        // create hash
        $hash = substr(md5(implode($this->categories) . ':' . implode($this->languages) ), 0, 10);
        $dictionaryFile = "JsTrans.dictionary.{$hash}.js";

        // publish assets and generate dictionary file if neccessary
        if (!file_exists($this->_publishPath) || YII_DEBUG) {
            // publish and get new url and path
            $this->_publishUrl  = $assetManager->publish($this->_assetsPath, false, -1, true);
            $this->_publishPath = $assetManager->getPublishedPath($this->_assetsPath);

            // declare config (passed to JS)
            $config = array('language' => $this->defaultLanguage);
			
            // getting protected loadMessages method using Reflection to call it from outside 
            $messages = Yii::app()->messages;
            $loadMessages = new ReflectionMethod(get_class($messages), 'loadMessages');
			$loadMessages->setAccessible(true);
            
            // loop message files and store translations in array
            $dictionary = array();
            foreach ($this->languages as $lang) {
                if (!isset($dictionary[$lang])) $dictionary[$lang] = array();

                foreach ($this->categories as $cat) {
                    $dictionary[$lang][$cat] = $loadMessages->invoke($messages, $cat, $lang);
                }
            }

            // JSONify config/dictionary
            $data = 'Yii.translate.config=' . CJSON::encode($config) . ';'
                  . 'Yii.translate.dictionary=' . CJSON::encode($dictionary);

            // save to dictionary file
            if (!file_put_contents($this->_publishPath . '/' . $dictionaryFile, $data)) {
               Yii::log('Error: Could not write dictionary file', 'trace', 'jstrans');
               return null;
            }
        }

        // register scripts
        Yii::app()->clientScript->registerScriptFile(
            $this->_publishUrl . '/JsTrans.min.js', CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile(
            $this->_publishUrl . '/' . $dictionaryFile, CClientScript::POS_HEAD);
    }
}
