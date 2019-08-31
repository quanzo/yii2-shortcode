<?php

namespace x51\yii2\modules\shortcode;
use Yii;

class Module extends \yii\base\Module
{
    public $exclude; // роуты в которых запрещено использование шорткодов. Можно применять символы ? и *
    public $automatic = true; // обрабатывать весь контент перед выводом результата
	public $shortcodes = []; // дополнительные шорткоды 

    public function init()
    {
        parent::init();
        $response = Yii::$app->response;
        if ($this->automatic) {
            $response->on($response::EVENT_AFTER_PREPARE, [$this, 'onShortcode']);
        }
		if (!empty($this->shortcodes)) {
			if (is_callable($this->shortcodes)) {
				$f = $this->shortcodes;
				$arSC = $f();
			} elseif (is_array($this->shortcodes)) {
				$arSC = & $this->shortcodes;
			}
			if (!empty($arSC) && is_array($arSC)) {
				$shortcodeProcess = \x51\classes\shortcode\Shortcode::getInstance();
				foreach ($arSC as $tag => $func) {
					$shortcodeProcess->add($tag, $func);
				}
			}
		}
    }

    public function onShortcode($event)
    {
        $response = $event->sender;
        if ($response->stream === null) { // отдача контента, а не файла
            $excluded = false;
            if (!empty($this->exclude)) {
                $currRoute = Yii::$app->controller->route;
                if (!is_array($this->exclude)) {
                    $arExclude = [$exclude];
                } else {
                    $arExclude = &$this->exclude;
                }

                foreach ($arExclude as $exPath) {
                    if (is_string($exPath)) {
                        $excluded = fnmatch($exPath, $currRoute);
                    } elseif (is_callable($exPath)) {
                        $excluded = $exPath($currRoute);
                    }
                    if ($excluded) {
                        break;
                    }
                }
            }
            if (!$excluded) {
                $response->content = $this->process($response->content);                
            }
        }
    } // end onShortcode

    public function process($content)
    {
        $stop = false;
        $shortcodeProcess = \x51\classes\shortcode\Shortcode::getInstance();
        do {
            $res_content = $shortcodeProcess->process($content, true);
            if ($content == $res_content) { // break
                $stop = true;
            } else {
                $content = $res_content;
            }
        } while (!$stop);
        return $content;
    } // end process

} // end class
