<?php
abstract class KlearMatrix_Model_Field_Picker_Abstract
{
    protected $_jqLocale;

    protected $_css = array();
    protected $_js = array();

    protected $_availableSettings = array(
        'altField' ,
        'altFormat' ,
        'appendText' ,
        'autoSize' ,
        'buttonImage' ,
        'buttonImageOnly' ,
        'buttonText' ,
        'calculateWeek' ,
        'changeMonth' ,
        'changeYear' ,
        'closeText' ,
        'constrainInput' ,
        'currentText' ,
        'dateFormat' ,
        'timeFormat' ,
        'dayNames' ,
        'dayNamesMin' ,
        'dayNamesShort' ,
        'defaultDate' ,
        'duration' ,
        'firstDay' ,
        'gotoCurrent' ,
        'hideIfNoPrevNext' ,
        'isRTL' ,
        'maxDate' ,
        'minDate' ,
        'monthNames' ,
        'monthNamesShort' ,
        'navigationAsDateFormat' ,
        'nextText' ,
        'numberOfMonths' ,
        'prevText' ,
        'selectOtherMonths' ,
        'shortYearCutoff' ,
        'showAnim' ,
        'showButtonPanel' ,
        'showCurrentAtPos' ,
        'showMonthAfterYear' ,
        'showOn' ,
        'showOptions' ,
        'showOtherMonths' ,
        'showWeek' ,
        'stepMonths' ,
        'weekHeader' ,
        'yearRange' ,
        'yearSuffix'
    );

    protected $_dateFormats = array(
        'sql' => 'yy-mm-dd',
        'af' => 'dd/mm/yy',
        'ar' => 'dd/mm/yy',
        'ar-DZ' => 'dd/mm/yy',
        'az' => 'dd.mm.yy',
        'bg' => 'dd.mm.yy',
        'bs' => 'dd.mm.yy',
        'ca' => 'dd/mm/yy',
        'cs' => 'dd.mm.yy',
        'cy-GB' => 'dd/mm/yy',
        'da' => 'dd-mm-yy',
        'de' => 'dd.mm.yy',
        'el' => 'dd/mm/yy',
        'en' => 'yy/mm/dd',
        'en-AU' => 'dd/mm/yy',
        'en-GB' => 'dd/mm/yy',
        'en-NZ' => 'dd/mm/yy',
        'eo' => 'dd/mm/yy',
        'es' => 'dd/mm/yy',
        'et' => 'dd.mm.yy',
        'eu' => 'yy/mm/dd',
        'fa' => 'yy/mm/dd',
        'fi' => 'dd.mm.yy',
        'fo' => 'dd-mm-yy',
        'fr' => 'dd/mm/yy',
        'fr-CH' => 'dd.mm.yy',
        'ge' => 'dd-mm-yy',
        'gl' => 'dd/mm/yy',
        'he' => 'dd/mm/yy',
        'hi' => 'mm/dd/yy',
        'hr' => 'dd.mm.yy.',
        'hu' => 'yy.mm.dd.',
        'hy' => 'dd.mm.yy',
        'id' => 'dd/mm/yy',
        'is' => 'dd/mm/yy',
        'it' => 'dd/mm/yy',
        'ja' => 'yy/mm/dd',
        'kk' => 'dd.mm.yy',
        'km' => 'dd-mm-yy',
        'ko' => 'yy-mm-dd',
        'lb' => 'dd.mm.yy',
        'lt' => 'yy-mm-dd',
        'lv' => 'dd-mm-yy',
        'mk' => 'dd.mm.yy',
        'ml' => 'dd/mm/yy',
        'ms' => 'dd/mm/yy',
        'nl' => 'dd-mm-yy',
        'nl-BE' => 'dd/mm/yy',
        'no' => 'dd.mm.yy',
        'pl' => 'dd.mm.yy',
        'pt' => 'dd/mm/yy',
        'pt-BR' => 'dd/mm/yy',
        'rm' => 'dd/mm/yy',
        'ro' => 'dd.mm.yy',
        'ru' => 'dd.mm.yy',
        'sk' => 'dd.mm.yy',
        'sl' => 'dd.mm.yy',
        'sq' => 'dd.mm.yy',
        'sr' => 'dd/mm/yy',
        'sr-SR' => 'dd/mm/yy',
        'sv' => 'yy-mm-dd',
        'ta' => 'dd/mm/yy',
        'th' => 'dd/mm/yy',
        'tj' => 'dd.mm.yy',
        'tr' => 'dd.mm.yy',
        'uk' => 'dd/mm/yy',
        'vi' => 'dd/mm/yy',
        'zh-CN' => 'yy-mm-dd',
        'zh-HK' => 'dd-mm-yy',
        'zh-TW' => 'yy/mm/dd',
    );

    protected $_timeFormat = 'hh:mm:ss';

    protected $_settings = array();
    protected $_plugin;

    public function __construct($config)
    {
        $currentKlearLanguage = Zend_Registry::get('currentSystemLanguage');
        $this->_jqLocale = $currentKlearLanguage->getjQLocale();

        if ($this->_jqLocale === false || !array_key_exists($this->_jqLocale, $this->_dateFormats)) {
            throw new \Exception('Klear locale not available in current picker');
        }

        $this->_init(); //Only include timepicker if need it and showSecond parameter with true value
        $this->_setConfig($config); //Set config in yaml field
        $this->_setPlugin();
    }

    protected function _setConfig($config)
    {
        if ($config->settings) {
            foreach ($config->settings as $key => $value) {
                $this->_setSetting($key, $value);
            }
        }

        return $this;
    }

    abstract protected function _setPlugin();

    protected function _init() {}

    protected function _setSetting($key, $value)
    {
        if (in_array($key, $this->_availableSettings)) {
            $this->_settings[$key] = $value;
        }
        return $this;
    }

    protected function _getSetting($key)
    {
        if (array_key_exists($key, $this->_settings)) {
            return $this->_settings[$key];
        }
        return null;
    }

    public function getConfig()
    {
        $config = array(
            'settings' => $this->_settings,
            'plugin' => $this->_plugin
        );
        return $config;
    }

    /**
     * Returns date format to set date in Zend_Date
     * @return string
     */
    protected function _getZendDateFormat()
    {
        $dateFormat = $this->_getSetting('dateFormat');
        $dateFormat = str_replace(array('mm', 'yy'), array('MM', 'yyyy'), $dateFormat);
        return $dateFormat;
    }

    /**
     * Returns time format to set time in Zend_Date
     * @return string
     */
    protected function _getZendTimeFormat()
    {
        return str_replace('hh', 'HH', $this->_getSetting('timeFormat'));
    }

    /**
     * If not exists dateFormat setting, set with jqLocale format
     */
    protected function _setPickerDateFormat()
    {
        if ($this->_getSetting('dateFormat') === null) {
            $this->_setSetting('dateFormat', $this->_dateFormats['sql']);
        }
    }

    /**
     * If not exists timeFormat setting, set with default timeFormat
     * If timeFormat do not have seconds, set showSecond to false
     */
    protected function _setPickerTimeFormat()
    {
        if ($this->_getSetting('timeFormat') === null) {
            $this->_setSetting('timeFormat', $this->_timeFormat);
        }

        if ($this->_getSetting('showSecond') === false) {
            $timeFormat = preg_replace("/\:ss/", '', $this->_getSetting('timeFormat'));
            $this->_setSetting('timeFormat', $timeFormat);
        }

        if (!preg_match("/\:ss/", $this->_getSetting('timeFormat'))) {
            $this->_setSetting('showSecond', false);
        }
    }

    protected function _includeTimepicker()
    {
        $this->_css[] = "/js/plugins/datetimepicker/jquery-ui-timepicker-addon.css";
        $this->_js[] = "/js/plugins/datetimepicker/jquery-ui-timepicker-addon.js";
        $this->_js[] = "/js/plugins/datetimepicker/localization/jquery-ui-timepicker-" . $this->_jqLocale . ".js";

        $this->_availableSettings[] = 'showSecond';
        $this->_setSetting('showSecond', true);
    }

    public function getExtraJavascript()
    {
        return $this->_js;
    }

    public function getExtraCss()
    {
        return $this->_css;
    }
}
