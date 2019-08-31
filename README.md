Shortcodes module for Yii2
==========================

Installation
------------

1.  Copy to the folder with modules and connect *autoload.php*

2.  Or use composer: add to the *require* section of the project
    `"quanzo/yii2-shortcode": "*"` or `composer require
    "quanzo/yii2-shortcode"`

3.  Add to configuration

```php
$config = [
    'bootstrap' => [
        'shortcode',
    ],
    'modules' => [
        'shortcode' => [
            'class' => '\x51\yii2\modules\shortcode\Module',
            'automatic' => false, // process content before output to browser
            'exclude' => [ // Routes in which the use of shortcodes is prohibited. Use * and ? mask
                'blocks/*',
            ],
            'shortcodes' => [
                'url' => function ($arParams, $content = '') { // use [url path="/site/index"]main page[/url] 
                    if (!empty($arParams['path'])) {
                        $arUrl = [
                            $arParams['path']
                        ];
                        foreach ($arParams as $name => $val) {
                            if ($name != 0 || $name != 'path') {
                                $arUrl[$name] = $val;
                            }
                        }
                        $href = Url::to($arUrl);
                        if ($content) {
                            return Html::a($content, $href);
                        } else {
                            return $href;
                        }
                    }
                    return '';
                },
            ]
        ],
    ]
];
```
