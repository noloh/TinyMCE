<?php
//Path to NOLOH Kernel, change to your path
require_once("/var/www/htdocs/Stable/NOLOH/NOLOH.php");
System::IncludePaths('../');

class TinyMCETest2 extends WebPage
{
	function __construct()
	{
		parent::WebPage('Basic TinyMCE Example 2');
		$this->Controls->Add($editor = new TinyMCE('Testing'));
		$this->Controls->Add($button = new Button('Click Me', $editor->Right + 10))
			->Click = new ServerEvent($this, 'SomeFunc', $editor);
		//Toolbar and Theme Modifications
		$editor->Theme = TinyMCE::Advanced;
		$editor->Skin = TinyMCE::O2K7Black;
		$editor->Toolbar->Orientation = 'top';
		$editor->Toolbar->Alignment = 'left';
		//Add Strips
		$editor->Strips->Add(array(
			TinyMCE::Bold, TinyMCE::Underline, TinyMCE::Strike, TinyMCE::Seperator,
			TinyMCE::Font, TinyMCE::FontSize, TinyMCE::Styles, TinyMCE::Seperator));
				//Add Strips
		$editor->Strips->Add(array(
			TinyMCE::Undo, TinyMCE::Redo, TinyMCE::Search, TinyMCE::Replace, TinyMCE::Seperator,
			TinyMCE::CharMap, TinyMCE::Cut, TinyMCE::Copy, TinyMCE::Paste));
	}
	function SomeFunc($obj)
	{
		System::Log($obj->Text);
	}
}