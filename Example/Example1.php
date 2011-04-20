<?php
//Path to NOLOH Kernel, change to your path
require_once("/var/www/htdocs/Stable/NOLOH/NOLOH.php");
System::IncludePaths('../');

class TinyMCETest extends WebPage
{
	function __construct()
	{
		parent::WebPage('Basic TinyMCE Example 1');
		$this->Controls->Add($editor = new TinyMCE('Testing'));
		$this->Controls->Add($button = new Button('Click Me', $editor->Right + 10))
			->Click = new ServerEvent($this, 'SomeFunc', $editor);
	}
	function SomeFunc($obj)
	{
		System::Log($obj->Text);
	}
}