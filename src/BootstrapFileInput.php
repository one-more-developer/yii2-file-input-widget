<?php
/**
 * @link https://github.com/2amigos/yii2-file-input-widget
 * @copyright Copyright (c) 2013-2015 2amigOS! Consulting Group LLC
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace valiant\fileinput;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 * BootstrapFileInput widget renders the improved and amazing plugin version from Krajee. It supports multiple file
 * preview with both images and/or text types.
 *
 * @author Alexander Kochetov <creocoder@gmail.com>
 */
class BootstrapFileInput extends InputWidget
{
    /**
     * @var array the options for the Bootstrap File Input plugin. Default options have exporting enabled.
     * Please refer to the Bootstrap File Input plugin Web page for possible options.
     * @see http://plugins.krajee.com/file-input#options
     */
    public $clientOptions = [];
    /**
     * @var array the event handlers for the underlying Jasny file input JS plugin.
     * Please refer to the [Bootstrap File Input](http://plugins.krajee.com/file-input#events) plugin
     * Web page for possible events.
     */
    public $clientEvents = [];


    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeFileInput($this->model, $this->attribute, $this->options);
        } else {
            echo Html::fileInput($this->name, $this->value, $this->options);
        }
        $this->registerClientScript();
    }

    /**
     * Registers Bootstrap File Input plugin
     */
    public function registerClientScript()
    {
        $view = $this->getView();

        BootstrapFileInputAsset::register($view);

        $id = $this->options['id'];

        $options = !empty($this->clientOptions) ? $this->prepareClientOptions() : '';

        $js = [
            'jQuery(\'#' . $id . '\').fileinput(' . $options . ');'
        ];

        if (!empty($this->clientEvents)) {
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = 'jQuery(\'#' . $id . '\').on(\'' . $event . '\', ' . $handler . ');';
            }
        }
        $view->registerJs(implode(PHP_EOL, $js));
    }

    /**
     * Prepare client option.
     *
     * @return string
     */
    protected function prepareClientOptions()
    {
        if (is_array($this->clientOptions)) {
            $result = [];
            foreach ($this->clientOptions as $optionName => $optionValue) {
                if ($optionValue instanceof \Closure) {
                    $result[] = sprintf('%s: %s', $optionName, $optionValue());
                } else {
                    $result[] = sprintf('%s: %s', $optionName, Json::encode($optionValue));
                }
            }
            $result = implode(', ', $result);
        } else {
            $result = Json::encode($this->clientOptions);
        }

        return sprintf('{%s}', $result);
    }
}
