<?php

require dirname(dirname(__FILE__)).'/vendor/autoload.php';
require dirname(__FILE__).'/Category.php';
require dirname(__FILE__).'/Event.php';
require dirname(__FILE__).'/Bar.php';

Event::register();
Category::register();
