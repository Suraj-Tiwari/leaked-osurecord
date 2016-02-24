<?php
  
  
	$mem = new Memcache();
	$mem->connect("localhost");
	$mem->set("recording", false);