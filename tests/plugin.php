<?php
require dirname( dirname( __FILE__ ) ) . '/vendor/autoload.php';
require dirname( __FILE__ ) . '/Category.php';
require dirname( __FILE__ ) . '/Event.php';

Event::register();
Category::register();
